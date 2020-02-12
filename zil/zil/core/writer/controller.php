<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\TextProcessor;
use zil\core\scrapper\Info;

use zil\blueprint\BluePrint;

use zil\factory\Filehandler;

/**
 * Creates controller class and file in application
 */
    class Controller  implements Writer
    {

        /**
         * Constructor
         */
        public function __construct(){}

        /**
         * create the controller class
         *
         * @param Info $Info
         * @param string|null $controller
         * @param string|null $type
         * @return void
         */
        public function create(Info $Info, ?string $controller = 'Home', ?string $type = null){

            try{

                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                if( !empty($app_base) ){

                    /*  Controller Buffering */

                    if(!file_exists("{$app_base}controller/{$controller}.php")) {

                        $type = $type != 'api' ? null : 'api';

                        /**
                         * For App controller
                         * @var [type]
                         */
                        $blueprint = new BluePrint($app_name);
                        $Base = $blueprint->controller($controller);

                        $Info->getprogressMessage("createController");

                        (new Filehandler())->createFile("{$app_base}/controller/$type/{$Base['filename']}", $Base['code']);

                        print("{$controller} created\n");

                        /**
                         * Autoload classes 
                         */

                        print( shell_exec($Info->getCommand("autoload")) )."\n";


                    }else{
                        throw new \Exception("Error: Couldn't create {$controller} controller, {$controller} exists\n");
                    }
                }

            }catch(\Exception $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{

                if(empty($type))
                    print "Web controller creation closed\n";
                else
                    print "{$type} controller creation closed\n";
            }
        }

        /**
         * Create a controller and a view
         *
         * @param Info $Info
         * @param string $controller
         * @param string|null $view
         * @return void
         */
        public function createEx(Info $Info, string $controller, ?string $view = "Index", bool $updateController = true){


            try{

                /**
                 * Details Gathering
                 */
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }


                if( !is_null($app_name) ){

                    if( !empty($controller) )
                        $this->create($Info, $controller);

                    if( !empty($view) )
                        (new View())->create($Info, $view, $controller, $updateController);

                    
                    /**
                     * Autoload classes
                     */
                    print( shell_exec($Info->getCommand("autoload")) )."\n";

                }else{
                    return false;
                }

            }catch(\Exception $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }

        }

        /**
         * Destroy a controller
         *
         * @param Info $Info
         * @param string $controllerName
         * @return void
         */
        public function destroy(Info $Info, string $controllerName){
            try{


                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                print "destroying {$controllerName}\n";

                @unlink("{$Info->getAppDir()}src/{$app_name}/controller/{$controllerName}.php");
                (new Filehandler())->removeDir("{$Info->getAppDir()}src/{$app_name}/view/{$controllerName}/");

                /**
                 * Autoload classes
                 */
                print( shell_exec($Info->getCommand("autoload")) )."\n";

            }catch(\Exception $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                print "destruction closed\n";
            }
        }
    }
?>
