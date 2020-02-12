<?php
namespace src\oauthcoop\controller;

use \zil\core\server\Param;
use \zil\core\server\Response;
use \zil\factory\View;
use \zil\core\facades\helpers\Notifier;
use \zil\core\facades\helpers\Navigator;
use \zil\core\facades\decorators\Hooks;

use src\oauthcoop\Config;
use src\oauthcoop\model\Feedback;

/**
 *  @Controller:Admin []
*/

class Admin{

    use Notifier, Navigator, Hooks;

    public function Login(Param $param){

        $OutputData = [
            'feedback' => (new Feedback())->readOutData(),
            'title' => 'Admin'
        ];

        // var_dump("<pre>", $OutputData, "</pre>");
        // die();

        #render the desired interface inside the view folder

        View::render("Admin/Login.php", $OutputData);
    }

    public function __construct(){}
    public function onInit(Param $param){}
    public function onAuth(Param $param){}
    public function onDispose(Param $param){}

}
