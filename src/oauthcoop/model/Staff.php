<?php
namespace src\oauthcoop\model;
use \zil\factory\Model;

class Staff{

    use Model;

	public $firstname = null;
	public $lastname = null;
	public $email = null;
	public $password = null;
	public $IPPIS_NO = null;
	public $religion = null;
	public $denomination = null;
	public $residential_address = null;
	public $oauth_file_no = null;
	public $Staff_rank = null;
	public $department = null;
	public $last_seen = null;
	public $id = null;
	public $created_at = null;

	public static $table = 'Staff';


    public function __construct(){}
    
}
    
