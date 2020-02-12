<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\security;

use zil\core\tracer\ErrorTracer;

class Sanitize{

	public function __construct(){

	}

	/**
	 * Cleanse data entries
	 *
	 * @param array $data
	 * @param string $acceptable_tags
	 * @return array
	 */
	public static function clean(array $data=[], string $acceptable_tags = '') : array {


		try{

			$data_cleansed 	= 	null;

			if (is_array($data) && count($data)!=0) {
				$new_data_set 	= 	[];

				foreach ($data as $value) {

					$value = trim($value);
					if(is_bool($value))
						$data_cleansed = $value;	
					

					if(!is_null($value)){
						if(is_string($value))
							$data_cleansed = strip_tags($value,$acceptable_tags);
					}else{
						$data_cleansed = $value; 
					}

					if(is_int($value))
						$data_cleansed = (int)filter_var($value,FILTER_SANITIZE_NUMBER_INT);
					
					if (is_resource($value) === true) {
						$data_cleansed = filter_var($value,FILTER_SANITIZE_URL);
						$data_cleansed = filter_var($value,FILTER_SANITIZE_EMAIL);	
					}

					//Queue
					array_push($new_data_set, $data_cleansed);
				}

				return $new_data_set;

			}else{

				throw new \Exception(" Expect non-empty Array as parameter 1");

			}
		}catch(\Throwable $t){
			new ErrorTracer($t);
		}
	}
}
?>