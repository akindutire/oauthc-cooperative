<?php
/**
 * Author: Akindutire Ayomide Samuel
 */

 namespace zil\core\middleware;

 use zil\factory\Session as SessionFactory;
 use zil\security\Encryption;

 class Csrf{

      public function __construct(string $csrfToken)
      {
         if($csrfToken != SessionFactory::getEncoded('CSRF_FLAG') )
            return false;
         else
            return true;
      }

      public static function generateToken():string{
         
         $token = (new Encryption())->authKey();
         SessionFactory::build( 'CSRF_FLAG', $token, true );
         return $token;

      } 
    
 }
?>