<?php 
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\factory;

	use \zil\core\config\Config;
	use \zil\core\tracer\ErrorTracer;


	class Fileuploader extends Config{

		private static $ERROR = null;
		private static $ERR_CODE = null;

		public function __construct(){
			
		}

		/**
		 * Upload and Compress file
		 *
		 * @param array $data
		 * @return Fileuploader
		 */
        public function upload(array $data=[]): Fileuploader {
			/**
			*	Array Format as ['file'=>resource file,'size'=>expected_size in byte,'type'=>[expected_type1,expected_type2],destination=>path,compress=>true]
			*/
			try{
				self::$ERROR = null;
				self::$ERR_CODE = null;
				/** @ $file  Binary file, comprising size, tmp, and name*/
				$file = $data['file'];
				/** Temporary file location provided by php file upload mechanism*/
				$tmp = $file['tmp_name'];
				if(array_key_exists('compress',$data) != true)
					$data['compress'] = false;

				if($this->checkFileValid($tmp) === false){
					
					self::$ERR_CODE = 'FNE';
					self::$ERROR = "{$file['name']} is not a file";
					
					return $this;
				}

				if ($this->checkFileType($tmp, $data['type']) === false) {
					$expected = implode(',', $data['type']);
					
					self::$ERR_CODE = 'FTE';
					
					self::$ERROR = "{$file['name']} is not an Expected type, Expecting {$expected} file, {$file['type']} given ";
					
					return $this;
				}
							
				if($this->checkFileSize($tmp, $data['size']) === false){
					
					self::$ERR_CODE = 'FSE';
					
					self::$ERROR = "{$file['name']} is too large, Expecting {$data['size']}, {$file['size']}kB given";
					
					return $this;
				}

				if(move_uploaded_file($file['tmp_name'], $data['destination'])){
						
					/**
					 * File uploaded
					 */

					if($data['compress'] === true){

						return $this->compress($data['destination'], $data['destination']);
							
					}else{

						self::$ERR_CODE = null;
						self::$ERROR = null;
						return $this;
					
					}
			
				}else{
						
					self::$ERR_CODE = 'FUE';
					self::$ERROR = "Couldn't complete file Upload of [TEMP | {$file['tmp_name']}] {$file['name']} to {$data['destination']} ";
					return $this;

				}
				
				
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
			
		/**
		 * Assert existence of error
		 *
		 * @return boolean
		 */
		public function hasError(): bool{
		
			if(!is_null(self::$ERROR))
				return true;

			return false;

		}

		/**
		 * Assert file upload
		 *
		 * @return boolean
		 */
		public function isUploaded(): bool{
		
			if(is_null(self::$ERROR))
				return true;

			return false;

		}


		/**
		 * Error statement when upload fails
		 *
		 * @return string|null
		 */
		public function getError() : ?string {
		
			return self::$ERROR;

		}

		/**
		 * Error code when upload fails
		 *
		 * @return string|null
		 */
		public function getErrorCode() : ?string{

			return self::$ERR_CODE;
		}


		/**
		 * Evaluate file validity 
		 *
		 * @param string $file
		 * @return void
		 */
		private function checkFileValid(string $file) : bool {
			try{

			    if(file_exists($file))
					return true;
				else
					return false;

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

		/**
		 * Evaluate type of file uploaded
 		 *
		 * @param string $file
		 * @param array $data_accept_type
		 * @return bool
		 */
		private function checkFileType(string $file, array $data_accept_type = []) : bool {

			try{

				$F = new \finfo();
				
				$mime_type = $F->file($file, FILEINFO_MIME_TYPE);
				
				if( in_array($mime_type, $data_accept_type) )
					return true;
				else
					return false;
				
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


		/**
		 * Evaluate file size against dedicated size
		 *
		 * @param string $file
		 * @param integer $maximum_size
		 * @return bool
		 */
		private function checkFileSize(string $file, int $maximum_size) : bool{
			try{
				if($maximum_size >= filesize($file))
					return true;
				else
					return false;
			}catch(\InvalidArgumentException $t){
				new ErrorTracer($t);
			}catch(\LengthException $t){
				new ErrorTracer($t);
			}catch(\LogicException $t){
				new ErrorTracer($t);
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}

		}

		/**
		 * Wrapper of compressors
		 *
		 * @return Fileuploader
		 */
		private function compress(string $file, string $destination) : Fileuploader{

				$F = new \finfo();
				
				$mime_type = $F->file($file, FILEINFO_MIME_TYPE);

			if(strpos($mime_type, 'image') !== false){

				
				if($this->compressImg($file, $destination, 80) == true){

					/**
					 * File compressed and uploaded
					 */
					self::$ERR_CODE = null;
					self::$ERROR = null;
					return $this;

				}else{

					self::$ERR_CODE = 'FCE';
					
					self::$ERROR = "Couldn't complete File Upload and Compression of {$file}";
					
					return $this;
				}

			}else{
				if($this->compressToZip($file, $destination ) == true){

					/**
					 * File uploaded and compressed
					 */
					self::$ERR_CODE = null;
					self::$ERROR = null;
					return $this;

				}else{

					self::$ERR_CODE = 'FCE';
					
					self::$ERROR = "Couldn't complete File Upload and Compression of {$file}";
				
					return $this;
				
				}

			}

		}

		/**
		 * Compress Images
		 *
		 * @param string $file
		 * @param string $destination
		 * @param integer $quality
		 * @return bool
		 */
		private function compressImg(string $file, string $destination, int $quality) : bool{

				try {

					if(!$this->checkFileType($file,['image/jpeg','image/png','image/gif']))
						throw new \DomainException("Invalid image type");

					$IMG_INFO = getimagesize($file);
					
					if ($IMG_INFO['mime'] = 'image/jpeg'){
					
						$image = imagecreatefromjpeg($file);

					}else if ($IMG_INFO['mime'] = 'image/gif'){

						$image = imagecreatefromgif($file);
					
					}else if($IMG_INFO['mime'] = 'image/png'){

						$image = imagecreatefrompng($file);

					}else{
						
						throw new \Exception("Couldn't get Image details");
					
					}

					if(imagejpeg($image, $destination, $quality)){

						return true;
					
					}else{
					
						return false;
					
					}

				} catch(\InvalidArgumentException $t){
					new ErrorTracer($t);
				} catch(\LogicException $t){
					new ErrorTracer($t);
				} catch(\DomainException $t){
					new ErrorTracer($t);
				}catch(\Exception $t){
					new ErrorTracer($t);
				}catch (\Throwable $t) {
					new ErrorTracer($t);
				}
			

		}

		/**
		 * Compress file to zip
		 *
		 * @param string $file
		 * @param string $destination
		 * @return boolean
		 */
		public function compressToZip(string $file, string $destination): bool{
			try{
				$zip = new \ZipArchive();
				if($zip->open("{$destination}.zip", ZipArchive::CREATE)){
					$zip->addFile($file, $destination);
					$zip->close();
					return true;
				}else{
					return false;
				}
				
				
				
				
			}catch(\InvalidArgumentException $t){
				new ErrorTracer($t);
			} catch(\LogicException $t){
				new ErrorTracer($t);
			} catch(\DomainException $t){
				new ErrorTracer($t);
			}catch(\Exception $t){
				new ErrorTracer($t);
			}catch (\Throwable $t) {
				new ErrorTracer($t);
			}
		}
			

		/**
		 * Rename file, depend on rename
		 *
		 * @param string $file
		 * @param string $newfile
		 * @return void
		 */
		public function renameFile(string $file, string $newfile){
		
			if (rename($file, $newfile))
				return true;
			else
				return 0;
		
		}


		
		/**
		 * Close file uploader
		 *
		 * @return void
		 */
		public function close(){
		
			self::$ERROR = null;
			self::$ERR_CODE = null;
		}

		/**
		 * Clean up while file uploader handler is destroyed
		 */
		public function __destruct()
		{
			$this->close();
		}

	}
?>
