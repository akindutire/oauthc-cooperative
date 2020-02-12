<?php
	
	namespace zil\factory;
	use zil\core\config\Config;
	use zil\core\tracer\ErrorTracer;
	use zil\core\exception\UnexpectedCaseException;
	

	class Logger extends Config{


		public $curAppPath  = 	null;
		private $rlogPath   = 	null;
		
		
		private static $Instance = null;

		private $logOverride	=	false;
		private $QlogOverride	=	false;

		public function __construct(){
					
		}

        private static function  getInstance(){

		    if(self::$Instance == null)
		        self::$Instance = new self;

		    return  self::$Instance;
		}
		
		public static function Init(){
			(self::getInstance())->logOverride = true;
			return self::getInstance();
		}

		public function QInit(){
			(self::getInstance())->QlogOverride = true;
			return self::getInstance();
		}

		public static function kill(){
			(self::getInstance())->logOverride = false;
			return self::getInstance();
		}

		private function setLogPath(string $path){
			$this->rlogPath = $path;
			return $this;
		}
		
        private function push($msg){
				
			try{
				
				$realPath = $this->rlogPath;

				(new Filehandler())->createDir($realPath);
			
				dateentrypoint:

				$file = $realPath.'/'.(date('F-d-Y',time())).".log";
				
				if(!file_exists($file))
					fopen($file, 'w+');

				if(!is_readable($file))
					goto dateentrypoint;

				$time = date('h:i:s a',time());

                if(!is_string($msg) && !is_numeric($msg)){
					
					$msg = print_r($msg, true);
				}

				$msg= $time." ::-->> $msg\n";

				error_log($msg,3,$file);	
				
			}catch(\InvalidArgumentException $t){
				new ErrorTracer($t);
			}catch(\LogicException $t){
				new ErrorTracer($t);
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public static function Log(...$msg){
			$parent = new parent;

			if ($parent->eventLog || (self::getInstance())->logOverride) {
			    foreach($msg as $msg_text) {
                    (self::getInstance())->setLogPath($parent->curAppPath . "/" . $parent->logPath[0])->push($msg_text);
                }
            }
		}

		public static function QLog(...$msg){
			$parent = new parent;
			if ((self::getInstance())->QlogOverride)
				(self::getInstance())->setLogPath($parent->curAppPath."/".$parent->logPath[0])->push($msg);
		}

		public static function ELog(...$msg){
				
			$parent = new parent;
			(self::getInstance())->setLogPath($parent->curAppPath."/".$parent->logPath[1])->push($msg);
		}
	}
?>
