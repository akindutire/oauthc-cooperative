<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core;

use zil\core\tracer\ErrorTracer;


    class TextProcessor
    {

        public function __construct(){}
        
        /**
         * Match Lines, words, just context
         *
         * @param string $search
         * @param string $stack
         * @return boolean
         */
        public static  function IfExact(string $search, string $stack): bool{
             
            try{
                $s = trim($search);
                $st = trim($stack);
                
                if( $s == $st)
                    return true;
                else
                    return false;
            }catch(\InvalidArgumentException $t){
               print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }catch(\LogicException $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }
        }

        /**
         * IfMatch
         *
         * @param string $search
         * @param string $stack
         * @return boolean
         */
        public static  function IfMatch(string $search, string $stack): bool{
            try{
                $s = trim($search);
                $st = trim($stack);
                
                if( preg_match_all("($s)", $st) === 1)
                    return true;
                else
                    return false;
            }catch(\InvalidArgumentException $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }catch(\LogicException $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }catch(\Throwable $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }
        }

    }
?>