<?php
namespace src\oauthcoop\model;

use zil\core\tracer\ErrorTracer;
use \zil\factory\Model;

class Feedback{

    use Model;

	public $id = null;
	public $email = null;
	public $message = null;
	public $subject = null;
	public $isRead = null;
	public $created_at = null;

	public static $table = 'Feedback';


	public function __construct(){}
	
	public function readOutData() : array {
		try{

			return $this->get('VERBOSE');

		} catch (\Throwable $t){
			new ErrorTracer($t);
		}
	}
    
}
    
