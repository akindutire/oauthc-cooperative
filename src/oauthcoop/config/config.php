<?php
         namespace src\oauthcoop\config;

         use \zil\core\interfaces\Config as ConfigInterface;

         /**
          *   @Configuration:oauthcoop
          */

         class Config implements ConfigInterface{

             private const DB_DRIVER 	=   'mysql';
             private const DB_HOST 		=   'remotemysql.com';
             private const DB_USER 		=   'N0VklpOp4t';
             private const DB_PASSWORD 	=   '6alkh1bMyK';
             private const DB_NAME 		=   'N0VklpOp4t';
             private const DB_PORT 		=    3306;
             private const DB_ENGINE	=   'MyISAM';
             private const DB_CHARSET	=   'latin1';
             private const APP_NAME	= "oauthcoop";

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
                     'charset'	=>	self::DB_CHARSET,
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