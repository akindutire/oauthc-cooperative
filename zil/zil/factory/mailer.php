<?php
	
	namespace zil\factory;
	use \zil\core\config\Config;
	
 	use zil\core\tracer\ErrorTracer;


	class Mailer extends Config{

		private $username 	= 	null;
		private $password 	= 	null;
		private $host 		= 	null;
		private $port 		= 	null;

		private $msg 		= 	null;

		
 		public function __construct(string $driver= "PHP_MAILER"){
			try{
				$parent = new parent;

				if($driver == 'PHP_MAILER')
					require $parent->curSysPath.'/dependency/PHPMailer/PHPMailerAutoload.php';
			
			}catch(\InvalidArgumentException $t){
				new ErrorTracer($t);

			}catch(\Throwable $t){
				new ErrorTracer($t);
			}

		}


		/**
		 * Connect to Mail Server
		 *
		 * @param string $username
		 * @param string $password
		 * @param string $host
		 * @param integer $port
		 * @return Mailer
		 */
		public function connectMail(string $username, string $password, string $host='smtp.gmail.com', int $port=587): Mailer{

			try{
				if(!empty($username) && !empty($password)){

					$this->username 	= 	$username;
					$this->password 	= 	$password;
					$this->host 		= 	$host;
					$this->port 		= 	$port;
					return $this;

				}else{
					throw new \Exception("Undefined mailer username and password");
				}
			}catch(\Throwable $e){
				new ErrorTracer($e);
			}
			

		}


		public function sendAuthMail( string $senderName, string $senderAddress, array $to = [], string $subject, string $body){

			try{
				if(!empty($this->username) && !empty($this->password)){
					
					$string_all 	= 	null;
					
					foreach ($to as $mailTo) {
						$string_all .= $mailTo.',';
					}


					$string_all	=	rtrim($string_all,',');
				
					$mail 	= 	new \PHPMailer();
					
					$mail->isSMTP();
					$mail->SMTPDebug = 0;
					$mail->SMTPAuth = true;
					$mail->SMTPSecure = 'tls';		
					$mail->Host = $this->host;
					$mail->Port = $this->port;
					$mail->Username = $this->username;
					$mail->Password = $this->password;
					$mail->From = $senderAddress;
					$mail->FromName = $senderName;
					$mail->Subject= $subject;
					$mail->Body = $body;
					$mail->isHTML(true);
					$mail->addAddress($string_all);
					$mail->Debugoutput = 'html';


					try{
			
						if($mail->Send()){

							$this->msg = "Mail Successfully Sent to $string_all";
							return $this;
						
						}else{
							throw new \phpmailerException($mail->ErrorInfo);
						}
				
					}catch(\phpmailerException $e){

						$this->msg = $e->getMessage();
					
						new ErrorTracer($e);

					}

				}else{

					throw new \LengthException("Username and Password can't be empty, connect to mail server first");
					
				}
			}catch(\InvalidArgumentException $t){
				new ErrorTracer($t);
			}catch(\LengthException $t){
				new ErrorTracer($t);
			}catch(\LogicException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}/** @noinspection PhpOptionalBeforeRequiredParametersInspection */


        /**
		 * Send Mail Without a 3rd Party Server
		 *
		 * @param string $senderName
		 * @param array $to
		 * @param string $subject
		 * @param string $body
		 * @return Mailer
		 */
		public function sendMail(string $senderName, string $from, array $to = [], string $subject, string $body): Mailer{

			try{

				$string_all = null;
				
				foreach ($to as $mailTo) {

					$string_all .= $mailTo.',';
				
				}
				

				$string_all=rtrim($string_all,',');
				

				$mail 	= 	new \PHPMailer();
			
				$mail->isMail();
				$mail->From = $from;
				$mail->FromName = $senderName;
				$mail->Subject= $subject;
				$mail->Body = $body;
				$mail->addAddress($string_all);
				$mail->Debugoutput = 'html';

				try{

					if($mail->Send())
						$this->msg = "Mail Successfully Sent to $string_all";
					else
						throw new \Exception($mail->ErrorInfo);

					return $this;
			
				}catch(\Exception $e){
					$this->msg = $e->getMessage();
					new ErrorTracer($e);
				}
			}catch(\InvalidArgumentException $t){
				new ErrorTracer($t);
			}catch(\LogicException $t){
				new ErrorTracer($t);
			}catch(\throwable $t){
				new ErrorTracer($t);
			}

		}


		public  function getStatus(){

		    return $this->msg;
        
        }

		
}
	
?>