<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core;

use zil\core\interfaces\ApplicationManager as AppManagerIf;
use zil\core\scrapper\Info;
use zil\core\directory\Manager as App_Dir_Manager;
use zil\core\directory\Tree;
use zil\core\writer\Init as Init_Writer;
use zil\core\writer\View as View_Writer;
use zil\core\writer\Ht;

use zil\factory\Filehandler;


    class ApplicationManager implements AppManagerIf
    {

        private $appName    = null;

        /**
         * Constructor
         */
        public function __construct(){ }

        /**
         * Switch between apps
         *
         * @param string $app_name
         * @return void
         */
        public function useApp(string $app_name):  void{

            $tree = json_decode(file_get_contents((new Info())->getSharedPath()."data/.app.json"));

            if(isset($tree->apps->{$app_name})){

                $tree->currentApp = $app_name;
                file_put_contents((new Info())->getSharedPath()."data/.app.json",json_encode($tree,JSON_PRETTY_PRINT));
                print "Switched to {$app_name}\n";

            }else{
                print "Couldn't use {$app_name}, {$app_name} not found\n";
                print "+-----------Apps------------+\n";

                foreach($this->showApps() as $app_name){
                    print $app_name;
                }

            }

        }

        /**
         * List installed apps
         *
         * @return void
         */
        public function showApps(){

            try{
                $tree = json_decode(file_get_contents((new Info())->getSharedPath()."data/.app.json"));
                foreach($tree->apps as $app){
                    yield $app->name."\n";
                }
            }catch(\Throwable | \ClosedGeneratorException $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }
        }



        /**
         * Install App
         */
        public function createApp(Info $Info){

            try{

                if(!empty((string)$Info->getAppName()) && !is_null($Info->getAppDir())){
                    $app_base = $Info->getAppBase();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories\n");
                }

                $tree = $Info->getTree();


                if( !is_null(@$tree) ) {

                    (new App_Dir_Manager())->validateDirectoryListing($Info);


                    if(!is_null(@($tree)->apps->{$Info->getAppName()}))
                        throw new \Exception("{$Info->getAppName()} App already exist");

                    (new Filehandler())->createDir("$app_base");

                    $tree->currentApp = '';
                    file_put_contents((new Info())->getSharedPath()."data/.app.json",json_encode($tree,JSON_PRETTY_PRINT));


                    $init = new Init_Writer();
                    $init->create($Info);
                    $this->registerApp($init->_isDefault);

                    (new View_Writer())->create($Info, 'Index', 'Home');

                    $Info->getProgressMessage("createApp");

                    $this->useApp($Info->getAppName());

                }else{
                    print "Error: {$Info->getAppName()} already existing\n";
                }
            }catch(\Throwable $e){
                print $e->getMessage()."\n";
            }finally{
                print "App creation closed\n";
            }
        }

        /**
         * Register a newly installed app
         *
         *
         * @return void
         */
        private function registerApp(bool $isDefault){

            $Info = new Info();
            $Info->getProgressMessage("registerApp");
            (new Tree())->createAppTree($Info, $isDefault);
        }

        /**
         * Release App to Production Mode
         *
         * @param Info $Info
         * @return void
         */
        public function setProdMode(Info $Info){
            try{

                $tree = $Info->getTree();

                $tree->apps->{$Info->getAppName()}->prod = true;

                file_put_contents((new Info())->getSharedPath()."data/.app.json",json_encode($tree,JSON_PRETTY_PRINT));

                print($Info->getAppName()." is in production mode\n");

            }catch(\Throwable $e){
                print $e->getMessage();
            }

        }

        /**
         * Release App to Development Mode
         *
         * @param Info $Info
         * @return void
         */
        public function setDevMode(Info $Info){
            try{

                $tree = $Info->getTree();

                $tree->apps->{$Info->getAppName()}->prod = false;
                file_put_contents((new Info())->getSharedPath()."data/.app.json",json_encode($tree,JSON_PRETTY_PRINT));

                print($Info->getAppName()." is in development mode\n");

            }catch(\Throwable $e){
                print $e->getMessage();
            }

        }

        /**
         * Uninstall App
         *
         * @param Info $Info
         * @return void
         */
        private function deleteAppFiles(Info $Info){

            try{
                echo "destroying app init...\n";

                $AppDir = $Info->getAppDir();
                $app_name = $Info->getAppName();
                $tree = $Info->getTree();
                
                print "Unregistering app...\n";

                unset($tree->apps->{$app_name});
                file_put_contents($Info->getSharedPath()."data/.app.json",json_encode($tree,JSON_PRETTY_PRINT));
                unset($tree);

                $filehandler = new Filehandler;

                print "Rewriting apps index...\n";

                (new Init_Writer())->destroy($Info);

                print "destroying app src logic...\n";
                $filehandler->removeDir("{$AppDir}src/{$app_name}/");

                return null;
            }catch(\Throwable $e){
                print $e->getMessage();

            }finally{
                print "Main destruction closed\n";
            }
        }


        /**
         * Remove app from app list while uninstalling
         *
         * @param Info $Info
         * @return boolean
         */
        private function unregisterApp(Info $Info) : bool {

            $tree = $Info->getTree();
            $app_name = $Info->getAppName();

            if(is_null(@$tree->apps->{$app_name})){

                echo "Error: {$app_name} not recognised\n";
                return false;
            }else{

                $this->deleteAppFiles($Info);
                return true;
            }
        }

        /**
         * Grand destroy for uninstall
         *
         * @param Info $Info
         * @param string|null $app_name
         * @return void
         */
        public function destroy(Info $Info, ?string $app_name) : bool{

            try{
                $tree = $Info->getTree();

                $tree->currentApp = '';
                file_put_contents($Info->getSharedPath()."data/.app.json",json_encode($tree,JSON_PRETTY_PRINT));

                $this->unregisterApp($Info);

                return true;
            }catch(\Throwable $e){
                print $e->getMessage();
            }
        }

        /**
         * Switch Out of app
         *
         * @param string $app_name
         * @return void
         */
        public function exitApp(string $app_name){
            try{
                $tree = json_decode(file_get_contents((new Info())->getSharedPath()."data/.app.json"));

                $tree->currentApp = '';

                file_put_contents((new Info())->getSharedPath()."data/.app.json",json_encode($tree,JSON_PRETTY_PRINT));

            }catch(\Throwable $e){
                print $e->getMessage();
            }
        }


    }
