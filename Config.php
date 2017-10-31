<?php


namespace Racine;

class Config
{
    /**
     * @var Config
     */
    private static $instance = null;
    
    /**
     * @var FileLocator
     */
    private $locator;
    
    
    private function __construct()
    {
        $configDirectories = [_CONFIG_DIR_];
    }
    
    protected static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public static function get($filename, $objectForMap = true, $ext = "php")
    {
        $config = require self::getConfigDir().DIRECTORY_SEPARATOR.$filename.'.'.$ext;
        if($objectForMap){
            $config = json_encode($config);
        }

        return $config;
        /* try{
            $items = Yaml::parse(
                file_get_contents(self::getConfigDir().DIRECTORY_SEPARATOR.$filename.'.yml'),
                true, true, $objectForMap
            );
            return $items;
        }catch (ParseException $exception){
            printf("Unable to parse the YAML string: %s", $exception->getMessage());
            return false;
        } */
    }
    
    public static function getResourcesDir()
    {
        return _RESOURCES_DIR_;
    }
    
    public static function getAppDir()
    {
        return _APP_DIR_;
    }
    
    public static function getViewsDir()
    {
        return _VIEWS_DIR_;
    }
    
    public static function getConfigDir()
    {
        return _CONFIG_DIR_;
    }
    
    public static function getPublicDir()
    {
        return _PUBLIC_DIR_;
    }
}