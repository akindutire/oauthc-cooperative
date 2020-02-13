<?php
namespace src\oauthcoop\migration;

use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:drop_last_Seen_on_staff->Staff []
*/
class drop_last_Seen_on_staff implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('Staff');
        $schema->destroy('last_seen');
    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){

    }
}
