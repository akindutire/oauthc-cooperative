<?php
namespace zil\security;	

	use zil\core\config\Config;
	use zil\core\exception\UnexpectedCaseException;
	use zil\core\error\Error;

	use zil\factory\Database;
	use zil\factory\BuildQuery;
	use zil\factory\Session;
	 
	use zil\core\tracer\ErrorTracer;
	
	class Authentication extends Config{
		
		private $authdata = [ [] ];
		private $source = null;
		private $mode = null;

		/**
		 * Set Auth Params
		 * Source could be a database table or a JSON file, 
		 * Mode could be SQL or JSON
		 * Auth data is a nested array, each member is isotopic to [keyOnSource, dataToMatchWithSourceKey]
		 * 
		 * @param array $authdata
		 * @param string $source
		 * @param string $mode
		 * 
		 */
		public function __construct(array $authdata = [ [] ], string $source, ?string $mode){
			
			$this->authdata = $authdata;
			$this->source = $source;

			if(is_null($mode))
			    $mode = "sql";

			$this->mode = $mode;

		}

		public function Auth(): ?string {

			try{
				(new Session())->deleteEncoded('AUTH_CERT');
				
				if(is_null($this->source))
					return null;

				if(strtoupper($this->mode) == 'JSON'){

					$authObj=json_encode(file_get_contents($this->source));
					$valid = true;
					foreach($this->authdata as $k => $authToken){

						if($authObj->{$authToken[0]} != $authToken[1]){
							$valid = false;
							break;
						}
					}

					if($valid == true){

						if(function_exists('sodium_bin2hex')){
							$byte_key = sodium_bin2hex(random_bytes(SODIUM_CRYPTO_AUTH_KEYBYTES));
						}else{
							$byte_key = bin2hex(random_bytes(64));
						}
						
						(new Session())->build('AUTH_CERT', $byte_key, true);
						return $byte_key;
					}

					return null;

				}else if(strtoupper($this->mode) == 'SQL'){

					$hash = []; 
					$first = false;
					
					foreach($this->authdata as $k => $authToken){

						if(count($authToken) > 2){
							if($first == false){
								$first = true;
								array_unshift($hash, $authToken);
							}
							unset($this->authdata[$k]);
						}
					}
					
					
					$connect_handle = (new Database())->connect();
					$sql = new BuildQuery($connect_handle);

					if(count($hash) == 0)
						$rs = $sql->read($this->source, $this->authdata, [], ['LIMIT 1']);
					else
						$rs = $sql->read($this->source, $this->authdata, [ $hash[0][0] ], ['LIMIT 1']);
					
					
					
					if($rs->rowCount() ==  1){
						
						if(function_exists('sodium_bin2hex')){
							$byte_key = sodium_bin2hex(random_bytes(SODIUM_CRYPTO_AUTH_KEYBYTES));
						}else{
							$byte_key = bin2hex(random_bytes(64));
						}


						if(count($hash) == 0){
							(new Session())->build('AUTH_CERT', $byte_key, true);
							return $byte_key;
						}else{

							list($saved_hashed) = $rs->fetch();

							if( (new Encryption())->hashVerify($hash[0][1], $saved_hashed) ){
								(new Session())->build('AUTH_CERT', $byte_key, true);
								return $byte_key;
							}else{
								return null;
							}
						}
					}else{
						return null;
					}
				}
			}catch(\InvalidArgumentException $t){
				new ErrorTracer($t);
			}catch(\LogicException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public static function Destroy() : bool{

			try{
				
				Session::deleteEncoded('APP_CERT');

				return true;

				
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}

		}
	}
?>