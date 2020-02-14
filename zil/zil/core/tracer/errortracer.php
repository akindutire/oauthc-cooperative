<?php
namespace zil\core\tracer;

use zil\core\config\Config;
use zil\core\scrapper\Info;
use zil\factory\Logger;
use zil\factory\Session;

class ErrorTracer extends Config{

   public function __construct(\Throwable $t){

       /**
         * Get Tree from System
         */
        $tree = (new Info())->getTree();

        /**
         * Get Current App Running
         */
        $current_app = basename( (new parent())->curAppPath);

        $type = get_class($t);
        $msg = "<p style='color: #2c3e50;'><h1 style='color:red; '>{$type}</h1><br> <span style='color: #fff;'>{$t->getMessage()} in {$t->getFile()} on line {$t->getLine()} </span> </p>\n";

        foreach($t->getTrace() as $i => $error){
            if(sizeof($error) == 0)
                continue;

            $c = @$error['class'];
            $f = @$error['file'];
            $l = @$error['line'];

            $msg .= "<p style=' padding: 1.2rem; margin:20px 15px; background: #2c3e50; color: #fff; border-radius: 5px;'><b style='margin-right: 1rem;'>{$i}</b> {$f}<br> <span style='margin-left: 1.5rem;'>Function >>> {$c}::{$error['function']}() on line {$l}</span></p>\n";

            
        }
           

        if(!isset($tree->apps->{$current_app}->prod) || $tree->apps->{$current_app}->prod == false){
    

        /**
         * Output Error if not on production
         */


            /** Exclude Markup while rendering error from API */
            if( !is_null(Info::getRouteType()) && Info::getRouteType() == 'api' ){

                    Logger::ELog(strip_tags( str_replace('<br>', "\n",$msg) ) );

            }else{
                /** Include Markup while rendering error from browser */
                if(ob_get_length() > 0)
                    ob_flush();

                print(

                "
                <div style='min-height: 750px; width: 100%; background: rgba(0,0,0, 0.9); z-index: 99999px;  position: absolute; top: 0px; left: 0px;'>
                    <div style='width: 80%; height: auto; border-radius: 5px; font-size: 1rem; position: relative;  padding: 0.6rem; margin: 1.5rem auto; font-family:  consolas, Helvetica, sans-serif; '>         {$msg}
                    </div>
                </div>
                "
                );

            }

            die();

        }else{
            Logger::ELog($msg);
        }
   }
}
?>