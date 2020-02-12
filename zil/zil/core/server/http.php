<?php
/**
 * Author: Akindutire Ayomide Samuel
 */

namespace zil\core\server;

use zil\core\config\Config;
use zil\core\exception\UnexpectedRouteException;
use zil\core\exception\UnexpectedCaseException;
use zil\core\tracer\ErrorTracer;

class Http extends Config{

    private $uri = '';
    private $method = '';
    private $hasResponseToReturn = false;
    private $headersToBeUsed = ['Content-Type: application/json', 'Accept: application/json'];
    private $data = [];


    public function __construct(string $uri){
        try{

            $cfg = new Config;
            $this->uri =  trim($uri, '/');
            return $this;

        }catch(\Throwable $t){
            new ErrorTracer($t);
        }

    }

    /**
     * Set the return response flag ahead of time
     * @return Http
     */
    public function hasResponse() : Http {
        try {

            $this->hasResponseToReturn = true;
            return $this;

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
     * Attach headers to request
     * @param array $headers
     * @return Http
     */
    public function headers(array $headers) : Http{
        try {

            $this->headersToBeUsed = $headers;
            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Accept dynamic request method and call a response on behalf on the host request method
     * @return object|string
     */
    private function requestProcessor() {
        try {

            if (empty($this->uri))
                throw new \Exception("Undefined url");

            $ReqHandle = curl_init($this->uri);

            $body = json_encode([]);
            if( sizeof($this->data) > 0 )
                $body = json_encode($this->data);

            $returnTransfer = 0;
            if( $this->hasResponseToReturn )
                $returnTransfer = 1;

            $isPost = 0;
            if( $this->method != 'GET')
                $isPost = 1;


            if($isPost == 1){
                curl_setopt($ReqHandle,CURLOPT_POST, $isPost);
                curl_setopt($ReqHandle, CURLOPT_POSTFIELDS, $body);
            }
            
            curl_setopt_array($ReqHandle, [
                CURLOPT_RETURNTRANSFER => $returnTransfer,
                CURLOPT_HTTPHEADER => $this->headersToBeUsed
            ]);


            $result = curl_exec($ReqHandle);

            if(curl_errno($ReqHandle)){

                return curl_error($ReqHandle);
            }

            if($returnTransfer == 1)
                return json_decode($result);



        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Execute a get request
     * @return object|string
     */
    public function get()  {
        try {

            /**
             * Set up request method
             */
            $this->method = 'GET';
            /**
             * Process request and call for response
             */
            return $this->requestProcessor();


        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Execute a post request
     * @param array|null $formData
     * @return object|string
     */
    public function post(?array $formData = []) {
        try {
            /**
             * Set up request method
             */
            $this->method = 'POST';

            /**
             * Set up request data
             */
            $this->data = $formData;

            /**
             * Process request and call for response
             */
            return $this->requestProcessor();

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
     * Execute a put request
     * @param array|null $formData
     * @return object|string
     */
    public function put(?array $formData = []) {
        try {
            /**
             * Set up request method
             */
            $this->method = 'PUT';

            /**
             * Set up request data
             */
            $this->data = $formData;

            /**
             * Process request and call for response
             */
            return $this->requestProcessor();

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
     * Execute a delete request
     * @return object|string
     */
    public function delete() {

        try {
            /**
             * Set up request method
             */
            $this->method = 'POST';


            /**
             * Process request and call for response
             */
            return $this->requestProcessor();

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }
}

?>
