<?php
namespace zil\core\scrapper;

use zil\core\tracer\ErrorTracer;
use zil\factory\Filehandler;


class Info
    {

        public static $_appDir;
        public static $_appName;

        /** @var array Provides cross data for methods for different modules and classes of individual app */
        public static $_dataLounge = [];

        /**
         * Constructor
         */
        public function __construct(){}
            
        /**
         * Set Application Root Directory
         *
         * @param string $dir
         * @return void
         */

        public static function getRouteType() : ?string {
            if (isset(self::$_dataLounge['ROUTE_USED']))
                return self::$_dataLounge['ROUTE_USED'];
            else
                return null;
        }

        public function setAppDir( string $dir ){
            try {

                self::$_appDir = str_replace(DIRECTORY_SEPARATOR, "/", "{$dir}/");

            }catch (\Throwable $t){

                new ErrorTracer($t);

            }
        }

        /**
         * Set application name
         *
         * @param string $app_name
         * @return void
         */
        public function setAppName( string $app_name )  {
            try {
                self::$_appName = $app_name;
            }catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        /**
         * Getter of application directory
         *
         * @return string
         */
        public function getAppDir(): string{
            try{

                return self::$_appDir;

            }catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }


        /**
         * Getter of application commands
         *
         * @return string
         */
        public function getCommand(string $anchor): string{
            try{

                $tree = json_decode(file_get_contents($this->getSystemPath() . "data/tree/.sys.json"));

                if(isset($tree->commands->{$anchor}))
                    return $tree->commands->{$anchor};
                else
                    throw new \Exception("Unknown command {$anchor}");
                    
            }catch (\Throwable $t){
                print($t->getMessage()."on line ".$t->getLine()." from ".$t->getFile());
            }
        }


        /**
         * 
         *
         * @return string
         */
        public function getAppName(): string{
            try{

                return  self::$_appName;

            }catch (\Throwable $t){

                new ErrorTracer($t);

            }
        }

        /**
         * System path
         *
         * @return string
         */
        public function getSystemPath(): string{
            try{

                $sys_path   =   str_replace(DIRECTORY_SEPARATOR, "/", dirname(__DIR__ , 2));
                return $sys_path."/";

            }catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        /**
         * System path
         *
         * @return string
         */
        public function getSharedPath(): string{
            try{

                $base_path   =   str_replace(DIRECTORY_SEPARATOR, "/", dirname(__DIR__ , 4));

                return $base_path . "/src/shared/";

            }catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        /**
         * 
         *
         * @return string
         */
        public function getAppBase():  string{
            try {
                return $this->getAppDir() . "/src/" . $this->getAppName() . "/";
            }catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }


        /**
         * Tree
         *
         * @return object
         */
        public function getTree() : object{
            try{

                $appSchemaDir   =   str_replace(DIRECTORY_SEPARATOR, "/", dirname(__DIR__ , 4)). "/src/shared/data/";

                if( file_exists("{$appSchemaDir}.app.json") ){
                    return json_decode(file_get_contents("{$appSchemaDir}.app.json"));
                }else{
                    /**
                     * Sample of app track
                     */
                    $sampleAppTrack = file_get_contents($this->getSystemPath()."data/tree/SAMPLEAPP.json");
                    /**
                     * Create app.json, App track file
                     */
                    (new Filehandler())->createFile("{$appSchemaDir}.app.json", null);

                    $f = fopen("{$appSchemaDir}.app.json", 'w+');

                    fwrite($f, $sampleAppTrack);
                    fclose($f);

                    return json_decode(file_get_contents("{$appSchemaDir}.app.json"));
                }

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        /**
         * Progress messages
         *
         * @param string $method
         * @return void
         */
        public function getProgressMessage(  string $method = null ){

             try{
                if(!empty($method)){

                    switch ($method){
                        case "validateDirectoryListing" : echo "creating directory listings...\n" ; break;
                        case "createInit" : echo "creating app init...\n" ; break;
                        case "createConfiguration" : echo "loading app configuration...\n" ; break;
                        case "createComposer" : echo "creating app autoload...\n" ; break;
                        case "createController" : echo "creating template controller...\n" ; break;
                        case "createComponent" : echo "creating component...\n" ; break;
                        case "createModel" : echo "creating template model...\n" ; break;
                        case "createView" : echo "creating template view...\n" ; break;
                        case "createService" : echo "creating template service...\n"; break;
                        case "registerApp" : echo "registering app...\n"; break;
                        default: echo ""; break;
                    }
                }else{
                    throw new \InvalidArgumentException("Expected  a string as an argument");
                }
            }catch(\InvalidArgumentException $t){
                new  ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        /**
         * current working application name
         *
         * @return string
         */
        public function getCurrentApp():  string{
            try {

                $tree = $this->getTree();
                if(is_object($tree))
                    return $tree->currentApp;

                throw new \Exception("No app tree found");

            }catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }
    }
?>
