<?php
/**
 * @Author Akindutire, Ayomide Samuel
 */
namespace zil\blueprint;

class Blueprint{

     private $app = '';

     /**
      * [__construct description]
      * @method __construct
      */
     public function __construct(string $appName){
         $this->app = $appName;
     }

     /**
      * [Stores configuration file format for app bundles]
      * @method config
      *
      * @return array           [description]
      */
     public function config() : array {

         $appName = $this->app;
         $code = "<?php
         namespace src\\{$appName}\config;

         use \zil\core\interfaces\Config as ConfigInterface;

         /**
          *   @Configuration:{$appName}
          */

         class Config implements ConfigInterface{

             private const DB_DRIVER 	=   'mysql';
             private const DB_HOST 		=   'localhost';
             private const DB_USER 		=   'root';
             private const DB_PASSWORD 	=   '';
             private const DB_NAME 		=   'test';
             private const DB_PORT 		=    3306;
             private const DB_ENGINE	=   'MyISAM';
             private const DB_CHARSET	=   'latin1';
             private const APP_NAME	= \"{$appName}\";

             public function __construct(){  }

             /** Specify allowed host to your app */
             public function getCorsPolicy() : array { 
                return ['*'];
             }

             /**
              * Specify app name and expected to be unique
              *
              * @return string
              */
             public function getAppName(): string{
                return self::APP_NAME;
             }

             /**
              * Database Info
              *
              * @return array
              */
             public function getDatabaseParams(): array{
                 return [
                     'driver'	=>	self::DB_DRIVER,
                     'host'		=>	self::DB_HOST,
                     'user'		=>	self::DB_USER,
                     'password'	=>	self::DB_PASSWORD,
                     'database'	=>	self::DB_NAME,
                     'port'		=>	self::DB_PORT,
                     'engine'	=>	self::DB_ENGINE,
                     'charset'	=>	self::DB_CHARSET
                 ];
             }

             /**
              * Other configuration options
              *
              * @return array
              */
             public function options(): array{
                 return [
                     'pageLoadStategy' => 'async',
                     'projectBasePath' => '/'
                 ];
             }
         }
        ";
         return [ 'code' => $code, 'filename' => 'config.php'];
     }


     /**
      * [Stores preprocessed app controller]
      * @method controller
      *
      * @param  string     $controller [description]
      * @return array                  [description]
      */
     public function controller( string $controller = null) : array {

         if(is_null($controller))
             $controller = 'Home';

         /**
          * Controller in groups
          */
         $controllerTokenizeArray = explode("/", trim($controller) );
         $controllerTokenizeArray = array_map( function($controllerTokens) {
            return ucfirst($controllerTokens);
         }, $controllerTokenizeArray);
         $controller = implode('/', $controllerTokenizeArray);

         $controllerClass = \ucfirst(end($controllerTokenizeArray));
         $appName = $this->app;

    $code = "<?php
namespace src\\{$appName}\controller;

use \zil\core\server\Param;
use \zil\core\server\Response;
use \zil\\factory\View;
use \zil\core\\facades\helpers\Notifier;
use \zil\core\\facades\helpers\Navigator;
use \zil\core\\facades\decorators\Hooks;

use src\\{$appName}\Config;

/**
 *  @Controller:{$controllerClass} []
*/

class {$controllerClass}{

    use Notifier, Navigator, Hooks;

    public function __construct(){}
    public function onInit(Param \$param){}
    public function onAuth(Param \$param){}
    public function onDispose(Param \$param){}

}
    ";

         return [ 'code' => $code, 'filename' => "{$controller}.php"];
     }

     /**
      * [Stores preprocessed middleware template]
      * @method middleware
      * @param  string     $appName    [app bundle name]
      * @param  string     $middleware [description]
      * @return array                  [description]
      */
     public function middleware( string $middleware ) : array {

         $appName = $this->app;

         $middleware = \ucfirst($middleware);

    $code = "<?php
namespace src\\{$appName}\middleware;

use zil\core\interfaces\Middleware;
use zil\core\interfaces\Param;

/**
 *   @Middleware:{$middleware} []
*/

class {$middleware} implements Middleware{

    public function __construct(Param \$param){ }

}
";
         return [ 'code' => $code, 'filename' => "{$middleware}.php"];
     }

