<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

class WBMPL_widgets extends WP_Widget
{
    function __construct($widget_id = null, $widget_name = '', $options = array())
	{
        /** WBMPL app Objects **/
        $this->main = WBMPL::getInstance('app.libraries.main');
        
        $this->posts = $this->main->getPosts();
        $this->request = $this->main->getRequest();
        $this->db = $this->main->getDB();
        $this->file = $this->main->getFile();
        $this->folder = $this->main->getFolder();
        $this->factory = $this->main->getFactory();
        
        /** AJAX actions **/
        add_action('wp_ajax_wbmpl_widget_layout_form', array($this, 'render_layout_form'));
        
		parent::__construct($widget_id, $widget_name, $options);
	}
    
    public function render_layout_form()
    {
        $this->id_base = $this->request->getVar('id_base', NULL);
		$this->number = $this->request->getVar('number', NULL);
		$layout = $this->request->getVar('layout', NULL);
		
        $path = $this->main->import('app.widgets.MPL.forms.'.str_replace('.php', '', $layout), true, true);
		if(!$this->file->exists($path)) $path = $this->main->import('app.widgets.MPL.forms.default', true, true);
		
		/** generate instance **/
		$result = $this->main->get_option('widget_wbmpl_post_listing_widget');
		$instance = $result[$this->number];
		
		ob_start();
		include $path;
		echo $output = ob_get_clean();
        exit;
    }
    
    public function get_field_id($field_name)
	{
		return 'widget-' . $this->id_base . '-' . $this->number . '-' . $field_name;
	}
	
	public function get_field_name($field_name)
	{
		return 'widget-' . $this->id_base . '[' . $this->number . '][' . $field_name . ']';
	}
    
    public function pages_selectbox($args = array(), $params = array())
    {
        $default_args = array('echo'=>false, 'show_option_none'=>__('Root', WBMPL_TEXTDOMAIN), 'option_none_value'=>'0');
        $args = array_merge($default_args, $args);
        
        $html = wp_dropdown_pages($args);
        if(isset($params['class'])) $html = str_replace('<select', '<select class="'.$params['class'].'"', $html);
        
        $html = str_replace('">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '">------', $html);
        $html = str_replace('">&nbsp;&nbsp;&nbsp;&nbsp;', '">----', $html);
        $html = str_replace('">&nbsp;&nbsp;', '">--', $html);
        
        return $html;
    }
    
    public function categories_selectbox($args = array(), $params = array())
    {
        $default_args = array('echo'=>false, 'show_option_none'=>__('-----', WBMPL_TEXTDOMAIN), 'option_none_value'=>'-1', 'hierarchical'=>true);
        $args = array_merge($default_args, $args);
        
        $html = wp_dropdown_categories($args);
        if(isset($params['class'])) $html = str_replace('<select', '<select class="'.$params['class'].'"', $html);
        
        $html = str_replace('">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '">------', $html);
        $html = str_replace('">&nbsp;&nbsp;&nbsp;&nbsp;', '">----', $html);
        $html = str_replace('">&nbsp;&nbsp;', '">--', $html);
        
        return $html;
    }
}