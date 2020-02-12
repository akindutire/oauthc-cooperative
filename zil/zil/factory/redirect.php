<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
	namespace zil\factory;

	use zil\core\config\Config;
 	use zil\core\tracer\ErrorTracer;


	class Redirect extends Config{

        /**
         * Redirect through URL
         *
         * @param string $url
         * @param string $relativeTo
         */
		public function __construct(string $url, string $relativeTo="/"){
			try{

				$scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';

				if( strpos($url, $scheme.'://') === 0 ){
					header("location:{$url}");
				}else{

					
					/**Retrieve request domain or subdomain */
					$requestBase = rtrim( (new Config())->requestBase, '/');
					
					$host = $_SERVER['HTTP_HOST'];

                    $url = preg_replace('/\/+/', '/', "{$host}/{$requestBase}/{$url}");

					header("location:{$scheme}://{$url}");
				}

			}catch(\InvalidArgumentException $t){
				new ErrorTracer($t);
			}catch(\LogicException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}
	}

?>
