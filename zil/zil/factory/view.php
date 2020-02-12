<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\factory;

use zil\core\config\Config;
use zil\core\exception\UnexpectedTemplateException;
use zil\core\tracer\ErrorTracer;
use zil\core\middleware\TemplateEngine;
use zil\core\middleware\Csrf;

class View extends Config{

    private static $data = [];
    private static $errors = [];
    private static $notifications = [];



    public function __construct(){}

    /**
     * @param string $view
     * @param array|null $_DATA
     * @return string
     */
    public static function viewProcessor(string $view) : string{

        try{

            /**
             * Template Engine, Extract sections from template and fix it up using its builds
             */

            /**
             * Reset Errors
             */
            self::$data = [];
            self::$errors = Session::getEncoded( '0xc4_form_errors' );
            (new Session())->deleteEncoded( '0xc4_form_errors' );

            /**
             * Reset Notifications
             */
            self::$notifications = [];
            self::$notifications = Session::getEncoded( '0xc4_page_notifications' );
            (new Session())->deleteEncoded( '0xc4_page_notifications' );


            /**
             * Normalize view
             */
            $view = str_replace('.php', '', $view).'.php';


            $parent = new parent;

            if (file_exists("{$parent->curAppPath}{$parent->viewPath}{$view}") === true) {


                /**
                 * Start Buffering
                 */
                ob_start();


                /**
                 * Clean up existing views
                 * */
                ob_clean();

                /**
                 * Trying to extract the template used
                 */


                $template = null;
                $content = file_get_contents("{$parent->curAppPath}{$parent->viewPath}{$view}");

                /**
                 * Save main context to array
                 */
                $lookupcontent = explode("\n", $content);

                foreach(explode("\n", $content) as $parts){

                    if(preg_match( '/\@(extend)\((\'|\")*([\w]+)(\'|\")*\)/i', $parts, $match ) > 0){
                        $template = isset($match[3]) ? str_replace('.php', '', $match[3]).'.php' : null;
                        break;
                    }
                }

                if(!file_exists("{$parent->curAppPath}{$parent->templatePath}{$template}"))
                    throw  new UnexpectedTemplateException("Couldn't found {$template} in {$parent->curAppPath}{$parent->templatePath}");


                /**
                 * Initialize sections in template
                 */
                $sections = [];

                /**
                 * Load template if exist
                 */
                if(!is_null($template) && file_exists("{$parent->curAppPath}{$parent->templatePath}{$template}")){


                    $content = file_get_contents("{$parent->curAppPath}{$parent->templatePath}{$template}");

                    /**
                     * Template context overwrites main context
                     * Having saved main context in lookup Array
                     */


                    preg_match_all( '/\@(section)\((\'|\")*([\w]+)(\'|\")*\)/i', $content, $match );
                    foreach($match[3] as $section){
                        if(!empty($section))
                            array_push($sections, $section);
                    }
                }


                /**
                 * Extract views to sections from build panel
                 * Replace sections placeholder with corresponding build panel
                 */

                foreach($sections as $section){

                    $build_found = false;
                    $build_context = null;

                    foreach($lookupcontent as $index => $original_line){

                        $line = trim($original_line);
                        if(empty($line))
                            continue;

                        if(!$build_found){
                            if(preg_match("/@build\((\'|\")*$section(\'|\")*\)/i", $line)){
                                $build_found = true;
                            }else{
                                continue;
                            }
                        }else{
                            if(!preg_match("/@endbuild/i", $line)){
                                unset($lookupcontent[$index]);
                                $build_context .= $original_line."\n";
                                continue;
                            }else{
                                $build_found = false;
                                break;
                            }
                        }
                    }

                    $content = preg_replace(
                        ["/@section\((\'|\")*$section(\'|\")*\)/i"],
                        [$build_context],
                        $content
                    );
                }


                /**
                 * Unset
                 */
                if(sizeof($sections) > 0)
                    unset($lookupcontent, $build_context, $build_found_found, $sections);


                /**
                * Parse and Convert all template lieterals
                 */

                return (new TemplateEngine())->mutate($content);

            } else {

                throw new \Exception("View file( <b>{$parent->curAppPath}{$parent->viewPath}{$view}</b> ) not found ");

            }

        }catch(\ParseError $t){

            new ErrorTracer($t);
        }catch(\TypeError $t){

            new ErrorTracer($t);
        }catch(\RuntimeException $t){

            new ErrorTracer($t);
        }catch(\LengthException $t){
            new ErrorTracer($t);
        }catch(\InvalidArgumentException $t){
            new ErrorTracer($t);
        }catch(UnexpectedTemplateException $t){
            new ErrorTracer($t);
        }catch(\Throwable $t){
            new ErrorTracer($t);
        }

    }

