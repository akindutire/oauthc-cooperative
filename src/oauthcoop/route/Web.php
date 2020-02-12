<?php
namespace src\oauthcoop\route;

use \zil\core\interfaces\Route;
use \zil\core\server\Resource;

/**
 *   @Route:Web
*/

class Web implements Route{

    use \zil\core\facades\decorators\Route_D1;

    /**
     * Web routes
    *
    * @return array
    */
    public function route(): array{
        return [
            'admin/login' => (new Resource('Admin@Login'))->alias('')->get(),
            ''
        ];
    }
}

    