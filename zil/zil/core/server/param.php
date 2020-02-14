<?php 
namespace zil\core\server;

use zil\core\config\Config;
use zil\core\interfaces\Param as PIf;
use zil\core\tracer\ErrorTracer;
use zil\factory\Logger;

class Param implements PIf{

    private $u = null;
    private $f = null;


    /**
     * Param constructor.
     * @param object $urlParameters
     * @param object $formParameters
     */
    public function __construct(object $urlParameters, object $formParameters){

        $this->u = $urlParameters;
        $this->f = $formParameters;
     }

    /**
     * @return object|string
     */
    public function url(?string $param = null) {
        try{

            if(!is_null($param)){

                if( property_exists($this->u, $param) )
                    return $this->u->$param;
                else
                    throw new \Exception("{$param} is not found in url params");

            }else{
                return $this->u;
            }

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }

    }

    /**
     * @return object|string
     */
    public function form(?string $param = null) {

        try{

            if(!is_null($param)){
                
                if( property_exists($this->f, $param) )
                    return $this->f->{$param};
                else
                    throw new \Exception("{$param} is not found in form params");

            }else{
                return $this->f;
            }

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }

    }

    
 
}

?>