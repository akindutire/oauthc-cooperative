<?php
/**
 * Author: Akindutire Ayomide Samuel
 */ 
namespace zil\factory;
	
	use zil\core\config\Config;
	use zil\core\tracer\ErrorTracer;

    class Filehandler extends Config{


		public function __construct(){
			
			$this->init();

		}

		private function init(){
			
		}

		/**
		*	Directory Methods
		*/

		public function isDir(  string $pathdir ){
			try{
				if (is_dir($pathdir) || file_exists($pathdir))
					return true;

				return null;
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		/**
		 * Create a new  directory
		 *
		 * @param string $path
		 * @param integer $mode
		 * @return void
		 */
		public function createDir(  string $path, int $mode = 0777 ){
			try{
				if(!is_dir($path))
					return mkdir($path,$mode,true);
				
				return null;
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		/**
		 * Create a new file
		 *
		 * @param string $filename
		 * @param [type] $context
		 * @return boolean
		 */
		public function createFile( string $filename, ?string $context = null) : bool {

			
			
			try{
				
				if(!is_dir(dirname($filename)))
					$this->createDir(dirname($filename));

				$handle =   fopen($filename,"w");
				
				if(is_null($handle))
					throw new \Exception("Couldn't open the file {$filename} for writing");

				if(fwrite($handle,$context) !== false) {
				
					fclose($handle);
				
					return true;
				}

				fclose($handle);

			}catch(\Throwable $t){
				new ErrorTracer($t);
			}           
		}

		/**
		 * Copy a file
		 *
		 * @param string $source
		 * @param string $destination
		 * @param [type] $context
		 * @return void
		 */
		public  function copy(  string $source,  string $destination, ?string $context = null){
			try{
				if(is_dir(dirname($source)))
					return copy($source,$destination,$context);

				return null;
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		/**
		 * Delete a directory
		 *
		 * @param string $dir
		 * @return void
		 */
		public function removeDir(  string $dir ) : bool{
			try{
				if(is_dir($dir))
					$files = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::CHILD_FIRST);
				else
					return false;
				
				foreach ( $files as $finfo ){
					$op = ($finfo->isDir() ? 'rmdir' : 'unlink');
					if($op == 'unlink'){
						if(file_exists($finfo->getRealPath()))
							@$op($finfo->getRealPath());
						else
							continue;
					}else{
						@$op($finfo->getRealPath());
					}
				}
				
				if(is_dir($dir))
					@rmdir($dir);
				
				return true;
			
			}catch(\OutOfRangeException $t){
				new ErrorTracer($t);
			}catch(\LogicException $t){
				new ErrorTracer($t);
			}catch(\Error $t){
				new ErrorTracer($t);
			}
		}

		/**
		 * Move file to another directory 
		 *
		 * @param string $old_dir
		 * @param string $new_dir
		 * @return void
		 */
		public function rename(  string $old_dir,  string $new_dir ) : bool {
			try{
				if( ( !is_dir($old_dir) || !is_file($old_dir) ) && ( !is_dir($new_dir) || !is_file($new_dir) ) )
					throw new \OutOfRangeException("[Expected]: A valid file, Invalid file name giver, file not found");

				return rename($old_dir, $new_dir);
			}catch(\OutOfRangeException $t){
				new ErrorTracer($t);
			}catch(\LogicException $t){
				new ErrorTracer($t);
			}catch(\Error $t){
				new ErrorTracer($t);
			}
		}

		/**
		 * Explore file
		 *
		 * @param string $filename
		 * @param string $width
		 * @param string $height
		 * @return void
		 */
		public function openFile(  string $filename,  string $width='200px',  string $height='auto') :  string {
			try{
				if(!file_exists($filename))
					throw new \OutOfRangeException("[Expected]: A valid file, Invalid file name given, file not found");

				$type = mime_content_type("{$filename}");
				$filename = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filename);
				$string = null;
				if($type == 'image/jpeg' || $type == 'image/gif' || $type == 'image/bmp' || $type == 'image/png' || $type == 'image/webp' || $type == 'image/jpg'){
					$string = "<img style='' src='$filename' width='$width' height='$height'>";
				}else if ($type == 'video/3gpp' || $type == 'video/jpm' || $type=='video/jpeg' || $type=='video/mp4' || $type=='video/mpeg' || $type=='video/x-matroska' || $type=='video/quicktime' || $type=='video/ogg' || $type=='video/webm') {
					$string = "<video style='' src='$filename' width='$width' height='$height' controls></video>";
				}else if($type == 'audio/vnd.dts' || $type == 'audio/mpeg' || $type=='audio/mp4' || $type=='audio/ogg' || $type=='audio/x-pn-realaudio' || $type=='audio/wav' || $type=='audio/mp3'){
					if ($type == 'audio/mp3') {
						$type = 'audio/mpeg';
					}
					$string = "<audio src='$filename' controls style=''></audio>";
				}else if($type == 'application/msword' || $type == 'application/vnd.ms-word.document.macroenabled.12' || $type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
					$string = "<a style='border:none; background: mediumslateblue; padding:4px;' id='btn' href='$filename'>Open File</a>";
				}else if($type == 'application/vnd.ms-powerpoint' || $type =='application/vnd.ms-powerpoint.template.macroenabled.12' || $type =='application/vnd.openxmlformats-officedocument.presentationml.template' || $type == 'application/vnd.ms-powerpoint.addin.macroenabled.12' || $type == 'application/vnd.cups-ppd' || $type == 'image/x-portable-pixmap' || $type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.ms-powerpoint.slideshow.macroenabled.12' || $type == 'application/vnd.openxmlformats-officedocument.presentationml.slideshow' || $type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.ms-powerpoint.presentation.macroenabled.12' || $type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation'){
						$string = "<a  style='border:none; background: mediumslateblue; padding:4px;' id='btn' href='$filename'>Open File</a>";
				}else if ($type == 'application/pdf') {
					$string = "<a style='border:none; background: mediumslateblue; padding:4px;' id='btn' href='$filename'>Open File</a>";
				}
				return $string;
			}catch(\OutOfRangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}
	}
?>
