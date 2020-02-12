<?php
namespace zil\core\facades\decorators;

use zil\core\interfaces\Param;
use zil\core\tracer\ErrorTracer;



    trait Hooks{

        /**
         * OnInitialization of Controller, Always after Constructor
         *
         * @return void
         */
        public function onInit(Param $param){

            try{

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        /**
         * OnAuthorization of Controller, Always after OnInitialization
         *
         * @return void
         */
        public function onAuth(Param $param){

            try{

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }



        /**
         * onEnd of Controller method
         *
         * @return void
         */
        public function onDispose(Param $param){

            try{

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }
    }



?>
