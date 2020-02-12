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

    class Middleware  implements Writer
    {

        public function __construct(){}

        public function create(Info $Info, string $middleware) {

            try{

                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                $tree = $Info->getTree();
                $app_name = $Info->getAppName();

                if(is_null(@$tree->apps->{$app_name})){

                    echo "Error: {$app_name} not recognised";
                    return false;
                }

                if(!empty($app_base) ){


                    $middleware = \ucfirst($middleware);

                    $Bp = new BluePrint($app_name);
                    $Base=$Bp->middleware($middleware);


                    if($middleware == null){
                        throw new \Exception("Undefined middleware name");
                    }else{
                        if( file_exists("{$app_base}middleware/{$Base['filename']}") )
                            return false;
                    }


                    if(!file_exists("{$app_base}middleware/{$Base['filename']}")) {


                        echo "Creating {$middleware} middleware...\n";
                        (new Filehandler())->createFile("{$app_base}middleware/{$Base['filename']}", $Base['code']);

                        /**
                         * Autoload classes
                         */
                        print( shell_exec($Info->getCommand("autoload")) )."\n";


                    }else{
                        echo "Error: Couldn't create {$middleware} middleware, {$middleware} exists\n";
                    }
                }
            }catch(\Exception $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                print "Middleware creation closed\n";
            }
        }

        public function destroy(Info $Info, string $middlewareName){

            try{

                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();

                    $middlewareName = \ucfirst($middlewareName);

                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                print("Destroying {$middlewareName} middleware\n");
                @unlink("{$app_base}/middleware/{$middlewareName}.php");

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
