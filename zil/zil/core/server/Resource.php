<?php
/**
 *  Author: Akindutire, Ayomide Samuel
 *  Created: 10-July-2019
 */

namespace zil\core\server;

use zil\core\tracer\ErrorTracer;

class Resource{

    private $as_view        = false;
    private $middleware     = [];
    private $denials        = [];
    private $allowed        = [];
    private $data           = [];
    private $trial          = 0;
    private $resource       = '';
    private $alias          = [];
    private $method         = '';


    /**
     * @param string $resource
     */
    public function __construct( string $resource ) {
        $this->resource = trim($resource);
    }

    /**
    *   Set request method to GET
    *   @return Resource
    */
    public function get() : Resource {
        try{
            
            $this->method = 'GET';
            return $this;

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
    *   Set request method to POST
    *   @return Resource
    */
    public function post() : Resource {
        try{

            $this->method = 'POST';
            return $this;
        
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
    *   Set request method to PUT
    *   @return Resource
    */
    public function put() : Resource {
        try{
            
            $this->method = 'PUT';
            return $this;

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
    *   Set request method to DELETE
    *   @return Resource
    */
    public function delete() : Resource {
        try{
            
            $this->method = 'DELETE';
            return $this;
            
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }


    /**
     * Middleware performs generic process before resource are loaded up
     *  Middleware class are namespaced under middleware
     * @param string $middleware
     * @return Resource
     */
    public function middleware(string ...$middlewareResource) : Resource {

        try {

            foreach ($middlewareResource as $middleware){
                if(!empty($middleware))
                    array_push($this->middleware, $middleware);
                else
                    continue;
            }

            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }

    }


    /**
     * Other names the resource can be called
     *
     * @param string $alias
     * @return Resource
     */
    public function alias(string ...$alias) : Resource {

        try {

            foreach ($alias as $otherName){

                $otherName = trim($otherName);

                /**Ensure route as a key or name or non-empty alias, empty string would then be mapped to *** anf vice-versa*/
                if(strlen($otherName) == 0)
                        $otherName = '*/*';

                array_push($this->alias, $otherName);
            }

            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }

    }

    /**
     * Deny Request for only specific IPs or all IPs
     *
     * @param string $deniedIp
     * @return resource
     */
    public function deny(string ...$deniedIp ) : Resource {

        try {

            foreach ($deniedIp as $IP){
                if (!empty($IP))
                    array_push($this->denials, $IP);
            }

            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Accept Request Only from specific IPs or all IPs
     *
     * @param string $allowedIp
     * @return Resource
     */
    public function allow( string ...$allowedIp ) : Resource {

        try {

            foreach ($allowedIp as $IP){
                if(!empty($IP))
                    array_push($this->allowed, $IP);
            }

            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * @return Resource
     */
    public function asView() : Resource {
        try {

            $this->as_view = true;
            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * @param int $period
     * @return Resource
     */
    public function trial(int $period ) : Resource
    {
        try {

            $this->trial = $period;
            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);

        }
    }

    /**
     * @param array ...$data
     * @return Resource
     */
    public function data(array ...$data) : Resource
    {
        try{

            foreach ( $data as $datum ){
                array_push($this->data, $datum);
            }

            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }


    /*
    *   Getters
    **/


    public function getData() : array {
        try{

            return $this->data;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }



    public function getMiddleware() : array {
        try{

            return $this->middleware;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function getDenials() : array {
        try{

            return $this->denials;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function getAllowance() : array {
        try{

            return $this->allowed;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function getAlias() : array {
        try{

            return $this->alias;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function getAsView() : bool {
        try{

            return $this->as_view;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function getTrial() : int {
        try{

            return $this->trial;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function getResourceContext() : string {
        try{

            return $this->resource;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function getMethod() : string {
        try{

            return $this->method;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }
}
