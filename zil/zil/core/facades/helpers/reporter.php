<?php
namespace zil\core\facades\helpers;
use zil\core\config\Config;
use zil\core\scrapper\Info;
use zil\core\server\Router;
use zil\core\server\Response;

    trait Reporter{

       public static function report(int $err_code){

            if (ob_get_contents())
                ob_end_clean();



            $existenceInfo = (new Router())->isRouteExist($err_code);

            if( $existenceInfo['exist'] == true ){

                if(Info::getRouteType() == 'api')
                    Response::fromApi( [ "{$err_code} error" ], 200 );
                else
                    Response::fromHttp( $existenceInfo['frame'] );

            }else {

                if(Info::getRouteType() == 'api') {
                    Response::fromApi(["{$err_code} error"], 200);
                }else{
                    $r = (new Config())->getSysPath()."/core/facades/reports/{$err_code}/index.php";
                    include_once ($r);
                    unset($r);
                }

                die();

            }

        }

    }



?>
