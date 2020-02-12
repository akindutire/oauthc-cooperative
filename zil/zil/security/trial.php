<?php
 	namespace zil\security;
	
	use zil\core\config\Config;

    use zil\core\scrapper\Info;
    use zil\core\tracer\ErrorTracer;
    use zil\factory\BuildQuery;
    use zil\factory\Database;
    use zil\factory\Logger;
	use zil\factory\Session;



	use \DateTime;
	use \DateInterval;

	class Trial extends Config{


	        private $app = null;

			private $go_Flag 		= 	null;
			private $dataINIFile 	= 	null;

			private $DefaultdatabaseParam = null;

			public function __construct(Info $Info){
					
				$parent = new parent;

				$this->app = basename($parent->curAppPath);

				if($Info->getTree()->apps->{$this->app}->trial > 0){

				    if(Session::getSession('Ignore_trial_check') == null) {

				        $this->DefaultdatabaseParam = ['driver' => 'sqlite', 'file' => "{$parent->curSysPath}/data/zilxc.sqlite3"];

                        $this->RunTrial($Info);
                     
                        (new Session())->build('Ignore_trial_check', 1);

                    }
				}
			}


			private function RunTrial(Info $Info){


				/**
				*	Database Used: SQLite
				*/

				$this->testIfAppRunOnce();

				if($this->go_Flag === 1){
					

					/**
					* App Run For First Time
					*/
						
						$this->AppRunFirstTime($Info);

				}else if ($this->go_Flag === 2) {
					
	
					/**App Once Run*/
						$this->AppOnceRun();
				
				}else {
				
		
					/**Dont Initialize*/
						$this->Stop_Sys();
				
				}

			}


			private function dataINIExists(): ?bool {

				$parent = new parent;
				
				$FrameWorkAbsolutePath = $parent->curSysPath;
				
				$FrameWorkdataPath = '/data/';

				$dataINIFile = "{$FrameWorkAbsolutePath}{$FrameWorkdataPath}.data.txt";

				
				if (file_exists("{$dataINIFile}") == true) {
					
					$this->dataINIFile = "{$dataINIFile}";
				
					return true;
				
				}

				return null;

			}


			private function testIfAppRunOnce(){

			   
			    if ($this->dataINIExists() == true) {
					
					$lenOfData = strlen(file_get_contents($this->dataINIFile));

					if($lenOfData == 778){

						$this->go_Flag = 1;

					}elseif ($lenOfData == 729) {
						
						$this->go_Flag = 2;						

					}else{

						$this->go_Flag = null;

					}
				
				}else{

					$this->go_Flag = null;
				}

			}


        
        private function AppRunFirstTime(Info $Info){

			try{	
				$parent = new parent;

                $FrameWorkAbsolutePath = $parent->curSysPath;
				
				$FrameWorkdataPath = '/data/';
				
				
				$CBMPdataPath = "{$FrameWorkAbsolutePath}{$FrameWorkdataPath}";

				if (is_dir($CBMPdataPath) === true) {
					
					$DataTXTFile = $CBMPdataPath.'.data.txt';

					$string=file_get_contents($DataTXTFile);
					$sub = substr($string, 558,49);

					$string = str_replace($sub, "", $string);

					if(file_put_contents($DataTXTFile, $string) !=  729)
					    throw new \Exception("Couldn't write a factory file [DiscursBag, App Data Directory Not Set]");

					
				}else{

					throw new \Exception("App Data Directory Not Set");
					
				}

				$CBMPdataPathFile = $CBMPdataPath.'.c.bmp';


				/*Trial Period*/

                    $trialPeriod = $Info->getTree()->apps->{$this->app}->trial;

				/*DATE OBJECTS*/
									
					$future = new DateTime();	
					$future->add(new DateInterval('P'.$trialPeriod.'D'));

					$now = new DateTime();
					$now = $now->getTimestamp();

					$future = $future->getTimestamp();


				if(file_put_contents($CBMPdataPathFile, $future) != 0){
				
					/**Contain TimeStamp To Stop App*/			
						
						$PlaceCBMPDataInDatabase = file_get_contents($CBMPdataPathFile);
												
				}else{
                   
                    throw new \Exception("Couldn't write a factory file [DiscursBag]");
				}


				$connection1 = (new Database())->connect($this->DefaultdatabaseParam);

				$sql = new BuildQuery($connection1);

				$connection1->exec("CREATE TABLE IF NOT EXISTS trial(id INTEGER PRIMARY KEY,start INTEGER NOT NULL,stop INTEGER NOT NULL,last_seen INTEGER NOT NULL)");

				$sql->delete('trial',[]);
				
				$rs = $sql->create('trial',[1,$now,$PlaceCBMPDataInDatabase,$now]);

				if ($rs == false) {
					
                    throw new \Exception("Couldn't Initialize Trial System [DiscursBag]");
				}

			}catch(\Throwable $e){
				new ErrorTracer($e);
			}

		}


      

        private function AppOnceRun(){

			try{	
				$parent = new parent;
				
				$FrameWorkAbsolutePath = $parent->curSysPath;;
				
				$FrameWorkdataPath = 'data';

				$CBMPdataPath = "{$FrameWorkAbsolutePath}{$FrameWorkdataPath}";


				if (file_exists("{$CBMPdataPath}.c.bmp") == false) {
					
					$this->kill_Sys();

				}else{

					
					
					$connection1 = (new Database())->connect($this->DefaultdatabaseParam);
					
					$sql = new BuildQuery($connection1);
					
					$rs = $sql->read('trial',[['id',1]],['last_seen']);

                    $now = new DateTime();
                    
                    $now = $now->getTimestamp();




                    /*CBMPFILE -Contain TimeStamp To Stop App*/
						
						$CBMPdata=file_get_contents("{$CBMPdataPath}.c.bmp");



					if ($rs->rowCount() != 1) {
				


						/*Repair Trial System*/

						$connection1->exec("CREATE TABLE IF NOT EXISTS trial(id INTEGER PRIMARY KEY,start INTEGER NOT NULL,stop INTEGER NOT NULL,last_seen INTEGER NOT NULL)");

						$sql->delete('trial',[]);
						
						$rs = $sql->create('trial',[1,$now,$CBMPdata,$now]);
						
						if ($rs == false) {
							
							Logger::Log("Couldn't Repair Trial System");

                            die("Couldn't Repair DiscursBag on line ".__LINE__);
						}




					}else if ($rs->rowCount() == 1) {

						


						/*Update Last Minute*/

						/*CBMPdata Contain TimeStamp To Stop App*/
						
						if ($CBMPdata < $now) {
							
							$this->Kill_Sys();

						}else{



							/*Check If System Back Dated*/
                           
                            $rs = $sql->read('trial',[['id',1]],['last_seen']);
							list($last_seen) = $rs->fetch();

							if($last_seen < $now){

								$sql->update('trial',[['id',1]],[['last_seen',$now]]);

							}else{

								$this->Stop_Sys_For_BackDate();
							}


						}

					}

					/*Repair or Update Logic End*/

				}
			}catch(\Throwable $e){
				new ErrorTracer($e);
			}
		}
			

		private function kill_Sys(){

				die("<pre><b>Fatal Error:</b> Contact App Provider, Z::TanyunChi</pre>");

			}

			private function Stop_Sys(){

				die("<pre><b>Catchable Error:</b> Contact App Provider, Z::DiscursEnvyBag</pre>");

			}

			private function Stop_Sys_For_BackDate(){

				die("<pre><b>Catchable Error:</b> Machine Date/Time Incorrect -Adjust Date/Time, Z::MachineEnvyBag</pre>");
			}
				
				
	}
?>