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
 *  @Controller:Home []
*/

class Home{

    use Notifier, Navigator, Hooks;

    public function index(Param $param){
        $OutputData = ['title' => "OAUTHCoop",];

        #render the desired interface inside the view folder
        // View::raw("Staff/Registraion.php");
        View::render("Index.php", $OutputData);
    }

    public function __construct(){}
    public function onInit(Param $param){}
    public function onAuth(Param $param){}
    public function onDispose(Param $param){}

}
    