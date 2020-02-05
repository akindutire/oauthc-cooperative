<?php
namespace src\oauthcoop\route;

use \zil\core\interfaces\Route;
use \zil\core\server\Resource;

/**
 *   @Route:Api
*/

class Api implements Route{

    use \zil\core\facades\decorators\Route_D1;

    /**
     * Api routes
    *
    * @return array
    */
    public function route(): array{
        return [];
    }
}

    