     public function migration( string $migrationTag, string $table ){

         $appName = $this->app;
         $migration = date('Y-m-d-').time()."$".$migrationTag;
         $table = \ucfirst($table);
         $code = "<?php
namespace src\\{$appName}\migration;

use zil\\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:{$migrationTag}->{$table} []
*/
class {$migrationTag} implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        \$schema = new Schema('{$table}');

        \$schema->build('id')->Primary()->Integer()->AutoIncrement();
        \$schema->build('created_at')->Timestamp();
    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){

    }
}
    ";
         return [ 'code' => $code, 'filename' => "{$migration}.php"];
     }

     public function model( string $model ) : array {

         $appName = $this->app;
         $model = \ucfirst($model);

    $code = "<?php
namespace src\\{$appName}\model;

use \zil\\factory\Model;

class {$model}{

    use Model;

    public \$id = null;
    public static \$table = '{$model}';
    
    public function __construct(){}
    
}
    ";
         return [ 'code' => $code, 'filename' => "{$model}.php"];

     }

     /**
      * [route description]
      * @method route
      *
      * @param  string $type    [Api or Web]
      * @return array           [description]
      */
     public function route( string $type ) : array {

         $appName = $this->app;
         $type = \ucfirst($type);

    $code = "<?php
namespace src\\{$appName}\\route;

use \zil\core\interfaces\Route;
use \zil\core\server\Resource;

/**
 *   @Route:{$type}
*/

class {$type} implements Route{

    use \zil\core\\facades\decorators\Route_D1;

    /**
     * {$type} routes
    *
    * @return array
    */
    public function route(): array{
        return [];
    }
}

    ";
        return [ 'code' => $code, 'filename' => "{$type}.php"];
     }


     /**
      * [service description]
      * @method service
      * @param  string  $service [description]
      * @return array            [description]
      */
     public function service( string $service ) : array {

         $appName = $this->app;
         $service = \ucfirst($service);

    $code = "<?php
namespace src\\{$appName}\service;

use \zil\core\server\Http;
use \zil\\factory\Session;
use \zil\\factory\Fileuploader;
use \zil\\factory\Filehandler;
use \zil\\factory\Logger;
use \zil\\factory\Mailer;
use \zil\\factory\Redirect;

use \zil\security\Encryption;
use \zil\security\Sanitize;

/**
 * @Service:$service []
*/

class {$service}{

    public function __construct(){ }

}
    ";
         return [ 'code' => $code, 'filename' => "{$service}.php"];
     }


     /**
      * [view description]
      * @method view
      * @param  string $view [description]
      * @return array        [description]
      */
     public function view( string $view ) : array {

         /**
          * View in groups
          */
         $viewTokenizeArray = explode("/", trim($view) );
         $viewTokenizeArray = array_map( function($viewTokens) {
             return ucfirst($viewTokens);
         }, $viewTokenizeArray);

         $view = implode('/', $viewTokenizeArray);


         $code = "<!DOCTYPE html>
         <html>
         <head>

             <title>{$view}</title>
             <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
             <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />

             <style>
             </style>

         </head>

         <body class=\"\" style=\"font-family:'sans-serif' !important;\">

         <article class=\"\" style=\"padding: 128px; margin-top: 150px; height: 100%;\">
             <div style=\"display: flex; justify-content: center; color: grey; font-size: 64px;\">
                 {$view} works
             </div>
         </article>

         <footer></footer>

         </body>

         </html>
         ";
         return [ 'code' => $code, 'filename' => "{$view}.php"];

     }

     /**
      * [hts description]
      * @method hts
      * @return array [description]
      */
     public function hts( ) : array {

         $code = "
        Options FollowSymLinks MultiViews
        IndexOptions FancyIndexing

        DirectoryIndex index.php index.html main.php main.html

        <FilesMatch \.(ini|xml|log|htaccess|htpasswd)>
            order allow,deny
            deny from all
        </FilesMatch>

        RewriteEngine On
        RewriteBase /

        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-l
        RewriteRule ^(.+)$ index.php?url_parameters=$1 [NC,L]

        php_flag display_startup_errors off
        php_value post_max_size 200M
        php_value upload_max_filesize 200M
        php_value memory_limit 400M
        php_value max_execution_time 600


         ";
         return [ 'code' => $code, 'filename' => ".htaccess"];

     }

     /**
      * [composer description]
      * @method composer
      * @return array    [description]
      */
     public function composer() : array {
         return
         [
             "code" => json_encode(["autoload"=> ["psr-4"=> ["src\\"=>"src"] ] ],JSON_PRETTY_PRINT),
             "filename" => "composer.json"
         ];
     }


     /**
      * [init description]
      * @method init
      * @param  array $configs [description]
      * @return array          [description]
      */
     public function init( ?array $configs ) : array {
       
        /**
         * Get string representation of config array
         */

        $namespaces = "";
        $appCFG = "";
        
        if(is_null($configs))
            $configs = [];
         foreach($configs as $appkey => $app_name){
            $namespaces .= "use src\\{$app_name}\config\Config as $app_name"."CFG;\n";
            $appCFG .= "\t'{$appkey}' => new {$app_name}CFG, ";
         }

$code = "<?php

include_once \$_SERVER['DOCUMENT_ROOT'].'/zil/vendor/autoload.php';
include_once \$_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
include_once \$_SERVER['DOCUMENT_ROOT'].'/zil/zil/main.php';

use zil\App;
{$namespaces}

\$cfg = [ $appCFG ];

\$AppSpace = new App(\$cfg);

/**
 * @params
*  true - allow all | false - deny all
*/

    \$AppSpace->start();

";


         return [ 'code' => $code, 'filename' => "index.php" ];

     }
}

?>
