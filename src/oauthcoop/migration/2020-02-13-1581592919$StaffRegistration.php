<?php
namespace src\oauthcoop\migration;

use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:StaffRegistration->Staff []
*/
class StaffRegistration implements Migration{

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

        $schema->build('id')->Primary()->Integer()->AutoIncrement();
        $schema->build('firstname')->String();
        $schema->build('lastname')->String();
        $schema->build('email')->String()->Unique();
        $schema->build('password')->String();
        $schema->build('IPPIS_NO')->String()->Unique();   
        $schema->build('religion')->String();
        $schema->build('denomination')->String();
        $schema->build('residential_address')->String();
        $schema->build('oauth_file_no')->String();
        $schema->build('rank')->String();
        $schema->build('department')->String();
        $schema->build('last_seen')->Timestamp();
        $schema->build('created_at')->Timestamp()->Default("NOW()");
    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){

    }
}
    