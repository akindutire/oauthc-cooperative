<?php 
	namespace zil\security;
	
	use zil\core\config\Config;
	use zil\core\exception\UnexpectedCaseException;
	use zil\core\error\Error;
 	use zil\core\tracer\ErrorTracer;
    use zil\factory\Logger;

    class Encryption extends Config{



		public function __construct(){

		}

		public function hash(string $data, int $securitylevel = null) : string {

			try{

			    if(is_null($securitylevel))
			        $securitylevel = 11;

				if ($securitylevel > 31){
				
					$securitylevel = 31;
				
				}else if ($securitylevel < 4) {
				
					$securitylevel = 4;
				
				}

				$option = ['cost' => $securitylevel ];

					$data = $this->Encode($data);

					if($hashed = password_hash($data,PASSWORD_BCRYPT,$option)){

						if (password_needs_rehash($hashed,PASSWORD_BCRYPT,$option)) {
							
							$new_result = password_hash($data,PASSWORD_BCRYPT,$option);	

							return $new_result;
						
						}else{
							return $hashed;
						
						}
					
					}else{

						throw new \Exception("Encryption Fails");

					}
			
			}catch(\Throwable | UnexpectedCaseException $t){
				new ErrorTracer($t);
			}
		}

		public function hashVerify( string $userpassword,  string $knownhash){
			try{

				$userhash = $this->Encode($userpassword);
				if(password_verify($userhash,$knownhash) == true)
					return true;
				
				return false;
			}catch(\Throwable | UnexpectedCaseException $t){
				new ErrorTracer($t);
			}

		}

		private function primeSwitcher(  string $string) :  string{

			try{

				$secret 	= 	$string;
				$count 		= 	0;
				$A 			= 	[1,1,1];

				for($j=$A[2]; $j < strlen($string); $j++){
					
					for ($i=$A[2]; $i < strlen($string); $i++) { 
						
						$is_prime = pow(2,($i-1)) % $i;
						if($is_prime == 1){

							if ($count <= 1) {

								$A[$count] = $i;
						
								/*"Sees $i as prime";*/
								$count+=1;
							}else{

								/* Prime Count Exceeded";*/
								break;
							}
						}else{

							/*echo "Not Prime Skipped $i <br>";*/
							continue;
						}
					}
					
					if ($A[1] != null) {	

						/*$A[1]."  Was saved as the Conjugate Prime to be last prime seen<br>";*/
						/*Next Iteration Start From last Prime saved*/
						
						$tmp = $string[$A[0]];
						
						$string[$A[0]] = $string[$A[1]];
						$string[$A[1]] = $tmp;
						
						$A[2] =  $A[1];
						$A[1] =  null;
						$A[0] =  null;
						
						$count = 0;
					}else{

						/*"No Conjugate Prime Found, No Need for swaps";*/
						break;
					}
				}

				return $secret.base64_encode("XX").$string;
			}catch(\Throwable | UnexpectedCaseException $t){
				new ErrorTracer($t);
			}

		}


		private function primeDeSwitcher( string $string) :  string{


			$length_of_original_key = strpos(trim($string),base64_encode("XX"));

			$DS = substr(trim($string),0,$length_of_original_key);

            return $DS;

		}

        private function MapChars($data) : string{

			try{
				/**
				 * /,\,'," are none acceptable characters, it is nullified by default
				 */

				$data = str_replace("\"",null,$data);
				$data = str_replace("\\",null,$data);
				$data = str_replace("/",null,$data);
				$data = str_replace("'",null,$data);

				$enc_table = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789 .,!@#$%^&*()_-+=-|?[]{}:;<>';

				$to_be_encoded = $data;
				$new_encoded = null;

				$pos = null;
				for($i=0; $i<strlen($data); $i++){

					$char_pos = strpos($enc_table,$to_be_encoded[$i]);
					if ($char_pos !== false) {

						for($j=0; $j<strlen($enc_table); $j++){
							if($to_be_encoded[$i]===$enc_table[$j]){

								$pos = -$j;

								$new_encoded .= $enc_table[$pos];
							}else{
								continue;
							}
						}

					}else{
						continue;
					}
				}

				if (empty($new_encoded)) {
					$new_encoded = $to_be_encoded;
				}

				return $new_encoded;
			
			}catch(\Throwable | UnexpectedCaseException $t){
				new ErrorTracer($t);
			}
        }

		public function Encode( string $data ) : string{

            $encoded = $this->primeSwitcher($this->MapChars($data));

            return $encoded;

		}


		public function Decode( string $data ) : string{

		    $decoded = $this->MapChars($this->primeDeSwitcher($data));
            
            return $decoded;

		}

	
		public function decrypt($encrypted,$key){

			#future
		}


		public function authKey() : string{

			if(function_exists('sodium_bin2hex'))
				return sodium_bin2hex( random_bytes(128) );
			else
				return bin2hex( random_bytes(128) );

		}

		public function generateShortHash() : string{
			
			if(function_exists('sodium_crypto_shorthash')){
				$bin_hash = sodium_crypto_shorthash( time(), random_bytes(SODIUM_CRYPTO_SHORTHASH_KEYBYTES) );
				return sodium_bin2hex( $bin_hash );
			}else{
				return bin2hex( random_bytes(128) );
			}

		}

	}
?>
