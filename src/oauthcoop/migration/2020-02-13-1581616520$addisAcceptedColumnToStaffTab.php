<?php
namespace src\oauthcoop\migration;

use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addisAcceptedColumnToStaffTab->Staff []
*/
class addisAcceptedColumnToStaffTab implements Migration{

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

        $schema->build('isAccepted')->Boolean()->Default('0');
    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){

    }
}
