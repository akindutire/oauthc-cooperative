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
 *  @Controller:Staff []
*/

class Staff{

    use Notifier, Navigator, Hooks;

    public function Registraion(Param $param){

        $OutputData = ['title' => "Registration",];

        #render the desired interface inside the view folder
        // View::raw("Staff/Registraion.php");
        View::render("Staff/Registraion.php", $OutputData);
    }

    public function Login(Param $param){

        $OutputData = [];

        #render the desired interface inside the view folder
        // View::raw("Staff/Login.php");
        View::render("Staff/Login.php", $OutputData);
    }

    public function __construct(){}
    public function onInit(Param $param){}
    public function onAuth(Param $param){}
    public function onDispose(Param $param){}

}