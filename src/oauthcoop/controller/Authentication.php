<?php
namespace src\oauthcoop\controller;

use \zil\core\server\Param;
use \zil\core\server\Response;
use \zil\factory\View;
use \zil\core\facades\helpers\Notifier;
use \zil\core\facades\helpers\Navigator;
use \zil\core\facades\decorators\Hooks;

use src\oauthcoop\Config;

/**
 *  @Controller:Authentication []
*/

class Authentication{

    use Notifier, Navigator, Hooks;

    public function __construct(){}
    public function onInit(Param $param){}
    public function onAuth(Param $param){}
    public function onDispose(Param $param){}

}
    