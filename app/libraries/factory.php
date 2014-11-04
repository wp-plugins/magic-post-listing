<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

class WBMPL_factory extends WBMPL_base
{
    public static $params = array();
    
    public function __construct()
    {
        $this->main = $this->getMain();
        $this->file = $this->getFile();
        
        $this->import('app.controller');
    }
    
    public function load_actions()
    {
        $this->action('wp_footer', array($this, 'load_footer'), 9999);
    }
    
    public function load_filters()
    {
    }
    
    public function load_menus()
    {
        $options = WBMPL::getInstance('app.options.controller', 'WBMPL_options_controller');
        add_submenu_page('options-general.php', __('Magic Post Listing', WBMPL_TEXTDOMAIN), __('Magic Post Listing', WBMPL_TEXTDOMAIN), 'manage_options', 'magic-post-listing', array($options, 'index'));
    }
    
    public function load_backend_assets()
    {
        $request = $this->getRequest();
        $page = $request->getVar('page', NULL);
        
        /** jQuery libraries **/
        wp_enqueue_script('jquery');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('wbmpl-backend-script', $this->main->asset('js/backend.js'));
        
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('wbmpl-backend-style', $this->main->asset('css/backend.css'));
    }
    
    public function load_frontend_assets()
    {
        $request = $this->getRequest();
        $page = $request->getVar('page', NULL);
        
        /** jQuery libraries **/
        wp_enqueue_script('jquery');
        wp_enqueue_script('wbmpl-frontend-script', $this->main->asset('js/frontend.js'));
        wp_enqueue_style('wbmpl-frontend-style', $this->main->asset('css/frontend.css'));
    }
    
    public function load_widgets()
    {
        $this->import('app.widgets.MPL.main');
        register_widget('WBMPL_post_listing_widget');
    }
    
    public function load_languages()
    {
        $locale = apply_filters('plugin_locale', get_locale(), WBMPL_TEXTDOMAIN);
		$language_filepath = WP_LANG_DIR.DS._WBMPL_BASENAME_.DS.WBMPL_TEXTDOMAIN.'-'.$locale.'.mo';
        
		if($this->file->exists($language_filepath))
        {
            load_textdomain(WBMPL_TEXTDOMAIN, WP_LANG_DIR.DS._WBMPL_BASENAME_.DS.WBMPL_TEXTDOMAIN.'-'.$locale.'.mo');
        }
		else
        {
			load_plugin_textdomain(WBMPL_TEXTDOMAIN, false, dirname(plugin_basename(__FILE__)).DS.'languages'.DS);
        }
    }
    
    public function load_shortcodes()
    {
        /** generate shortcode **/
        $pro = $this->getPRO();
        if($pro) add_shortcode('WBMPL', array($pro, 'mpl'));
    }
    
    public function params($key = 'footer', $string)
	{
		$string = (string) $string;
		if(trim($string) == '') return false;
		
        if(!isset(self::$params[$key])) self::$params[$key] = array();
        array_push(self::$params[$key], $string);
	}
    
    public function load_footer()
    {
		if(!isset(self::$params['footer']) or (isset(self::$params['footer']) and !count(self::$params['footer']))) return;
        
        foreach(self::$params['footer'] as $key=>$string) echo PHP_EOL.$string.PHP_EOL;
    }
    
    public function action($hook, $function, $priority = 10, $accepted_args = 1)
    {
        /** Check Parameters **/
        if(!trim($hook) or !$function) return false;
        
        return add_action($hook, $function, $priority, $accepted_args);
    }
    
    public function filter($tag, $function, $priority = 10, $accepted_args = 1)
    {
        /** Check Parameters **/
        if(!trim($tag) or !$function) return false;
        
        return add_filter($tag, $function, $priority, $accepted_args);
    }
}