<?php
/**
 * 
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\factory;

use zil\core\config\Config;
use zil\core\tracer\ErrorTracer;
use zil\core\scrapper\Info;

	class BuildQuery extends Config{

		private $Instance = null;
		private static $connection_handle 	= 	null;
		private $lastLogicalOfConditionString 	=	null;
		private $ConditionString 	= 	null;
		private $ConditionValue 	= 	[];

		public function __construct($connection_handle){
			try{
				if($connection_handle != null)
					self::$connection_handle = $connection_handle;
				else
					throw new \Exception("Couldn't initialize CRUD Class, no database resource found");
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

        /**
         * @return BuildQuery
         */
        private function getInstance() : self {
			if($this->Instance == null)
				$this->Instance = new self(self::$connection_handle);

			return $this->Instance;		
		}

		/**
		 * create new data
		 *
		 * @param string $table
		 * @param array| $data
		 * @return boolean|null
		 */
		public function create(string $table, array $data = []): ?bool{
			try {
				if(is_null(self::$connection_handle))
					throw new \PDOException("Database Resource not found");
				
				if(!is_array($data))
					throw new \Exception("Argument #2 expect an array, ".gettype($data)." given");
					
				$i=1; 
				$variable_space = null;
				while ($i <= sizeof($data)) {
					$variable_space.='?,';
					$i++;
				}

				$variable_space = rtrim($variable_space,',');
				$query = "INSERT INTO $table VALUES($variable_space)";
				Logger::QLog($query);
						
				$rs = self::$connection_handle->prepare($query);
				$rs->execute($data);

				Info::$_dataLounge["zdx_0xc4_last_insert_into_{$table}"] = self::$connection_handle->lastInsertId();
				// Session::build( "zdx_0xc4_last_insert_into_{$table}", self::$connection_handle->lastInsertId(), true  );

				if ($rs->rowCount() == 1){	
					return true;
				}else{
					throw new \PDOException();
				}
			}catch(\Exception $e){
				new ErrorTracer($e);
			}catch(\PDOException $e){
				new ErrorTracer($e);
			}catch (\Throwable $e) {
				new ErrorTracer($e);
			}
			return null;
		}

        /**
         * @param string $table
         * @param array $data
         * @param array $data_field_to_select
         * @param array $extra
         * @return object
         */
        public function read(string $table, array $data = [], array $data_field_to_select=[], array $extra=[]) : object {
			try{
				if(is_null(self::$connection_handle))
					throw new \PDOException("Database Resource not found");

				if(!\is_array($data))
					throw new \Exception("Argument #2 expect a 2D array, ".gettype($data)." given");
					
				if(!\is_array($data_field_to_select))
					throw new \Exception("Argument #3 expect a 1D array, ".gettype($data_field_to_select)." given");
				
				if(!\is_array($extra))
					throw new \Exception("Argument #4 expect a 1D array, ".gettype($extra)." given");

				$field_to_select = '*';
				if (sizeof($data_field_to_select) != 0) {
					if(!is_string($data_field_to_select[0]))
						throw new \Exception("Argument #3 elements expect a string, ".gettype($data_field_to_select)." given");

					$field_to_select = implode(',', $data_field_to_select);
					$field_to_select = rtrim($field_to_select,',');
				}

				$extra_query =  sizeof($extra) != 0 ? $extra[0] : null;
				$query = "SELECT {$field_to_select} FROM {$table} {$extra_query}";
				$ConditionAndValue = ['condition'=>'','value'=>[]];
				if ( sizeof($data) != 0 ) {
					$ConditionAndValue = ($this->getInstance())->extractCondition($data);
					$condition = $ConditionAndValue['condition'];
					$query = "SELECT {$field_to_select} FROM {$table} WHERE {$condition} {$extra_query}";
				}

				Logger::QLog($query);
				
				$rs = self::$connection_handle->prepare($query);
				if ($rs->execute($ConditionAndValue['value']) !== false){
					unset($ConditionAndValue);

					return $rs;
				}else{
					throw new \PDOException("Error: Couldn't execute Query");
				}
			}catch(\Exception $e){
				new ErrorTracer($e);
			}catch(\PDOException $e){
				new ErrorTracer($e);
			}catch (\Throwable $e) {
				new ErrorTracer($e);
			}
			return null;
		}

        /**
         * @param string $table
         * @param array|null $data
         * @param array|null $data_field_to_update
         * @param array|null $extra
         * @return object
         */
        public function update(string $table, ?array $data=[ [ [ ] ] ], ?array $data_field_to_update=[ [] ], ?array $extra=[]) : object {
			
			try{
				if(is_null(self::$connection_handle))
						throw new \PDOException("Database Resource not found");

				if(!\is_array($data))
					throw new \Exception("Argument #2 expect an array, ".gettype($data)." given");
					
				if(!\is_array($data_field_to_update))
					throw new \Exception("Argument #3 expect an array, ".gettype($data_field_to_update)." given");
				
				if(!\is_array($extra))
					throw new \Exception("Argument #4 expect an array, ".gettype($extra)." given");

				$field_to_update = null;
				$UpdateVal = [];
				foreach ($data_field_to_update as $field_update_array) {
					if (!is_array($field_update_array) && sizeof($field_update_array) != 2)
						throw new \Exception("SQL Error: Expecting Nested Array as Arguement, Expecting two(2) parameters");
							
					$field_to_update.=	"{$field_update_array[0]} = ?,";
					array_push($UpdateVal, $field_update_array[1]);				
				}
				$field_to_update = rtrim($field_to_update,",");

				$extra_query =  sizeof($extra) != 0 ? $extra[0] : null;
				
				$query = "UPDATE {$table} SET {$field_to_update} {$extra_query}";
				
				$ConditionAndValue = ['condition'=>'','value'=>[]];
				if (sizeof($data) != 0 ) {
					$ConditionAndValue = ($this->getInstance())->extractCondition($data);
					$condition = $ConditionAndValue['condition'];
					$query = "UPDATE {$table} SET {$field_to_update}  WHERE {$condition} {$extra_query}";
				}

				Logger::QLog($query);
					
				$rs = self::$connection_handle->prepare($query);
				
				
				if ($rs->execute(  array_merge($UpdateVal, $ConditionAndValue['value'])  ) != false){
					unset($ConditionAndValue, $UpdateVal);
					return $rs;
				}else{

					throw new \PDOException("Couldn't execute Query");
				}

			}catch(\PDOException $e){
				new ErrorTracer($e);

			}catch (\Exception $e) {
				new ErrorTracer($e);

			}catch(\Throwable $e){	   
                new ErrorTracer($e);

			}

			return null;
            
		}

		/**
		 * Remove a row in db table
		 *
		 * @param string $table
		 * @param array|null $data
		 * @param array|null $extra
		 * @return object
		 */
		public function delete(string $table, ?array $data=[ [ [ ] ] ], ?array $extra=[]) : object {
		
			try{

				if(is_null(self::$connection_handle))
					throw new \PDOException("Database Resource not found");

				if(!\is_array($data))
					throw new \Exception("Argument #2 expect an array, ".gettype($data)." given");
				
				if(!\is_array($extra))
					throw new \Exception("Argument #3 expect an array, ".gettype($extra)." given");

				$extra_query =  sizeof($extra) != 0 ? $extra[0] : null;

				$query = "DELETE FROM {$table} {$extra_query}";	
				
				$ConditionAndValue = ['condition'=>'','value'=>[]];

				if (sizeof($data) != 0 ) {
					$ConditionAndValue = ($this->getInstance())->extractCondition($data);
					$condition = $ConditionAndValue['condition'];
					$query = "DELETE FROM $table WHERE $condition $extra_query";
				}

				Logger::QLog($query);

				$rs = self::$connection_handle->prepare($query);
				
				if ($rs->execute($ConditionAndValue['value']) != false){
					unset($ConditionAndValue);
					return $rs;
				}else{
					throw new \PDOException("Error: Couldn't execute Query");					
				}
				

			}catch(\PDOException $e){
				new ErrorTracer($e);

			}catch (\Exception $e) {
				new ErrorTracer($e);

			}catch(\Throwable $e){	   
                new ErrorTracer($e);

			}
		
			return null;
		}

		/**
		 * Empty a database table
		 *
		 * @param string $table
		 * @return object
		 */
		public function truncate(string $table) : object {


			try {
				
				$query = "TRUNCATE {$table}";

				if(is_null(self::$connection_handle))
					throw new \PDOException("Database Resource not found");

                Logger::QLog($query);
               
                $rs = self::$connection_handle->prepare($query);
               
                if ($rs->execute() != false){

                    return $rs;
                }else{

					throw new \PDOException("Error: Couldn't execute Query");
                }

            }catch(\PDOException $e){
				new ErrorTracer($e);

			}catch (\Exception $e) {
				new ErrorTracer($e);

			}catch(\Throwable $e){	   
                new ErrorTracer($e);

			}
			
			return null;
		}

		/**
		 * Provide columns of table
		 *
		 * @param [type] $table
		 * @return array
		 */
		public function getTableCols(string $table) : array {
            try{

                if(!empty($table)){
                    
                    $db = (new parent())->dbParams;
                    $pdohandle = (new Database())->connect();

                    
                    if($db['driver'] == 'mysql' || $db['driver'] == 'pgsql' || $db['driver'] == 'mssql'){

                        $rs = $pdohandle->query("SELECT COLUMN_NAME FROM  INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='{$db['database']}' AND TABLE_NAME = '{$table}'");
                    }else if($db['driver'] == 'sqlite'){
                        
                        $rs = $pdohandle->query("SELECT name FROM pragma_table_info('$table')");
                    }else if($db['driver'] == 'oracle'){
                    
                        $rs = $pdohandle->query("SELECT COULUMN_NAME FROM  ALL_TAB_COLUMNS WHERE TABLE_NAME = '{$table}'");
                    }

                    $ModelAttribute = [];
                    if($rs->rowCount() > 0){
                        while( list($col) = $rs->fetch() ){
                            array_push($ModelAttribute, $col);
                        }
                    }	

                    return $ModelAttribute;

                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
		}
		
		/**
		 * Format Conditions specified into string
		 *
		 * @param array $array
		 * @param integer $index
		 * @return void
		 */
		private function extractConditionEx(array $array, int $index = 0) : void{
			
			try{
				$array = array_values($array);

				if(!is_array($array))
					throw new \DomainException(" [INTERNAL ERROR] Args? #1 of data condition not an array");	
				
				if(sizeof($array) > 0){

					$defaultLogicalOperator = "AND";

					while($index < sizeof($array)){

						if(!isset($array[$index]))
							break;

						if(is_array($array[$index]) && sizeof($array[$index]) != 0){


							/**
							*Child Array length must be more than 2 otherwise IGNORE
							*/
                            $sub_array = $this->checkArray($array[$index]);

							if ($sub_array != false) {
								
								$this->ConditionString .= " (";
								$this->extractConditionEx($sub_array,0);
								
							}else{

								if (sizeof($array) == 1) {
								
									if (sizeof($array[$index])  ==  2) {

										$this->ConditionString .= "{$array[$index][0]} = ? ";
										array_push($this->ConditionValue, $array[$index][1]);

									}else if (sizeof($array[$index]) > 2) {
										$this->ConditionString .= "{$array[$index][0]} {$array[$index][1]} ? ";
										array_push($this->ConditionValue, $array[$index][2]);

									}else{

                                        $this->ConditionString = null;
										throw new \DomainException(" [INTERNAL ERROR] Child Condition Array expect least of two (2) elements.");

									}

								}else{

									$logicalOperatorsArray = ["OR","AND","NOT","XOR","NAND"];
									if (sizeof($array[$index])  ==  2) {

										$this->ConditionString .= "{$array[$index][0]} = ? AND ";
										
										array_push($this->ConditionValue, $array[$index][1]);
										$this->lastLogicalOfConditionString = $defaultLogicalOperator;
									
									}else if (sizeof($array[$index]) == 3) {
										
										if ($index != sizeof($array)) {
											
											if (!in_array($array[$index][2], $logicalOperatorsArray) ) {

												$this->ConditionString .= "{$array[$index][0]} {$array[$index][1]} ? $defaultLogicalOperator ";
												array_push($this->ConditionValue, $array[$index][2]);
												$this->lastLogicalOfConditionString = $defaultLogicalOperator;

											}else{

												$this->ConditionString .= "{$array[$index][0]} = ? {$array[$index][2]} ";
												array_push($this->ConditionValue, $array[$index][1]);
												$this->lastLogicalOfConditionString = $array[$index][2];
											}

										}else{

											if (in_array($array[$index][2], $logicalOperatorsArray) === true) {
												
												$this->ConditionString .= "{$array[$index][0]} = ? ";
												array_push($this->ConditionValue, $array[$index][1]);
												$this->lastLogicalOfConditionString = $array[$index][2];
											}else{
												
												$this->ConditionString = "{$array[$index][0]} ? {$array[$index][2]} ";
												array_push($this->ConditionValue, $array[$index][1]);
												$this->lastLogicalOfConditionString = $defaultLogicalOperator;
											}
										}

									}else if (sizeof($array[$index]) > 3) {
										
										if ($index != sizeof($array)) {
											$this->ConditionString .= "{$array[$index][0]} {$array[$index][1]} ? {$array[$index][3]} ";
											array_push($this->ConditionValue, $array[$index][2]);										
											$this->lastLogicalOfConditionString = $array[$index][3];

										}else{
											$this->ConditionString .= "{$array[$index][0]} {$array[$index][1]} ? ";
											array_push($this->ConditionValue, $array[$index][2]);
											$this->lastLogicalOfConditionString = $defaultLogicalOperator;
										}

									}else{

										$this->ConditionString = null;
										throw new \DomainException(" [INTERNAL_ERROR] Child Condition Array expect least of two (2) elements.");
										
									}

								}/*Endif*/					
							}/*Endif*/	

						}/*Endif*/


						
						/* #increment index*/
						$index += 1;

					}/*Endwhile*/
					
					
					$this->ConditionString = rtrim(trim($this->ConditionString),$this->lastLogicalOfConditionString);
					
					
					$this->ConditionString .= ") {$this->lastLogicalOfConditionString} ";

					return;
				
				}/*Endif*/
			
			}catch(\PDOException $e){
				new ErrorTracer($e);

			}catch (\Exception $e) {
				new ErrorTracer($e);

			}catch (\DomainException $e) {
				new ErrorTracer($e);

			}catch (\InvalidArgumentException $e) {
				new ErrorTracer($e);

			}catch(\Throwable $e){	   
                new ErrorTracer($e);

			}

		}/*EndextractCondition*/ 


		/**
		 * Wrapper of extractConditionEx
		 *
		 * @param array $array
		 * @param integer $index
		 * @return array
		 */
		private function extractCondition(array $array, int $index=0): array{
			
			try{
				$arr = [];

				$this->extractConditionEx($array,$index);

				$this->ConditionString = trim(rtrim(trim($this->ConditionString),"{$this->lastLogicalOfConditionString}"));

				$this->ConditionString[strlen($this->ConditionString)-1] = ' ';


				$arr['condition'] = $this->ConditionString;

				$arr['value'] = $this->ConditionValue;

				$this->ConditionString = null;

				$this->ConditionValue = [];

				return $arr;

			}catch (\Exception $e) {
				new ErrorTracer($e);

			}catch(\Throwable $e){
                new ErrorTracer($e);

			}
		}


		/**
		 * Check if array and nested
		 *
		 * @param array $array
		 * @return boolean | array
		 */
		private function checkArray(array $array){

			try{
				if (is_array($array) and sizeof($array) > 1) {

					$flag = 0;
					foreach($array as $in_array){

						if (is_array($in_array)) {

							$flag = 1;
							break;
						}
					}

					if ($flag == 1)
						return $array;
					else
						return false;

				}else{
					return false;
				}
			}catch (\InvalidArgumentException $e) {
				new ErrorTracer($e);

			}catch(\Throwable $e){	   
                new ErrorTracer($e);

			}
		}


	}

	
?>
