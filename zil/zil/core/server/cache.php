<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\server;

use zil\core\config\Config;


use \Phpfastcache\CacheManager;
use \Phpfastcache\Config\ConfigurationOption;

use \Phpfastcache\Helper\Psr16Adapter;
use zil\core\tracer\ErrorTracer;


class Cache extends Config{

    protected $cacheLife = 0;

    private static $InstanceCache = null;

    public function __construct(){
        // Setup File Path on your config files
        // Please note that as of the V6.1 the "path" config 
        // can also be used for Unix sockets (Redis, Memcache, etc)

        $sysPath = (new parent())->curSysPath;
        $cachePath = (new parent())->cachePath;
        
        if(is_null(self::$InstanceCache)){
        
            CacheManager::setDefaultConfig(new ConfigurationOption([
                'path' =>  "{$sysPath}/{$cachePath}"
            ]));

            $defaultDriver = 'Files';
            self::$InstanceCache = new Psr16Adapter($defaultDriver);

        }
    }

    public function hit(string $key){
        
        return self::$InstanceCache->has($key);
     
    }

    public function set(string $key, $data){

        if((new parent())->projectCacheAge > 0){
            if(!self::$InstanceCache->has($key))    
                
               return self::$InstanceCache->set($key, $data, (new parent())->projectCacheAge);
            
        }else{
            return false;
        }
    }

    public function get(string $key){
     
        if(self::$InstanceCache->has($key))  
            return self::$InstanceCache->get($key);
    
    }

}

?>