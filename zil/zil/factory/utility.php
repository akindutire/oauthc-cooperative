<?php


namespace zil\factory;


use zil\core\config\Config;
use zil\core\facades\helpers\Navigator;
use zil\core\tracer\ErrorTracer;

class Utility extends Config
{

    use Navigator;

    private static $fnBox = [];
    private const UTIL_FN_PROC_LIST_URL = "core/facades/helpers/util.php";

    public function __construct()
    {

    }

    /**
     * Stack/Register an utility function to be used in future
     * @param string $name
     * @param \Closure $fn
     * @return bool
     */
    public function stack(string $name, \Closure $fn) : bool {
        try{

            if(isset(self::$fnBox[$name]))
                throw new \Exception("Function already exist");

            self::$fnBox[$name] = $fn;

            return true;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Call a stacked utility
     * @param string $util
     */
    public function call(string $util){
        try {
            if (!isset(self::$fnBox[$util]))
                throw new \Exception("Utility not found");

            if( is_callable(self::$fnBox[$util]) ) {

                $callable = self::$fnBox[$util];
                $callable();

            } else {
                throw new \Exception("$util is not a function or callable, ".gettype(self::$fnBox[$util])." parsed");
            }

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Remove/Unstack an utility
     * @param string $util
     * @return bool
     */
    public function unstack(string $util) : bool {
        try{

            if(isset(self::$fnBox[$util])){
                unset(self::$fnBox[$util]);
                return true;
            }

            return false;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**Return Ip of current user
     * @return string
     */
    public static  function getIp() : string {
        try{

            return (new self())->ipDetect();

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }


    /**
     * Resolve internal URI
     * @param string $route
     * @return string
     */
    public static function route(string $route) : string {
        try {

            $SysPath = (new parent())->getSysPath();
            include_once $SysPath. DIRECTORY_SEPARATOR. self::UTIL_FN_PROC_LIST_URL;

            return \route($route);

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Assets FS
     * @param $resource
     * @return string
     */
    public static function asset($resource) : string {
        try {

            $SysPath = (new parent())->getSysPath();
            include_once $SysPath. DIRECTORY_SEPARATOR. self::UTIL_FN_PROC_LIST_URL;
            return \asset($resource);

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
     * Generate http response report, such as 404, 401,...
     * @param int $reportType
     */
    public static function report(int $reportType){
        try {

            $SysPath = (new parent())->getSysPath();
            include_once $SysPath. DIRECTORY_SEPARATOR. self::UTIL_FN_PROC_LIST_URL;
            \report($reportType);

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
     * Shared FS files location
     * @param $resource
     * @return string
     */
    public static function shared($resource) : string {
        try {

            $SysPath = (new parent())->getSysPath();
            include_once $SysPath. DIRECTORY_SEPARATOR. self::UTIL_FN_PROC_LIST_URL;
            return \shared($resource);

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**Resource link particularly in uresource folder
     * @param $resource
     * @return string
     */
    public static function uresource($resource) : string {
        try {

            $SysPath = (new parent())->getSysPath();
            include_once $SysPath. DIRECTORY_SEPARATOR. self::UTIL_FN_PROC_LIST_URL;
            return \uresource($resource);

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }


}
