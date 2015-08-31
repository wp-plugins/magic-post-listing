<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

/**
 * Webilia MPL main class
 * @author Webilia <info@webilia.com>
 */
class WBMPL
{
    /**
     * Constructor method
     * @author Webilia <info@webilia.com>
     */
    protected function __construct()
    {
        /** Define MPL Text Domain for localization **/
        if(!defined('WBMPL_TEXTDOMAIN')) define('WBMPL_TEXTDOMAIN', 'wbmpl');
        
        /** MPL Version **/
        if(!defined('WBMPL_VERSION')) define('WBMPL_VERSION', '2.0');
    }
    
    private function __clone()
    {
    }
    
    private function __wakeup()
    {
    }
    
    /**
     * Getting instance. This Class is a singleton class
     * @author Webilia <info@webilia.com>
     * @staticvar object $instance
     * @return \static
     */
    public static function instance()
	{
        static $instance = null;
        if(null === $instance) $instance = new static();
        
        return $instance;
	}
    
    /**
     * This method initialize the MPL, This add WordPress Actions, Filters and Widgets
     * @author Webilia <info@webilia.com>
     */
    public function init()
    {
        // Import Base library
        $this->import('app.libraries.base');
        
        // Import Widget Class
        $this->import('app.libraries.widgets');
        
        // Import MPL Factory, This file will do the rest
        $factory = WBMPL::getInstance('app.libraries.factory');
        
        // Registering MPL actions
        $factory->load_actions();
        
        // Registering MPL filter methods
        $factory->load_filters();
        
        // Registering MPL hooks such as activate, deactivate and uninstall hooks
        $factory->load_hooks();
        
        // Register MPL Widget
        $factory->action('widgets_init', array($factory, 'load_widgets'));
        
        // Include needed assets (CSS, JavaScript etc) in the WordPress backend
        $factory->action('admin_enqueue_scripts', array($factory, 'load_backend_assets'), 0);
        
        // Include needed assets (CSS, JavaScript etc) in the website frontend
		$factory->action('wp_enqueue_scripts', array($factory, 'load_frontend_assets'), 0);
        
        // Register the shortcodes
        $factory->action('init', array($factory, 'load_shortcodes'));
        
        // Register language files for localization
        $factory->action('init', array($factory, 'load_languages'));
    }
    
    /**
     * Getting a instance of a MPL library
     * @author Webilia <info@webilia.com>
     * @static
     * @param string $file
     * @param string $class_name
     * @return object|boolean
     */
    public static function getInstance($file, $class_name = NULL)
    {
        /** Import the file using import method **/
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
        else return false;
    }
    
    /**
     * Imports the MPL file
     * @author Webilia <info@webilia.com>
     * @static
     * @param string $file Use 'app.libraries.base' for including /path/to/plugin/app/libraries/base.php file
     * @param boolean $override include overridden file or not (if exists)
     * @param boolean $return_path Return the file path or not
     * @return boolean|string
     */
    public static function import($file, $override = true, $return_path = false)
    {
        // Converting the MPL path to normal path (app.libraries.base to /path/to/plugin/app/libraries/base.php)
        $original_exploded = explode('.', $file);
        $file = implode(DS, $original_exploded) . '.php';
        
        $path = _WBMPL_ABSPATH_ . $file;
        $overridden = false;
        
        // Including override file from theme
        if($override)
        {
            // Search the file in the main theme
            $wp_theme_path = get_template_directory();
            $theme_path = $wp_theme_path .DS. 'webilia' .DS. _WBMPL_BASENAME_ .DS. $file;
            
            /**
             * If overridden file exists on the main theme, then use it instead of normal file
             * For example you can override /path/to/plugin/app/libraries/base.php file in your theme by adding a file into the /path/to/theme/webilia/magic-post-listing-pro/app/libraries/base.php
             */
            if(file_exists($theme_path))
            {
                $overridden = true;
                $path = $theme_path;
            }

            // If the theme is a child theme then search the file in child theme
            if(is_child_theme())
            {
                // Child theme overriden file
                $child_theme_path = get_stylesheet_directory() .DS. 'webilia' .DS. _WBMPL_BASENAME_ .DS. $file;

                /**
                * If overridden file exists on the child theme, then use it instead of normal or main theme file
                * For example you can override /path/to/plugin/app/libraries/base.php file in your theme by adding a file into the /path/to/child/theme/webilia/magic-post-listing-pro/app/libraries/base.php
                */
                if(file_exists($child_theme_path))
                {
                    $overridden = true;
                    $path = $child_theme_path;
                }
            }
        }
        
        // Return the file path without importing it
        if($return_path) return $path;
        
        // Import the file and return override status
        if(file_exists($path)) require_once $path;
        return $overridden;
    }
}