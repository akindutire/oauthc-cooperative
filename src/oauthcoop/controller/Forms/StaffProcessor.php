<?php

namespace src\oauthcoop\controller;

use mysql_xdevapi\Exception;
use zil\core\facades\decorators\Hooks;
use zil\core\facades\helpers\Navigator;
use zil\core\facades\helpers\Notifier;
use zil\core\server\Param;
use zil\factory\Logger;
use zil\factory\View;
use zil\security\Encryption;
use zil\security\Validation;

/**
 * @Controller:StaffProcessor []
 */
class StaffProcessor
{

    use Notifier, Navigator, Hooks;


    public function SubmitStaffRegistration(Param $param)
    {
        try {
            $V = new Validation(
                ['email', 'email|required'],
                ['password', 'required'],
                ['password_2', 'required'],
                ['ippis_no', 'required']
            );

            if($V->isPassed()){

                $email = $param->form('email');
                $password = $param->form('password');
                $password_2 = $param->form('password_2');
                $ippis_no = $param->form('ippis_no');

                if($password == $password_2){
                    $enc_password = (new Encryption())->hash($password, 15);

                    $Staff = new \src\oauthcoop\model\Staff();


                    $Staff->email = $email;
                    $Staff->password = $enc_password;
                    $Staff->IPPIS_NO = $ippis_no;

                    $Logger = new Logger();
                    $Logger->QInit();
                    if($Staff->create()){

                        $data = ["message" => "Registration completed, please wait for admin confirmation", "status" => true ];
                    }else{
                        throw new \Exception("An error has occurred, please try again later");
                    }
                }else{
                    throw new \Exception("Password don't match");
                }
            }else{
                throw new \Exception($V->getErrorString());
            }

        } catch (\Throwable $t) {
            $data = ["message" => $t->getMessage(), "status" => false ];
        }finally{
            View::render("DisplayStaffRegistrationNotification", $data);
        }
    }

    public function __construct()
    {
    }

    public function onInit(Param $param)
    {
    }

    public function onAuth(Param $param)
    {
    }

    public function onDispose(Param $param)
    {
    }

}
