<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

/**
 * MPL widget
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update
 */
class WBMPL_post_listing_widget extends WBMPL_widgets
{
	private $tplpath = 'app.widgets.MPL.tmpl';
    private $formspath = 'app.widgets.MPL.forms';
    private $assetspath = 'app.widgets.MPL.assets';
    private $widgetpath = 'app.widgets.MPL';
	
    public function __construct()
	{
        /* Widget settings. */
		$widgetOptions = array('classname'=>'WBMPL_post_listing_widget', 'description'=>__('Magic Post Listing', WBMPL_TEXTDOMAIN));

		/* Widget control settings. */
		$controlOptions = array('width'=>200, 'height'=>400, 'id_base'=>'WBMPL_post_listing_widget');
        
		parent::__construct('WBMPL_post_listing_widget', __('Magic Post Listing', WBMPL_TEXTDOMAIN), $widgetOptions, $controlOptions);
	}

	/**
	 * How to display the widget on the screen.
	 */
	public function widget($args, $instance)
	{
        $this->widget_id = $this->number;
        if($this->widget_id < 0) $this->widget_id = abs($this->widget_id)+1000;
        
        $before_widget = '';
        $after_widget = '';
        $before_title = '';
        $after_title = '';
        
		extract($args);
		
		/* The variables from the widget settings. */
        $widget_title = $this->posts->render_widget_title(apply_filters('widget_title', $instance['widget_title']), $instance);
		$widget_title_url = $instance['widget_title_url'];
		$post_type = $instance['post_type'];
        
        $query = $this->posts->get_query($instance);
		$posts = $this->db->select($query);
		$rendered = $this->posts->render($posts, $instance);
        
        $layout_name = str_replace('.php', '', $instance['display_layout']);
        $assets_path = $this->main->import($this->assetspath.'.'.$layout_name.'.'.$layout_name, true, true);
        $layout_path = $this->main->import($this->tplpath.'.'.$layout_name, true, true);
        
		/* Before widget (defined by themes) */
		echo $before_widget;
        
        if($this->file->exists($assets_path)) include $assets_path;
        $this->posts->generate_dynamic_styles($instance, $this->widget_id);
		include $layout_path;
		
		/* After widget (defined by themes) */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	public function update($new_instance, $old_instance)
	{
		$instance = array();
		
		$instance['show_widget_title'] = in_array($new_instance['show_widget_title'], array('1','0')) ? $new_instance['show_widget_title'] : 1;
		
		/* Strip tags for title and title url to remove HTML. */
		$instance['widget_title'] = strip_tags($new_instance['widget_title']);
		$instance['widget_title_url'] = strip_tags($new_instance['widget_title_url']);
        
		$instance['widget_css_classes'] = trim($new_instance['widget_css_classes']) != '' ? $new_instance['widget_css_classes'] : '';
		$instance['widget_main_color'] = trim($new_instance['widget_main_color']) != '' ? $new_instance['widget_main_color'] : '';
        $instance['widget_main_color_ignore'] = isset($new_instance['widget_main_color_ignore']) ? 1 : 0;
		$instance['widget_url_target'] = in_array($new_instance['widget_url_target'], array('_self','_blank')) ? $new_instance['widget_url_target'] : '_self';
        
		$instance['post_authors'] = trim($new_instance['post_authors']) != '' ? $new_instance['post_authors'] : '';
		$post_type = isset($new_instance['post_type']) ? $new_instance['post_type'] : 'post';
        $instance['post_type'] = $post_type;
        
        /** custom post type options **/
        if(!in_array($post_type, array('post', 'page')))
        {
            foreach($new_instance as $key=>$value)
            {
                if(strpos($key, 'cpost_'.$post_type.'_') === false) continue;
                $instance[$key] = trim($value) != '' ? $value : '';
            }
        }
        
		/** page option **/
		$instance['parent_page'] = trim($new_instance['parent_page']) != '' ? $new_instance['parent_page'] : '0';
        $instance['include_page_ids'] = trim($new_instance['include_page_ids']) != '' ? $new_instance['include_page_ids'] : '';
		$instance['exclude_page_ids'] = trim($new_instance['exclude_page_ids']) != '' ? $new_instance['exclude_page_ids'] : '';
		
		/** post option **/
		$instance['post_categories'] = trim($new_instance['post_categories']) != '' ? $new_instance['post_categories'] : '';
        $instance['include_current_category'] = isset($new_instance['include_current_category']) ? 1 : 0;
        
		$instance['post_tags'] = trim($new_instance['post_tags']) != '' ? $new_instance['post_tags'] : '';
        $instance['include_current_tag'] = isset($new_instance['include_current_tag']) ? 1 : 0;
        
		$instance['include_post_ids'] = trim($new_instance['include_post_ids']) != '' ? $new_instance['include_post_ids'] : '';
		$instance['exclude_post_ids'] = trim($new_instance['exclude_post_ids']) != '' ? $new_instance['exclude_post_ids'] : '';
		
        $instance['exclude_current_post'] = isset($new_instance['exclude_current_post']) ? 1 : 0;
        
		/** size and order options **/
		$instance['listing_orderby'] = in_array($new_instance['listing_orderby'], array('post_date','post_modified','comment_count')) ? $new_instance['listing_orderby'] : 'post_date';
		$instance['listing_order'] = in_array($new_instance['listing_order'], array('ASC','DESC')) ? $new_instance['listing_order'] : 'ASC';
		$instance['listing_size'] = trim($new_instance['listing_size']) != '' ? $new_instance['listing_size'] : 6;
		
		/** thumbnail options **/
		$instance['thumb_show'] = in_array($new_instance['thumb_show'], array('0','1')) ? $new_instance['thumb_show'] : '0';
		$instance['thumb_width'] = trim($new_instance['thumb_width']) != '' ? $new_instance['thumb_width'] : 100;
		$instance['thumb_height'] = trim($new_instance['thumb_height']) != '' ? $new_instance['thumb_height'] : 100;
		$instance['thumb_link'] = in_array($new_instance['thumb_link'], array('0','1')) ? $new_instance['thumb_link'] : '0';
        $instance['thumb_skip'] = trim($new_instance['thumb_skip']) ? $new_instance['thumb_skip'] : 0;
		
		/** item title options **/
		$instance['display_show_title'] = in_array($new_instance['display_show_title'], array('0','1')) ? $new_instance['display_show_title'] : '1';
		$instance['display_link_title'] = isset($new_instance['display_link_title']) ? 1 : 0;
		$instance['display_cut_title_size'] = trim($new_instance['display_cut_title_size']) != '' ? $new_instance['display_cut_title_size'] : 100;
		$instance['display_cut_title_mode'] = in_array($new_instance['display_cut_title_mode'], array('0','1','2')) ? $new_instance['display_cut_title_mode'] : '1';
        $instance['display_title_html_tag'] = in_array($new_instance['display_title_html_tag'], array('','h2','h3','h4','h5','h6','strong')) ? $new_instance['display_title_html_tag'] : '';
		
		/** item content options **/
		$instance['display_show_content'] = in_array($new_instance['display_show_content'], array('0','1')) ? $new_instance['display_show_content'] : '1';
		$instance['display_link_content'] = isset($new_instance['display_link_content']) ? 1 : 0;
		$instance['display_cut_content_size'] = trim($new_instance['display_cut_content_size']) != '' ? $new_instance['display_cut_content_size'] : 100;
		$instance['display_cut_content_mode'] = in_array($new_instance['display_cut_content_mode'], array('0','1','2')) ? $new_instance['display_cut_content_mode'] : '1';
		
		/** item author options **/
		$instance['display_show_author'] = in_array($new_instance['display_show_author'], array('0','1')) ? $new_instance['display_show_author'] : '0';
		$instance['display_link_author'] = isset($new_instance['display_link_author']) ? 1 : 0;
		$instance['display_author_label'] = trim($new_instance['display_author_label']) != '' ? $new_instance['display_author_label'] : '';
		
		/** item date options **/
		$instance['display_show_date'] = in_array($new_instance['display_show_date'], array('0','1')) ? $new_instance['display_show_date'] : '1';
		$instance['display_date_format'] = trim($new_instance['display_date_format']) != '' ? $new_instance['display_date_format'] : "Default";
		$instance['display_date_label'] = trim($new_instance['display_date_label']) != '' ? $new_instance['display_date_label'] : '';
		
		/** item category options **/
		$instance['display_show_category'] = in_array($new_instance['display_show_category'], array('0','1')) ? $new_instance['display_show_category'] : '0';
		$instance['display_category_link'] = isset($new_instance['display_category_link']) ? 1 : 0;
        $instance['display_category_label'] = trim($new_instance['display_category_label']) != '' ? $new_instance['display_category_label'] : '';
		$instance['display_category_separator'] = trim($new_instance['display_category_separator']) != '' ? $new_instance['display_category_separator'] : '';
		
		/** item tags options **/
		$instance['display_show_tags'] = in_array($new_instance['display_show_tags'], array('0','1')) ? $new_instance['display_show_tags'] : '0';
		$instance['display_tags_link'] = isset($new_instance['display_tags_link']) ? 1 : 0;
        $instance['display_tags_label'] = trim($new_instance['display_tags_label']) != '' ? $new_instance['display_tags_label'] : '';
		$instance['display_tags_separator'] = trim($new_instance['display_tags_separator']) != '' ? $new_instance['display_tags_separator'] : '';
		
		/** item string break options **/
		$instance['display_show_string_break'] = in_array($new_instance['display_show_string_break'], array('0','1')) ? $new_instance['display_show_string_break'] : '1';
		$instance['display_string_break_str'] = trim($new_instance['display_string_break_str']) != '' ? $new_instance['display_string_break_str'] : '';
		$instance['display_string_break_img'] = trim($new_instance['display_string_break_img']) != '' ? $new_instance['display_string_break_img'] : '';
		$instance['display_link_string_break'] = isset($new_instance['display_link_string_break']) ? 1 : 0;
		
		/** advanced options **/
		$instance['allowed_html_tags'] = trim($new_instance['allowed_html_tags']) != '' ? $new_instance['allowed_html_tags'] : '';
		$instance['no_post_default_text'] = trim($new_instance['no_post_default_text']) != '' ? $new_instance['no_post_default_text'] : '';
		
		/** layout options **/
		$possible_widget_layouts = $this->layouts();
		$instance['display_layout'] = in_array($new_instance['display_layout'], $possible_widget_layouts) ? $new_instance['display_layout'] : 'default.php';
		
		foreach($new_instance as $key=>$value)
		{
			if(strpos($key, 'layout_') === false) continue;
			$instance[$key] = $value;
		}
		
		/** advanced options **/
		$codes = $this->posts->generate_codes($instance);
		
		$instance['shortcode'] = $codes['shortcode'];
		$instance['phpcode'] = $codes['phpcode'];
        
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 */
	public function form($instance)
	{
        $this->widget_id = $this->number;
        if($this->widget_id < 0) $this->widget_id = abs($this->widget_id)+1000;
        
		/** Set up some default widget settings. **/
		$defaults = $this->posts->get_default_args();
		$instance = wp_parse_args((array) $instance, $defaults);
		$post_types = $this->posts->get_post_types();
        $path = $this->main->import($this->widgetpath.'.form', true, true);
		
		ob_start();
		include $path;
		echo $output = ob_get_clean();
	}
	
	public function layouts()
	{
		return $this->folder->files(_WBMPL_ABSPATH_ .DS. 'app' .DS. 'widgets' .DS. 'MPL' .DS. 'tmpl', '.php$', false, false);
	}
}