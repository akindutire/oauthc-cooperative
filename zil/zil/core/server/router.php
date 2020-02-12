<?php
namespace zil\core\server;

use zil\core\tracer\ErrorTracer;
use zil\core\facades\helpers\Reporter;

class Router{
    use Reporter;


    public function __construct(){ }

    public static function Route(){

        try{
            $req = (new Request())->Uri()->getFrame();

            if( count($req) > 0){
                Response::fromHttp( $req );
            }else{
                http_response_code(404);
                self::report(404);
            }

            unset($req);
            return;

        }catch(\InvalidArgumentException $t){
            new ErrorTracer($t);
        }catch(\TypeError $t){
            new ErrorTracer($t);
        }catch(\BadMethodCallException $t){
            new ErrorTracer($t);
        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function isRouteExist(string $uri, string $method = null) : array {

        try {

            if(is_null($method))
                $m = 'GET';

            $frame = (new Request())->setUriAndMethod($uri, $m)->getFrame();

            if(  sizeof($frame) == 0)
                return [ 'frame' => $frame, 'exist' => false ];

            return [ 'frame' => $frame, 'exist' => true ];;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }

    }
}
?>
