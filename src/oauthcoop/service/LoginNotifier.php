<?php
namespace src\oauthcoop\service;

use \zil\core\server\Http;
use zil\core\tracer\ErrorTracer;
use \zil\factory\Session;
use \zil\factory\Fileuploader;
use \zil\factory\Filehandler;
use \zil\factory\Logger;
use \zil\factory\Mailer;
use \zil\factory\Redirect;

use \zil\security\Encryption;
use \zil\security\Sanitize;

/**
 * @Service:LoginNotifier []
*/

class LoginNotifier{

    public function __construct(){ }

    public function loginAssert(): bool {
        try{
            Session::build('isLoggedIn', true);
            return true;
        }catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function isLoggedIn() : bool {
        $temp = Session::get('isLoggedIn')
        return is_null($temp) ? false : true;
    }

}
