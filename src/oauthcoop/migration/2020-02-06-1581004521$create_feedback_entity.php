<?php
namespace src\oauthcoop\migration;

use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:create_feedback_entity->Feedback []
*/
class create_feedback_entity implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('Feedback');

        $schema->build('id')->Primary()->Integer()->AutoIncrement();
        $schema->build('email')->String()->NotNull();
        $schema->build('message')->String()->NotNull();
        $schema->build('subject')->String()->NotNull();
        $schema->build('isRead')->Boolean()->Default('0');
        $schema->build('created_at')->Timestamp();
    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){

    }
}
    