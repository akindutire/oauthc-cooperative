<?php


    use zil\factory\View;
    use zil\core\config\Config;
    use zil\factory\Session;
    use zil\factory\Redirect;
    use zil\core\tracer\ErrorTracer;

    
            
            $GLOBALS['appDir'] = (new Config())->getCurAppPath();
            $GLOBALS['routerLinkBase'] =   rtrim( (new Config())->getRequestBase(), '/');
            $GLOBALS['projectDir'] = (new Config())->getProjectBasePath();

            if(!function_exists('render')){

                function render(string $view, array $data = []){
                    View::render($view, $data);
                }
            }

            if(!function_exists('view')){
                function view(string $view, array $data = []){ 

                    View::render($view, $data);
                }
            }

            if(!function_exists('route')){
                function route(string $route){ 

                    $r = preg_replace('(/+)','/', '/'.$GLOBALS['routerLinkBase'].'/'.$route);

                    $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
                    $host = $_SERVER['HTTP_HOST'];

                    return "{$scheme}://{$host}{$r}";
                }
            }

            if(!function_exists('asset')){
                function asset(string $resource){ 
                    $scheme = 'http://';
                    if( isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] )
                        $scheme = 'https://';

                    
                    return str_replace(
                        $_SERVER['DOCUMENT_ROOT'], 
                        $scheme.$_SERVER['HTTP_HOST'], 
                        $GLOBALS['appDir'])."/asset/$resource";
                }
            }

            if(!function_exists('dependency')){
                function dependency(string $resource): string{
                    return $GLOBALS['appDir'].'/dependency/'.$resource;
                }
            }


            if(!function_exists('session')){
                function session(string $key){ 
                    return Session::get($key);
                }
            }


            if(!function_exists('report')){
                function report(int $reportType){ 
                    include_once($GLOBALS['appDir']."/asset/report/{$reportType}/index.php");
                }
            }

            if(!function_exists('uresource')){
                function uresource(string $resource){ 
                   
                    $scheme = 'http://';
                    if( isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] )
                        $scheme = 'https://';

                    
                    return str_replace(
                        $_SERVER['DOCUMENT_ROOT'], 
                        $scheme.$_SERVER['HTTP_HOST'], 
                        $GLOBALS['appDir'])."/asset/uresource/$resource";
                }
            }

            

            if(!function_exists('shared')){
                function shared(string $resource){

                    $scheme = 'http://';
                    if( isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] )
                        $scheme = 'https://';

                    
                    return $scheme.$_SERVER['HTTP_HOST'].$GLOBALS['projectDir']."/src/shared/$resource";
                
                }
            }

            if(!function_exists('crossGet')){
                function crossGet(string $appname, string $resource){
                    try{
                        $scheme = 'http://';
                        if( isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] )
                            $scheme = 'https://';


                        return $scheme.$_SERVER['HTTP_HOST'].$GLOBALS['projectDir']."/src/{$appname}/$resource";

                    }catch(\Throwable $t){
                        new ErrorTracer($t);
                    }
                
                }
            }


            if(!function_exists('errors')){
                function errors(){

                    return View::errors();
                }
            }

            if(!function_exists('notifications')){
                function notifications($notificationType = 'SUCCESS'){

                    if($notificationType == 'SUCCESS')
                        return View::notifications();
                    else
                        return View::errors();
                }
            }

            if(!function_exists('data')){
                function data(?string $key){

                    return View::data($key);
                }
            }

            if(!function_exists('goBack')){
                function goBack(){
                    try{
                        
                        if(isset($_SERVER['HTTP_REFERER']))
                            new Redirect($_SERVER['HTTP_REFERER']);
                        else
                            throw new DomainException("Couldn't found previous URL on server domain");

                    }catch(DomainException $t){
                        new ErrorTracer($t);
                    }catch(\Throwable $t){
                        new ErrorTracer($t);
                    }
                }
            }
            

    

?>
