<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\scrapper\Info;
use zil\blueprint\BluePrint;
use zil\factory\Filehandler;

    class Component implements Writer
    {

        /**
         * Constructor
         */
        public function __construct(){}

        /**
         * Create View without conveyor
         *
         * @param Info $Info
         * @param string|null $name
         * @param string|null $controller
         * @param boolean $first
         * @return void
         */
        public function create(Info $Info, ?string $name = "Index", ?string $controller = 'Home'){

            try{

                /**
                 * Normalize controller name
                 */
                $controller = ucfirst(rtrim($controller, '/'));
                $name = ucfirst(rtrim($name, '/'));

                /**
                 * Details Gathering
                 */
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories\n");
                }

                if( !empty($app_base) ){

                    /* View Buffering */

                    if( empty($controller) ){
                        throw new \Exception("Error: Empty controller identifier\n");
                    }

                    /**
                     * Component BluePrint
                     */

                    if( !is_null($controller) && file_exists("{$app_base}controller/{$controller}.php") ){

                        /**
                         * Set Progress Message
                         */
                        $Info->getProgressMessage("createComponent");

                        print "updating {$controller} controller...\n";

                        $filehandler = new Filehandler;

                        $context = null;

                        $buffering_pont = "{$app_base}controller/{$controller}.php";
                        $context_handle = fopen("{$buffering_pont}", 'rb');

                        /**
                         * Prepare new Component
                         */
                        $component = "\t\t\tpublic function {$name}(Param \$param){\n\t\t\t\ttry{}catch(\Throwable \$t){}\n\t\t\t}\n\n";

                        $first = true;
                        $halt_writing = false;

                        while ($blueprint = fgets($context_handle) ) {

                            /**
                             * Find first function in the controller class and insert conveyor before first
                             */
                            $pattern = "/^(public|private|protected)?[\s]*function[\s]+(.)+(\{)?$/i";

                            if ( preg_match($pattern, trim($blueprint), $match) != 0 && $halt_writing == false) {

                                if($first){

                                    $context .= $component.$blueprint;
                                    $halt_writing = true;
                                }
                            }else{
                                $context .= $blueprint;
                            }
                        }

                        /**
                         * Recreate Controller File
                         */
                        $filehandler->createFile("{$app_base}controller/{$controller}.php", $context);

                        unset( $context, $blueprint);
                        fclose($context_handle);

                    }else{
                        throw new \Exception("Host controller/api not found");
                    }

                }else{
                    echo "Error: Couldn't create {$name} view, {$name} exists\n";
                }
            }catch(\Exception $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                unset( $context, $blueprint);

                print "Component creation closed\n";
            }
        }


        /**
         * Destroy A View and Remove its conveyor
         *
         * @param Info $Info
         *
         * @param string $controllerAndComponent
         * @return void
         */
        public function destroy (Info $Info, string $controllerAndComponent){
            try{

                /**
                 * Details Gathering
                 */
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();

                    list($hostcontroller, $component) = explode('@', $controllerAndComponent);
                    $hostcontroller = ucfirst($hostcontroller);
                    $component = ucfirst($component);

                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }


                if( !is_null($hostcontroller) && file_exists("{$app_base}controller/{$hostcontroller}.php") ){

                    /**
                     * Remove View conveyor and delete the view
                     */
                    print "updating {$hostcontroller} controller...\n";

                    $context = null;

                    $blueprinting_source_handle = fopen("{$app_base}controller/{$hostcontroller}.php", 'r+');

                    $function_open = false;
                    $expression_open = false;
                    $host_controller_updated = false;
                    $write = null;

                    while($blueprint = fgets($blueprinting_source_handle)){

                        if($function_open){

                            $host_controller_updated = true;

                            if(preg_match("/(public|protected|private)[\s]+function[\s\S]+/i", trim($blueprint), $matches)){
                                $write = $blueprint;
                                $function_open = false;
                            }else{
                                $write = null;
                            }

                        }else{

                            if(preg_match("/(public|protected|private)[\s]+function[\s]+{$component}[\s]*\(.*\)/i", trim($blueprint), $matches)){
                                $function_open = true;
                                $write = null;
                            }else{
                                $write = $blueprint;
                            }
                        }

                        $context .= $write;
                    }

                    fclose($blueprinting_source_handle);
                    if ( $host_controller_updated ) {

                        file_put_contents("{$app_base}controller/{$hostcontroller}.php", $context);
                        print "{$hostcontroller} updated\n";

                        unset( $function_open, $expression_open, $host_controller_updated );
                    }else{
                        print "{$hostcontroller} not updated\n";
                    }
                }else{
                    throw new \Exception("Error: Couldn't found host controller for {$component} view");
                }

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
