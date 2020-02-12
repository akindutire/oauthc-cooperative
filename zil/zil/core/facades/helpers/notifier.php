<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\facades\helpers;

use zil\factory\Session;
use zil\core\tracer\ErrorTracer;


trait Notifier{

    private $zdx_notifications = [];

    /**
     * Set Notification message
     *
     * @param string $message
     * @return Notifier
     */
    public function notification(string $message){
        try{

            array_push($this->zdx_notifications, $message);
            return $this;

        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }



    /**
     * Clear existing notifications, either Errors or Message
     * @return void
     */
    public function clear() : self{
        try{

            /**
            *    Clear all flash messages
             */
            Session::deleteEncoded('0xc4_form_errors');
            Session::deleteEncoded('0xc4_page_notifications');
            return $this;

        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
     * Publish notification
     *
     * @param string|null $notificationType
     * @return void
     */
    public function send(?string $notificationType){
        try{

            $Session = new Session;

            if($notificationType == 'ERROR' || $notificationType == 'ERR' || $notificationType == 'E'){

                $prev_err = $this->zdx_notifications;
                if(is_array($Session->getEncoded('0xc4_form_errors')))
                    $prev_err = array_merge($Session->getEncoded('0xc4_form_errors'), $this->zdx_notifications);

                $Session->build( '0xc4_form_errors', $prev_err, true );

            }else{

                $Session->build( '0xc4_page_notifications', $this->zdx_notifications, true );
            }
            $this->zdx_notifications = [];

        } catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

}

?>
