<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

class WBMPL
{
    public static $instance = NULL;
    
    public function __construct()
    {
        if(!defined('WBMPL_TEXTDOMAIN')) define('WBMPL_TEXTDOMAIN', 'wbmpl'); /** Language Textdomain **/
        if(!defined('WBMPL_VERSION')) define('WBMPL_VERSION', '1.0'); /** Version **/
    }
    
    public static function instance($init = true)
	{
		if(!self::$instance)
		{
			self::$instance = new WBMPL();
		}

		return self::$instance;
	}
    
    public function init()
    {
        $this->import('app.libraries.base');
        $this->import('app.libraries.widgets');
        
        /** Factory **/
        $factory = WBMPL::getInstance('app.libraries.factory');
        
        $factory->load_actions();
        $factory->load_filters();
        
        $factory->action('widgets_init', array($factory, 'load_widgets'));
        $factory->action('admin_enqueue_scripts', array($factory, 'load_backend_assets'), 0);
		$factory->action('wp_enqueue_scripts', array($factory, 'load_frontend_assets'), 0);
        $factory->action('init', array($factory, 'load_shortcodes'));
        $factory->action('init', array($factory, 'load_languages'));
    }
    
    public static function getInstance($file, $class_name = NULL)
    {
        /** Import the file **/
        $override = self::import($file);
        
        /** Generate class name if not provided **/
        if(!trim($class_name))
        {
            $ex = explode('.', $file);
            $file_name = end($ex);
            $class_name = 'WBMPL_'.$file_name;
        }
        
        if($override) $class_name .= '_override';
        
        /** Generate the object **/
        if(class_exists($class_name)) return new $class_name();
    }
    
    public static function import($file, $override = true, $return_path = false)
    {
        $original_exploded = explode('.', $file);
        $file = implode(DS, $original_exploded) . '.php';
        
        $path = _WBMPL_ABSPATH_ . $file;
        $override = false;
        
        if($override)
        {
            /** main theme **/
            $wp_theme_path = get_template_directory();
            $theme_path = $wp_theme_path .DS. 'webilia' .DS. _WBMPL_BASENAME_ .DS. $file;
            
            if(file_exists($theme_path))
            {
                $override = true;
                $path = $theme_path;
            }

            /** child theme **/
            $wp_stylesheet = get_option('stylesheet');
            if(strpos($wp_stylesheet, '-child') !== false)
            {
                $child_theme_path = $wp_theme_path.'-child' .DS. 'webilia' .DS. _WBMPL_BASENAME_ .DS. $file;

                if(file_exists($child_theme_path))
                {
                    $override = true;
                    $path = $child_theme_path;
                }
            }
        }

        if($return_path)
        {
            return $path;
        }
        
        if(file_exists($path)) require_once $path;
        return $override;
    }
}