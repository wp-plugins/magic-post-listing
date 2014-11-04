<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

class WBMPL_main extends WBMPL_base
{
    public function __construct()
    {
    }
    
    public function get_full_url()
	{
		/** get $_SERVER **/
		$server = $this->getRequest()->get('SERVER');
		
		$page_url = 'http';
		if(isset($server['HTTPS']) and $server['HTTPS'] == 'on') $page_url .= 's';
		
        $site_domain = (isset($server['HTTP_HOST']) and trim($server['HTTP_HOST']) != '') ? $server['HTTP_HOST'] : $server['SERVER_NAME'];
        
		$page_url .= '://';
		$page_url .= $site_domain.$server['REQUEST_URI'];
		
		return $page_url;
	}
    
    public function get_authors($args = array())
	{
		return get_users($args);
	}
    
	public function asset($asset)
	{
		return $this->URL('WBMPL').'assets/'.$asset;
	}
    
	public function URL($type = 'site')
	{
		/** make it lowercase **/
		$type = strtolower($type);
		
		if(in_array($type, array('frontend','site'))) $url = site_url().'/';
		elseif(in_array($type, array('backend','admin'))) $url = admin_url();
		elseif($type == 'content') $url = content_url().'/';
		elseif($type == 'plugin') $url = plugins_url().'/';
		elseif($type == 'include') $url = includes_url();
		elseif($type == 'wbmpl')
		{
			if(strpos(_WBMPL_ABSPATH_, 'themes') === false) $url = plugins_url().'/magic-post-listing/';
			else $url = get_template_directory_uri().'/plugins/magic-post-listing/';
		}
		
		return $url;
	}
    
    public function get_option($option, $default = NULL)
    {
        return get_option($option, $default);
    }
    
    public function pro_messages($type = 'upgrade')
    {
        $message = '';
        if($type == 'upgrade') $message = '<p class="wbmpl_upgrade_message">'.__('This feature is included in Magic Post Listing (MPL) PRO.', WBMPL_TEXTDOMAIN).'</p>';
        elseif($type == 'more_layouts') $message = '<p class="wbmpl_more_layouts_message">'.sprintf(__('By upgrading to %s you can use more layouts. Click %s to see demos.', WBMPL_TEXTDOMAIN), '<a href="http://webilia.com/plugins/MPL/" target="_blank">'.__('MPL PRO', WBMPL_TEXTDOMAIN).'</a>', '<a href="http://webilia.com/plugins/MPL/" target="_blank">'.__('here', WBMPL_TEXTDOMAIN).'</a>').'</p>';
        
        return $message;
    }
}