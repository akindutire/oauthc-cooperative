<?php
/**
 * Author: Akindutire Ayomide Samuel
 */

 namespace zil\core\middleware;

 use zil\factory\Session as SessionFactory;
 use zil\core\tracer\ErrorTracer;
 
 class Session{

    public function __construct()
    {
        
    }

    
    /**
     * A wrapper to checkSessionLifeTime and guard session name from this app with a cryptic name
     *
     * @param string $session_path
     * @return void
     */
    public static function secureSession(string $session_path, string $prefix){

        try{
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_only_cookies', 1);
          
            session_save_path($session_path);
            
            session_name( hash('sha256', "{$prefix}_xcliqs") );

            /**
                Checks if Session has ever been created once
             */
            if( !isset($_SESSION) )
                session_start();
       
            self::checkSessionLifetime();
        
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
     * Help check if session is suppose to be active or bounce it over to a new Id
     *
     * @return void
     */
    private static function checkSessionLifetime(){

        try{

            if(SessionFactory::getSession('Trace_LifeTime') == null){
               
                /**
                 * No Session Switch So far
                 */
            
                (new SessionFactory())->build('Trace_LifeTime',time() );
            }

            $current_time = time();
            $elapsed_session = SessionFactory::getSession('Trace_LifeTime');

            /**
             * Session limit can be modified, Currently = 15minutes
             */
            
            $session_limit = 900;

            $old_or_current_session = session_id();
            
            if (($current_time - $elapsed_session) > $session_limit){
                    
                    if(session_regenerate_id()) {

                        $new_session = session_id();

                            /**
                             * Session was copied successfully, Goto Old Session remove System Auth flags(Don't Alter) and Switch to new Session
                             */

                            session_id($old_or_current_session);
                        
                            (new SessionFactory())->delete('AUTH_CERT')->delete('Trace_LifeTime')->delete('Ignore_trial_check')->delete('CSRF_FLAG');
                            

                            /**
                             * Individual App Auth flag goes here, delete Auth flag to restrict access to obsolete session
                             */

                            session_id($new_session);

                            if( !isset($_SESSION) )
                                session_start();

                            (new SessionFactory())->build( 'Trace_LifeTime',time() );



                    }else{

                        /**
                         * Create Session manually and set up session data, provided session switch via session_regenerate_id failed
                         */

                        $new_id =   session_create_id('sessq-');

                        session_id($new_id);
                        session_start();
                        (new SessionFactory())->build('Last_Session', 1)->build('Trace_LifeTime', time());

                    }
            }

            /**
             * Remove Obsolete Session files
             */
          
            

            session_gc();
            
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