    public static function render(string $view, ?array $_DATA = []){
        try{


            /**
             * Process Template, data and view
             */
            $content = self::viewProcessor($view);


            $parent = new parent;


            #Set Page data

            if(is_array($_DATA))
                self::$data = array_merge($_DATA, ['ROUTE_BASE'=> $parent->requestBase]);

            /**
             *
             * Load helpers
             */

            include_once($parent->curSysPath."/core/facades/helpers/util.php");

            /**
             * Inject CSRF Field
             */
            if(preg_match('/<!--ZDX_BINDING_CSRF_STRICTLY_FOR_ENCRYPTION-->/', $content)){

                output_add_rewrite_var("CSRF_FLAG", Csrf::generateToken());

            }

            /**
             * Generate Anonymous View the script
             * */

            $tmpfile = self::generateAnonymousViewConveyor($content);

            /** Execute  */
            include_once ($tmpfile);

        }catch(\ParseError $t){

            new ErrorTracer($t);
        }catch(\TypeError $t){

            new ErrorTracer($t);
        }catch(\RuntimeException $t){

            new ErrorTracer($t);
        }catch(\Error $t){

            var_dump($t->getMessage());
            new ErrorTracer($t);

        }catch(\Exception $t){

            new ErrorTracer($t);
        }finally{
            if(is_readable($tmpfile))
                unlink($tmpfile);
        }


    }

    /**
     * Publish raw code to screen
     */
    public static function raw(string $view, string $media = 'SCREEN'){

        try{

            /**
             * Process Template, data and view
             */
            $content = self::viewProcessor($view);

            if($media === 'SCREEN') {

                /**
                 * Generate Anonymous View the script
                 * */

                $tmpfile = self::generateAnonymousViewConveyor($content);

                show_source($tmpfile, false);

            }else if($media === 'FILE') {

                Logger::Init();
                Logger::Log($content);
                Logger::kill();

            }else {

                Logger::Init();
                Logger::Log($content);
                Logger::kill();

            }

        }catch(\Throwable $t){
            new ErrorTracer($t);
        }finally{
            if(is_readable($tmpfile))
                unlink($tmpfile);
        }
    }

    /**
     * Log code to file
     */
    public static function log(string $view, ?array $_DATA = []){

        try{

            /**
             * Process Template, data and view
             */
            $content = self::viewProcessor($view);


            Logger::Init();
            Logger::Log($content);
            Logger::kill();

        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
     * Data sent from view conveyor to view
     *
     * @param string|null $key
     * @return object|array
     */
    public static function data(?string $key = null) {

        try{

            if(!is_null($key)){
                if(array_key_exists($key, self::$data))
                    return self::$data[$key];
                else
                    throw new \Exception("Data for $key not found");
            }else{
                return self::$data;
            }
        }catch(\DomainException $t){
            new ErrorTracer($t);
        }catch(\ParseError $t){

            new ErrorTracer($t);
        }catch(\TypeError $t){

            new ErrorTracer($t);
        }catch(\CompileError $t){
            new ErrorTracer($t);
        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
     * Errors due from form validations
     *
     * @return array|null
     */
    public static function errors() : ?array{

        return self::$errors;
    }


    /**
     * Flash messages
     *
     * @return array|null
     */
    public static function notifications() : ?array {
        return self::$notifications;
    }

    /**
     * Output Parsed Content to browser
     *
     * @param string $content
     * @return string
     */
    private static function generateAnonymousViewConveyor(string $content) : string{


        try{

            $tmpfile = @tempnam('./tmp', '0xzil');
            file_put_contents($tmpfile, $content);

            return $tmpfile;


        }catch(\Throwable $t){

            new ErrorTracer($t);
        }
    }

}

?>
