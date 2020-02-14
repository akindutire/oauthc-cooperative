<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\factory;	

	use \PDO;
	use zil\core\config\Config;
 	use zil\core\tracer\ErrorTracer;
	
	class Database extends Config{

		private static $Instance = null;
        private static $status = null;
		private $link = null;
		private $con_params = [];

		public function __construct(){
			$this->con_params = (new parent())->dbParams;
		}

        private static function getInstance(){
            if(self::$Instance == null)
                self::$Instance = new self;

            return self::$Instance;
        }

		private function databaseDriverDigest( string $driver){
			try{
				$supportedDriversArray	=	PDO::getAvailableDrivers();
				if (in_array($driver, $supportedDriversArray)) 
					$this->driver 	=	$driver;
				else
					throw new \DomainException("Database Driver for {$driver} is not Enabled on this Server, Suggest try Installing it");
			}catch(\Throwable $e){
				self::$status 	= 	$e->getMessage();
				new ErrorTracer($e);
				exit();
			}
		}

        /**
         * @param $con_params
         * @return null|PDO
         */

        private function newConnection( array $con_params ) : ?PDO {
			
			/****
			*	More Development is Needed in Future -Supported Database ARE [Mysql,Sqlite,PgSql]
			*/
			try {
				if (sizeof($con_params) > 0) {	
					if (array_key_exists('driver', $con_params) === false) {
						self::$status 	= 	"Couldn't Establish a Database Connection, Database Params not properly formatted";
						throw new \DomainException("Database Params array Expect a format including the following keys\ndriver\thost\tdatabase\tuser\tpassword\tport\nAnd if Sqlite database Use the following keys\ndriver\tfile\n");
					}else{
						$this->con_params = array_merge($this->con_params,$con_params);
					}
				}
                $this->databaseDriverDigest($this->con_params['driver']);
                $connect_handle = null;
				if ($this->con_params['driver'] == 'mysql') {
						$driver 			= 	$this->con_params['driver'];			
						$Host 		 		=	$this->con_params['host'];
						$DatabaseName 		=	$this->con_params['database'];
						$DatabaseUsername	=	$this->con_params['user'];
						$DatabasePassword	=	$this->con_params['password'];
						$Port 				=	$this->con_params['port'];
						$connect_handle 	= 	new PDO("{$driver}:host={$Host};port={$Port};dbname={$DatabaseName}", "$DatabaseUsername", "$DatabasePassword");
				}else if ($this->con_params['driver'] == 'sqlite') {
						$driver 			= 	$this->con_params['driver'];
						$DbPath 			= 	$this->con_params['file'];
						$connect_handle 	= 	new PDO("$driver:$DbPath");
				}else if ($this->con_params['driver'] == 'pgsql') {
						$DatabaseType 		= 	$this->con_params['driver'];
						$Host 		 		=	$this->con_params['host'];
						$DatabaseName 		=	$this->con_params['database'];
						$DatabaseUsername	=	$this->con_params['user'];
						$DatabasePassword	=	$this->con_params['password'];
						$Port 				=	$this->con_params['port'];
						$connect_handle 	= 	new PDO("{$DatabaseType}:host={$Host} port={$Port} dbname={$DatabaseName} user={$DatabaseUsername} password={$DatabasePassword}");
				}
				if ($connect_handle != null) {
					$connect_handle->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
					$connect_handle->setAttribute(PDO::ATTR_PERSISTENT,TRUE);
					return $connect_handle;
				}else{
					throw new \PDOException("Couldn't Establish a Database Connection", 1);
				}
			} catch (\PDOException $e) {
				new ErrorTracer($e);
			} catch (\DomainException $e){
				new ErrorTracer($e);
			}catch (\InvalidArgumentException $e){
				new ErrorTracer($e);
			} catch(\Throwable  $t){
				new ErrorTracer($t);
			}
		}

        /**
         * @param array $con_params
         * @return PDO|null
         */
        public function connect(array $con_params = [] ){
			try{
				$this->link = (self::getInstance())->newConnection($con_params);
				if ($this->link != false) {
					self::$status 	=	"Connection Established";			
					return $this->link;
				}
				return null;
			}catch(\InvalidArgumentException $t){
				new ErrorTracer($t);
			}catch(\LogicException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function getStatus(){
			return self::$status;
		}

		public function closeConnection(){
			$this->link = null;
		}
	}
?>
