<?php
/**
 * Author: Akindutire Ayomide Samuel
 *
 */
namespace zil\core\directory;

use zil\core\scrapper\Info;
use zil\factory\Filehandler;

    class Manager
    {

        public function __construct(){}

        /**
         * Default Directories without installations
         *
         * @param Info $Info
         * @return void
         */
        private function dumpBaseDir(Info $Info){

            try{

                $AppDir = $Info->getAppDir();
                (new Filehandler())->createDir("{$AppDir}/src");

                if(!is_dir("{$AppDir}/src/shared/data"))
                    (new Filehandler())->createDir("{$AppDir}/src/shared/data");

            }catch(\Throwable $e){
                print $e->getMessage();
            }
        }

        /**
         * Enforce Directories during installation
         *
         * @param Info $Info
         * @return void
         */
        public function validateDirectoryListing(Info $Info) : array {

            try{

                $this->dumpBaseDir($Info);

                $filehandler = new Filehandler;

                $halt_flag = null;

                $directories = [
                    'config',
                    'controller',
                    'controller/api',
                    'session',
                    'log',
                    'log/event',
                    'log/error',
                    'model',
                    'view',
                    'service',
                    'asset',
                    'asset/template',
                    'asset/uresource',
                    'middleware',
                    'route',
                    'migration'
                ];

                $app_base = $Info->getAppBase();

                $Info->getprogressMessage("validateDirectoryListing");

                foreach ($directories as $folder){

                    if ($filehandler->createDir("{$app_base}{$folder}") != true)
                        break;

                }
                return $directories;

            }catch(\Throwable $e){
                print $e->getMessage();
            }
        }

    }
?>
