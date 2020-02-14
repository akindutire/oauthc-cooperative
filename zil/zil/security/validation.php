<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\security;

use zil\core\scrapper\Info;

use zil\core\tracer\ErrorTracer;

class Validation{

    public static $errors = [];

    /**
     * Set rules for Incoming Forms Request
     * Use case: new Validation( [key, 'email,url,number,text,boolean|required|minlength:8|maxlength:9', callable ] )
     *
     * @param array $rules
     * @return boolean
     */
    public function __construct (array ...$rules)  {
        try{

            self::$errors = [];

            $isApi = false;

            // check from browser

            if( Info::getRouteType() == 'api' ){

                // further check from Api
                $isApi = true;
                $data = json_decode(file_get_contents('php://input'), true);


                if(sizeof( $data ) == 0)
                    throw new \Exception("Empty request body sent, couldn't validate");
            }

            foreach($rules as $rule){

                if(sizeof($rule) > 1){

                    $key = $rule[0];

                    if(!is_string($rule[0]) || !is_string($rule[1]))
                        throw new \InvalidArgumentException("Argument #1 and #2 expected to be a string");
                        
                    $r = explode('|', $rule[1]);
                    
                    if(!is_array($r))
                        throw new \ArgumentCountError("Argument #2 of rule {$key} not found");

                    

                    if( !$isApi ){

                        if( !isset($_REQUEST[$key]) ){
                            self::$errors[$key] = "Undefined index: {$key} in request array";
                            break;
                        }

                        $val = $_REQUEST[$key];

                    }else{
                        
                        if( !isset($data[$key]) ){
                            self::$errors[$key] = "Undefined index: {$key} in request array";
                            break;
                        }

                        $val = $data[$key];
                    }

                    if(in_array('required', $r)){
                        if(empty($val) && $val !== 0){
                            self::$errors[$key] = "$key must not be empty";
                            break;
                        }
                    }

                    if(in_array('email', $r)){
                        /**Email */
                        if( !filter_var($val, FILTER_VALIDATE_EMAIL) )
                            self::$errors[$key] = "$key expected to be an email";


                    }else if(in_array('url', $r)){
                        /**Is url */
                        if( !filter_var($val, FILTER_VALIDATE_URL) )
                            self::$errors[$key] = "{$key} expected to be an URL";

                    }

                    if(in_array('number', $r)){

                        if( !is_numeric($val) )
                            self::$errors[$key] = "{$key} expected to be a number";

                    }else if(in_array('text', $r)){

                        if( !is_string($val) )
                            self::$errors[$key] = "{$key} expected to be a text";

                    }else if(in_array('boolean', $r)){

                        if( !is_bool($val) )
                            self::$errors[$key] = "{$key} expected to be a boolean";

                    }

                    foreach($r as $mini_rule){

                        if(preg_match('/maxlength[\s]*:[\s]*([\d]+)/', $mini_rule, $matches) > 0){

                            $maxlen = intval($matches[1]);

                            if( strlen($val) > $maxlen )
                                self::$errors[$key] = "{$key} expected at most  {$maxlen} characters";


                        }else if( preg_match('/minlength[\s]*:[\s]*([\d]+)/', $mini_rule, $matches) > 0 ){

                            $minlen = intval($matches[1]);

                            if( strlen($val) < $minlen )
                                self::$errors[$key] = "{$key} expected at least {$minlen} characters";

                        }else if( preg_match('/min[\s]*:[\s]*([\d]+)/', $mini_rule, $matches) > 0 ){

                            $min = intval($matches[1]);

                            if( $val < $min )
                                self::$errors[$key] = "{$key} expected least value {$min}";

                        }else if( preg_match('/max[\s]*:[\s]*([\d]+)/', $mini_rule, $matches) > 0 ){

                            $max = intval($matches[1]);

                            if( $val > $max )
                                self::$errors[$key] = "{$key} expected most value {$max}";

                        }

                    }

                    if(isset($rule[2]) && is_callable($rule[2])){
                        $rule[2]();
                    }

                }else{
                    throw new \Exception("Validation rule must contain at least 2 entries( form key and rule )");
                }

            }

        }catch(\Throwable $t){
            new ErrorTracer($t);

        }

    }

    /**
     * @return bool
     */
    public function isPassed() : bool {

        if(sizeof( self::$errors ) > 0)
            return false;
        else
            return true;

    }

    /**
     * @return array
     */
    public function getError() : array {
        return self::$errors;
    }

    /**
     * @return string
     */
    public function getErrorString() : string {
        return implode("\n", $this->getError());
    }
}
?>
