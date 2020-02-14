<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\factory;
    
    use zil\core\config\Config;
    use zil\core\exception\UnexpectedCaseException;

    use zil\factory\Logger;
    use zil\core\tracer\ErrorTracer;



	class Session extends Config{

			public function __construct(){

			}

            /**
             * Retrieve prefix for session usually the app name 
             *
             * @return string
             */
			private static function getAppPrefix(): string{

			    $syscfg = new parent;
                $current_app_prefix = basename($syscfg->curAppPath)."__";
                return $current_app_prefix;
			}
            
            
			private function buildSession0(string $id, $data): bool{
                try{
                    $_SESSION[$id] = $data;

                    if (!empty($_SESSION[$id])) {
                        
                        Logger::Log("$id Session Saved");
                        return true;
                        
                    }else {
                        
                        throw  new \Exception("Server Error: Session Building Failed");
                    }
                }catch(\InvalidArgumentException $t){
                    new ErrorTracer($t);
                }catch(\ParseError $t){
                    new ErrorTracer($t);
                }catch(\TypeError $t){
                    new ErrorTracer($t);
                }catch(\Throwable $t){
                    new ErrorTracer($t);
                }
			}

        /**
         * Build up a value in a session unique key
         *
         * @param string $session_key
         * @param string $session_data
         * @param bool $encode
         * @return Session
         */
			public static function build(string $session_key, $session_data, bool $encode = false): Session{

                try{
                   

                    if (!empty($session_key) && !empty($session_data)) {  
                            
                        
                        if($encode)
                            $session_key = base64_encode($session_key);

                        $session_id = self::getAppPrefix().trim($session_key);

                        (new self())->buildSession0($session_id, $session_data);

                    }

                        
                    return new self;

                }catch(\InvalidArgumentException $t){
                    new ErrorTracer($t);
                }catch(\ParseError $t){
                    new ErrorTracer($t);
                }catch(\TypeError $t){
                    new ErrorTracer($t);
                }catch(\Throwable $t){
                    new ErrorTracer($t);
                }
				
			}

            /**
             * Session with Encoded key
             *
             * @param string $id
             * @return string|null|array
             */
            public static function getEncoded(string $id)  {

                
                return self::getSession(base64_encode($id));
            }

            /**
             * Session with Plain key
             *
             * @param string $id
             * @return void
             */
            public static function get(string $id) : ?string{
                
               return self::getSession($id);
            }

            /**
             * Retrieve a session value via a session key
             *
             * @param string $id
             * @return string|null|array
             */
			public static function getSession(string $id)  {

                try{
                   
                    $prefix = self::getAppPrefix();
                    $id = "{$prefix}{$id}";

                    if (isset($_SESSION[$id])){
                        return $_SESSION[$id];
                    }else {
                        return null;	  
                    }

                }catch(\InvalidArgumentException $t){
                    new ErrorTracer($t);
                }catch(\ParseError $t){
                    new ErrorTracer($t);
                }catch(\TypeError $t){
                    new ErrorTracer($t);
                }catch(\Throwable $t){
                    new ErrorTracer($t);
                }
			}

            /**
             * Remove a session data via a key
             *
             * @param string $id
             * @return Session
             */
			public static function delete(string $id): Session{
                
                try{
                    
                    $id = (new self())->getAppPrefix().$id;
                    unset($_SESSION[$id]);
                    
                    return new self;

                }catch(\InvalidArgumentException $t){
                    new ErrorTracer($t);
                }catch(\ParseError $t){
                    new ErrorTracer($t);
                }catch(\TypeError $t){
                    new ErrorTracer($t);
                }catch(\Throwable $t){
                    new ErrorTracer($t);
                }
            }

            /**
             * Remove a session data via a key
             *
             * @param string $id
             * @return Session
             */
			public static function deleteEncoded(string $id): Session{
                
                try{
                   
                    return self::delete(base64_encode($id));
                    
                }catch(\InvalidArgumentException $t){
                    new ErrorTracer($t);
                }catch(\ParseError $t){
                    new ErrorTracer($t);
                }catch(\TypeError $t){
                    new ErrorTracer($t);
                }catch(\Throwable $t){
                    new ErrorTracer($t);
                }
            }
 
                
	}
?>