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

    class Service  implements Writer
    {

        public function __construct(){}

        public function create(Info $Info, string $service) {

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


                    $service = \ucfirst($service);

                    if($service == null){
                        throw new \Exception("Undefined service name");
                    }else{
                        if( file_exists("{$app_base}service/{$service}.php") )
                            return false;
                    }

                    if(!file_exists("{$app_base}service/{$service}.php")) {


                        $Bp = new BluePrint($app_name);
                        $Base = $Bp->service($service);

                        $Info->getProgressMessage("createService");
                        (new Filehandler())->createFile("{$app_base}service/{$Base['filename']}", $Base['code']);

                        print("{$service} created\n");

                        /**
                         * Autoload classes
                         */

                        print( shell_exec($Info->getCommand("autoload")) )."\n";

                        
                    }else{
                        echo "Error: Couldn't create {$service} service, {$service} exists\n";
                    }
                }
            }catch(\Exception $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                print "Service creation closed\n";
            }
        }

        public function destroy(Info $Info, string $serviceName){

            try{

                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                    $serviceName = \ucfirst($serviceName);

                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                print("Destroying {$serviceName} service\n");
                @unlink("{$app_base}/service/{$serviceName}.php");

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
