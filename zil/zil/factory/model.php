<?php
/**
 * Author: Akindutire Ayomide Samuel
 */	
namespace zil\factory;	

    use zil\core\server\Cache;
    use zil\core\tracer\ErrorTracer;
    use zil\core\scrapper\Info;

    use \PDO;

	trait Model{

        protected $zdx_Take = 0;

        protected static $zdx_conditions = [];
        protected static $zdx_updates = [];
        protected static $zdx_selects = [];
        protected static $zdx_extra = [ 'group' => '' ,'limit' => '', 'order' => '', 'sort' => ''];
        protected static $zdx_relate_to = '';
        protected static $zdx_tbl_alias = '';

        public static $key = '';

		public function __construct(){			
            
        }

        /**
         * Bind key to model
         *
         * @param string $key
         * @return Model
         */
        

        public static function key(string $key):  self {
            try{
                
                self::$key = $key;
                return new self;

            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        /**
         * Create a new record on the model database table
         *
         * @return boolean
         */
        public function create(): bool{

            try{

                if(!empty(self::$table)){
                    //Connect to a particular database engine
                    $db = (new Database())->connect();
                    //Inject database dependency for query building and execution
                    $sql = new BuildQuery($db);
                    //Extract table columns
                    $TableColumns = $sql->getTableCols(self::$table);

                    //Bind data to table columns
                    $new_model_attribs = array_map( function ($col){
                        return ["key"=>$col, "value"=>$this->{$col}];
                    } , $TableColumns );

                    //Build, Prepare and Execute query on data
                    $rs = $sql->create(self::$table,  $new_model_attribs);

                    return $rs;

                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }finally{

               foreach( $sql->getTableCols(self::$table) as $col){
                   $this->{$col} = null;
               }

            }
        }

        /**
         * Get the id of last created record
         *
         * @return integer
         */
        public static function lastInsert() : ?int {

            try{
                $table = self::$table;
                
                return intval(Info::$_dataLounge["zdx_0xc4_last_insert_into_{$table}"]);
            
                // return intval( Session::getEncoded( "zdx_0xc4_last_insert_into_{$table}" ) );
            
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            } 
        }


        /**
         * Terminal method to execute query and retrieve records -[cached]
         *
         * @return object | array
         */
        public function getCached(){
            try{
                if(!empty(self::$table)){
                    
                    $db = (new Database())->connect();
                    $sql = new BuildQuery($db);
                    
                    /**Generate cache key */
                    $dx = null;
                    if( sizeof(self::$zdx_conditions) > 0 ){
                        foreach(self::$zdx_conditions as $d){
                            if(is_array($d))
                                $dx .= implode('_', $d);
                        }
                    }

                    $extra = self::$zdx_extra['group'].' '.self::$zdx_extra['order'].' '.self::$zdx_extra['sort'].' '.self::$zdx_extra['limit'];
                    
                    $raw = preg_replace(['/[\s]+/', '(/|\|\)|\(|@|\}|/)'], ['', ''], self::$table.'.'.self::$zdx_relate_to.'_'.implode(".", self::$zdx_selects ).'_'.$dx.'_'.$extra );
                    $cache_key =  md5($raw); 

                    /**
                    * Cache hit
                    */
                     $Cache = new Cache;
                    if( $Cache->hit($cache_key) )
                        return $Cache->get($cache_key);
                  
                    $rs = $sql->read(self::$table.' '.self::$zdx_tbl_alias.' '.self::$zdx_relate_to  , self::$zdx_conditions, self::$zdx_selects, [ self::$zdx_extra['group'].' '.self::$zdx_extra['order'].' '.self::$zdx_extra['sort'].' '.self::$zdx_extra['limit'] ] );

                    
                    $rs->setFetchMode(PDO::FETCH_OBJ);

                    $data = $rs->fetchAll();

                    /**If single record, extract it */
                    if($rs->rowCount() == 1)
                        $data = $data[0];
                
                
                    /**
                    * Cache miss, then cache data
                    */             
                    if( !$Cache->hit($cache_key) ) 
                        $Cache->set($cache_key, $data);
                        

                    return $data;
                
                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }finally{
                    $dx = null;
                    self::$zdx_relate_to = null;
                    self::$zdx_tbl_alias = null;
                    self::$zdx_conditions = [];
                    self::$zdx_selects = [];
                    self::$zdx_extra = [ 'group' => '' ,'limit' => '', 'order' => '', 'sort' => ''];
            } 
        }


        /**
         * Terminal method to execute query and retrieve records [non-cached]
         *
         * @return object | array
         */
        public function get(?string $type = null){
            try{
                if(!empty(self::$table)){
                    
                    $db = (new Database())->connect();
                    $sql = new BuildQuery($db);
                    
                    $rs = $sql->read(self::$table.' '.self::$zdx_tbl_alias.' '.self::$zdx_relate_to  , self::$zdx_conditions, self::$zdx_selects, [ self::$zdx_extra['group'].' '.self::$zdx_extra['order'].' '.self::$zdx_extra['sort'].' '.self::$zdx_extra['limit'] ] );

                    $rs->setFetchMode(PDO::FETCH_OBJ);

                    $data = $rs->fetchAll();

                    if( strtoupper($type) != 'VERBOSE'){
                        /**If singleton record, extract it */
                        if($rs->rowCount() == 1){
                            $data = $data[0];
                        }
                    }

                    return $data;

                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }finally{
           
                self::$zdx_relate_to = null;
                self::$zdx_tbl_alias = null;
                self::$zdx_conditions = [];
                self::$zdx_selects = [];
                self::$zdx_extra = [ 'group' => '' ,'limit' => '', 'order' => '', 'sort' => ''];
            } 
        }

        

        /**
         * Prospective method to query all records
         *
         * @return self
         */
        public static function all() : self {
            try{

                if(!empty(self::$table)){
                  
                    self::$zdx_selects = [];
                    self::$zdx_conditions = [];
                    
                    return (new self());

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
         * Prospective method to query range of records
         *
         * @param integer $min
         * @param integer $max
         * @return self
         */
        public static function between(int $min, int $max) : self {
            try{

                if(!empty(self::$table)){

                    if(empty(self::$key))
                        throw new \Exception("Lookup key not found, model is not associated with any key");

                    $key = self::$key;

                    self::$zdx_conditions = [  [ $key, '>=', $min], [ $key, '<=', $max ] ];
                    
                    return new self;

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
         * Prospective method to query a single records
         *
         * @param string|int $value
         * @return self
         */
        public static function find($value) : self {
            try{

                if(!empty(self::$table)){

                    if(empty(self::$key))
                        throw new \Exception("Lookup key not found, model is not associated with any key");

                    $key = self::$key;
                    array_push(self::$zdx_conditions, [$key, $value]);
                    return new self;

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
         * Prospective method to return set of fields in record(s)
         *
         * @param string ...$columns
         * @return self
         */
        public static function filter(string ...$columns) : self{
            try{
                if(!empty(self::$table)){
                    
                    foreach($columns as $cols){
                        array_push(self::$zdx_selects,  $cols);
                    }
                    
                    return new self;

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
         * Prospective method to constrain records
         *
         * @param array ...$conditions
         * @return self
         */
        public function where(array ...$conditions) : self {
            try{

                
                if(!empty(self::$table)){
                    foreach($conditions as $condition){
                        array_push(self::$zdx_conditions, $condition);
                    }
                    
                    return $this;

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
         * Terminal method to empty all records
         *
         * @return integer
         */
        public static function empty() :int {
             try{
                if(!empty(self::$table)){
                    
                    $db = (new Database())->connect();
                    $sql = new BuildQuery($db);

                    $rs = $sql->truncate(self::$table);

                    
                    return $rs->rowCount();
                
                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }finally{
                
                self::$zdx_relate_to = null;
                self::$zdx_conditions = [];
                self::$zdx_selects = [];
                self::$zdx_extra = [ 'group' => '' ,'limit' => '', 'order' => '', 'sort' => ''];
    
            } 
        }

        /**
         * Terminal method to update record
         *
         * @param array ...$predicates
         * @return integer
         */
        public function update(): int{
            try{
                if(!empty(self::$table)){
                    
                    $db = (new Database())->connect();
                    $sql = new BuildQuery($db);

                    $zdx_predicates = [];
                    foreach($sql->getTableCols(self::$table) as $col ){
                        if( !is_null($this->{$col}) )
                            array_push($zdx_predicates, [ $col, $this->{$col} ]);
                        else
                            continue;
                    }
 
                    if(sizeof($zdx_predicates) == 0)
                        return 0;

                    $rs = $sql->update(self::$table.' '.self::$zdx_relate_to  , self::$zdx_conditions, $zdx_predicates );

                    return $rs->rowCount();
                    
                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            } finally{
                // Reset model
                self::$zdx_relate_to = null;
                self::$zdx_conditions = [];
                $zdx_predicates = [];

                
                foreach( $sql->getTableCols(self::$table) as $col){
                    $this->{$col} = null;
                }
            }
        }

        /**
         * Terminal method to remove a single record
         *
         * @return integer
         */
        public function delete() : int{
            try{
                if(!empty(self::$table)){
                    
                    $db = (new Database())->connect();
                    $sql = new BuildQuery($db);

                    
                    $rs = $sql->delete(self::$table.' '.self::$zdx_relate_to  , self::$zdx_conditions);

                    return $rs->rowCount();

                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }finally{
                self::$zdx_relate_to = null;
                self::$zdx_conditions = [];
            } 
        }

        /**
         * Terminal method to count records
         *
         * @return integer
         */
        public function count() : int{
            try{
                if(!empty(self::$table)){
                    
                    $db = (new Database())->connect();
                    $sql = new BuildQuery($db);

                    $rs = $sql->read(self::$table.' '.self::$zdx_tbl_alias.' '.self::$zdx_relate_to  , self::$zdx_conditions, self::$zdx_selects, [ self::$zdx_extra['group'].' '.self::$zdx_extra['order'].' '.self::$zdx_extra['sort'].' '.self::$zdx_extra['limit'] ] );

                   
                    return $rs->rowCount();
                
                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }finally{
                
                self::$zdx_relate_to = null;
                self::$zdx_tbl_alias = null;
                self::$zdx_conditions = [];
                self::$zdx_selects = [];
                self::$zdx_extra = [ 'group' => '' ,'limit' => '', 'order' => '', 'sort' => ''];

            } 
        }

        /**
         * terminal method to return first record alone
         *
         * @return object
         */
        public function first() : object{
            try{
                if(!empty( self::$table )){

                    self::$zdx_extra['limit'] =  "LIMIT 1";

                    return $this->get();

                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\RangeException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            } 

        }

        /**
         * terminal method to return last record alone
         *
         * @return object
         */
        public function last() : object{
            try{
                if(!empty(self::$table)){

                    if(empty(self::$key))
                        throw new \Exception("Lookup key not found, model is not associated with any key");

                    $key = self::$key;
                    $zdx_Take = $this->zdx_Take == 0 ? $this->zdx_Take : $this->zdx_Take-1;

                    self::$zdx_extra['order'] =  strlen( trim(self::$zdx_extra['order']) ) == 0 ? "ORDER BY {$key}" : self::$zdx_extra['order'];
                    
                    self::$zdx_extra['sort'] = "DESC";

                    self::$zdx_extra['limit'] =  "LIMIT {$zdx_Take},1" ;


                    return $this->get();

                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\RangeException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            } 

        }

        
        /**
         * Prospective function, to limit selection
         *
         * @param integer $limit
         * @return Model
         */
        public function take(int $limit) : self{
            try{
                if(!empty(self::$table)){
                    
                    $this->zdx_Take = $limit;

                    self::$zdx_extra['limit'] = "LIMIT 0,{$limit}" ;

                    return $this;
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
         * Prospective method to group record
         *
         * @param string $column
         * @return Model
         */
        public function groupBy(string $column) : self {
            try{
                if(!empty(self::$table)){
                    
                    self::$zdx_extra['group'] = "GROUP BY {$column}" ;

                    return $this;
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
         * Prospective method to group record
         *
         * @param string $column
         * @return Model
         */
        public function orderBy(string $column) : self {
            try{
                if(!empty(self::$table)){
                    
                    self::$zdx_extra['order'] = "ORDER BY {$column}" ;

                    return $this;
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
         * Prospective method to sort record in descending order
         *
         * @return Model | self
         */
        public function desc() : self{
            try{
                if(!empty(self::$table)){

                    if(empty(self::$key))
                        throw new \Exception("Lookup key not found, model is not associated with any key");

                    $key = self::$key;

                    self::$zdx_extra['order'] =  strlen( trim(self::$zdx_extra['order']) ) == 0 ? "ORDER BY {$key}" : self::$zdx_extra['order'];
                    
                    self::$zdx_extra['sort'] = "DESC";


                    return $this;
                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\RangeException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            } 
        }

        /**
         * Prospective method to sort record in descending order
         *
         * @return Model
         */
        public function asc() : self {
            try{
                if(!empty(self::$table)){

                    if(empty(self::$key))
                        throw new \Exception("Lookup key not found, model is not associated with any key");

                    $key = self::$key;

                    self::$zdx_extra['order'] =  strlen( trim(self::$zdx_extra['order']) ) == 0 ? "ORDER BY {$key}" : self::$zdx_extra['order'];
                    
                    self::$zdx_extra['sort'] = "ASC";

                    return $this;
                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\RangeException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            } 
        }

        /**
         * Prospective method to Joins records
         *
         * @param string $model
         * @return Model
         */
        public static function with(string $model, ?string $condition = null) : self {
            
            try{

                if(!is_null($condition))
                    self::$zdx_relate_to .= " JOIN $model ON $condition";
                else
                    self::$zdx_relate_to .= " JOIN $model";

                return new self;

            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\RangeException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            } 
        }

        /**
         * Prospective method assign alias to host table
         *
         * @param string $model
         * @return Model
         */
        public static function as(string $alias) : self {
            
            try{

                if(!empty(self::$table)){
                    
                    if(!is_null($alias))
                        self::$zdx_tbl_alias = " AS $alias ";
                    

                }else{
                    throw new \DomainException("Table not found, model is not associated with any table");
                }
                
                return new self;

            }catch(\InvalidArgumentException $t){
                new ErrorTracer($t);
            }catch(\RangeException $t){
                new ErrorTracer($t);
            }catch(\DomainException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t);
            } 
        }

        /** Schema Validators */

        /**
         * @param array ...$prequisite
         * @return bool
         */
        public function isExists(array ...$prequisite) : bool {
            try{

                if( (new self())->where( ...$prequisite )->count() > 0 )
                    return true;
                else
                    return false;

            } catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        /**
         * @param string $value
         * @return bool
         */
        public function isDuplicate(string $value) : bool {
            try{

                if(empty(self::$key))
                    throw new \Exception("Lookup key not found, model is not associated with any key");

                if( gettype($value) != 'resource' ){
                    if( (new self())->find( $value )->count() > 0 )
                        return true;
                    else
                        return false;

                }else{
                    throw new \Exception("A resource can\'t be validated");
                }

            } catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        /**
         * In-Aux Function
         * @param $method_name
         * @param $arguments
         * @return Model|null
         */
        public function __call($method_name, $arguments) : ?self
        {

            try {// Note: value of $name is case sensitive.
                if ($method_name == 'iwhere') {

                    if (sizeof($arguments) == 2 && is_string($arguments[0]) && is_string($arguments[1])) {
                        return $this->where([$arguments[0], $arguments[1]]);
                    }else{
                        throw new \Exception("Two (2) arguments are expected as string");
                    }
                }else{
                    return null;
                }

            } catch (\Throwable $t) {
                new ErrorTracer($t);
            }

        }
	}

?>
