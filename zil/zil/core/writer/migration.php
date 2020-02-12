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

    class Migration implements Writer
    {

        public function __construct(){}

        public function create(Info $Info, string $migration_name, string $table_name) {

            try{
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){

                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();

                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                if(!empty($app_base) ){

                    
                    $Bp = new BluePrint($app_name);
                    $Base = $Bp->migration($migration_name, $table_name);

                    if(!file_exists("{$app_base}migration/{$Base['filename']}")) {


                        print "Creating {$migration_name} migration...\n";
                        (new Filehandler())->createFile("{$app_base}migration/{$Base['filename']}", $Base['code']);

                        /**
                         * Autoload migration class
                         */

                        print( shell_exec($Info->getCommand("autoload")) )."\n";
                    }
                }
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{

                print "Migration creation  closed\n";
            }
        }

        public function destroy(Info $Info, string $name){

        }

    }
?>
