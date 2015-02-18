<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

class WBMPL_posts extends WBMPL_base
{
    public function __construct()
    {
        $this->posts = $this;
        $this->main = $this->getMain();
        $this->db = $this->getDB();
        $this->file = $this->getFile();
        $this->folder = $this->getFolder();
        $this->factory = $this->getFactory();
    }
    
	public function get_query($params)
	{
		$condition1 = "`post_status`='publish'";
        $condition2 = "";
		
		/** include post type to query **/
		if(trim($params['post_type']) != '') $condition1 .= " AND `post_type`='".$params['post_type']."'";
		
		/** include post authors to query **/
		if(trim($params['post_authors']) != '') $condition1 .= " AND `post_author` IN (".$params['post_authors'].")";
        
        /** exclude current post from query **/
		if(trim($params['exclude_current_post']) and is_singular())
        {
            $current_post_id = get_queried_object_id();
            if($current_post_id) $condition1 .= " AND `ID` NOT IN (".$current_post_id.")";
        }
		
		/** include post queries **/
		if($params['post_type'] == 'post')
		{
			if(trim($params['post_categories']) != '' and trim($params['post_categories']) != '-1') $condition1 .= " AND `ID` IN (SELECT `object_id` FROM `#__term_relationships` WHERE `term_taxonomy_id` IN (".$this->get_taxonomy_ids($params['post_categories'])."))";
			if(trim($params['post_tags']) != '')
            {
                $condition1 .= " AND `ID` IN (SELECT `object_id` FROM `#__term_relationships` WHERE `term_taxonomy_id` IN (".$this->get_taxonomy_ids_by_names($params['post_tags'])."))";
            }
			
			if(trim($params['include_post_ids']) != '') $condition2 .= " OR `ID` IN (".$params['include_post_ids'].")";
			if(trim($params['exclude_post_ids']) != '') $condition1 .= " AND `ID` NOT IN (".$params['exclude_post_ids'].")";
		}
		/** include page queries **/
		elseif($params['post_type'] == 'page')
		{
			$condition1 .= " AND `post_parent`='".$params['parent_page']."'";
			if(trim($params['include_page_ids']) != '') $condition2 .= " OR `ID` IN (".$params['include_page_ids'].")";
			if(trim($params['exclude_page_ids']) != '') $condition1 .= " AND `ID` NOT IN (".$params['exclude_page_ids'].")";
		}
        /** include custom post type queries **/
        else
        {
            $post_type = $params['post_type'];
            foreach($params as $key=>$value)
            {
                if(trim($value) == '' or $value == '-1') continue;
                if(strpos($key, 'cpost_'.$post_type.'_terms_') === false) continue;
                
                $condition1 .= " AND `ID` IN (SELECT `object_id` FROM `#__term_relationships` WHERE `term_taxonomy_id` IN (".$this->get_taxonomy_ids($value)."))";
            }
			
			if(trim($params['cpost_'.$post_type.'_include_post_ids']) != '') $condition2 .= " OR `ID` IN (".$params['cpost_'.$post_type.'_include_post_ids'].")";
			if(trim($params['cpost_'.$post_type.'_exclude_post_ids']) != '') $condition1 .= " AND `ID` NOT IN (".$params['cpost_'.$post_type.'_exclude_post_ids'].")";
        }
		
        $condition2 = trim($condition2, 'OR ');
        
		/** order and size **/
		$order_limit = " ORDER BY `".($params['listing_orderby'] ? $params['listing_orderby'] : 'post_date')."` ".($params['listing_order'] ? $params['listing_order'] : 'DESC')." LIMIT ".($params['listing_size'] ? $params['listing_size'] : 10);
		return $query = "SELECT * FROM `#__posts` WHERE (".$condition1.") ".(trim($condition2) != '' ? " OR (".$condition2.")" : '').$order_limit;
	}
	
	public function get_taxonomy_ids($term_ids)
	{
		$query = "SELECT `term_taxonomy_id` FROM `#__term_taxonomy` WHERE `term_id` IN (".$term_ids.")";
		$taxonomy_ids = $this->db->select($query, 'loadAssocList');
		
		$taxonomy_str = '';
		foreach($taxonomy_ids as $taxonomy_id)
		{
			$taxonomy_str .= $taxonomy_id['term_taxonomy_id'].",";
		}
		
		return trim($taxonomy_str, ', ');
	}
	
	public function get_term_ids($names)
	{
		$query = "SELECT `term_id` FROM `#__terms` WHERE `name` IN (".$names.")";
		$term_ids = $this->db->select($query, 'loadAssocList');
		
		$term_str = '';
		foreach($term_ids as $term_id)
		{
			$term_str .= $term_id['term_id'].",";
		}
		
		return trim($term_str, ', ');
	}
	
	public function get_taxonomy_ids_by_names($names)
	{
		$ex = explode(',', $names);
		
		$names_str = '';
		foreach($ex as $key=>$value)
		{
			$value = trim($value, "' ");
			$names_str .= "'".trim($value)."',";
		}
		
		return $this->get_taxonomy_ids($this->get_term_ids(trim($names_str, ', ')));
	}
	
	public function get_categories($post_id)
	{
		$post_categories = wp_get_post_categories($post_id);
		$cats = array();
		
		foreach($post_categories as $c)
		{
          	$cat = get_category($c);
			$cats[] = array('id'=>$c, 'name'=>$cat->name, 'slug'=>$cat->slug, 'link'=>get_category_link($c));
		}
		
		return $cats;
	}
	
	public function get_tags($post_id)
	{
		$post_tags = wp_get_post_tags($post_id);
        $tags = array();
		
		foreach($post_tags as $tag)
		{
			$tags[] = array('id'=>$tag->term_id, 'name'=>$tag->name, 'slug'=>$tag->slug, 'link'=>get_tag_link($tag->term_id));
		}
		
		return $tags;
	}
	
	public function get_thumbnail($post_id, $size = array(100, 100))
	{
		return get_the_post_thumbnail($post_id, $size);
	}
	
	public function get_post_url($post_id)
	{
		return get_permalink($post_id);
	}
    
	public function render($posts, $instance = array())
	{
		$rendered = array();
		foreach($posts as $post)
		{
			$post_id = $post->ID;
            $author_id = $post->post_author;
            
			$rendered[$post_id] = (array) $post;
			$rendered[$post_id]['rendered']['thumbnail'] = $this->get_thumbnail($post_id, array($instance['thumb_width'], $instance['thumb_height']));
			$rendered[$post_id]['rendered']['link'] = $this->get_post_url($post_id);
		}
        
		return $rendered;
	}
	
	public function get_default_args()
	{
		return array(
			  'show_widget_title'=>'1', 'widget_title'=>'Related Posts', 'widget_title_url'=>'', 'widget_url_target'=>'_self', 'widget_css_classes'=>'', 'widget_main_color'=>'#345d81',
              'widget_main_color_ignore'=>0, 'post_type'=>'post','post_authors'=>'', 'listing_orderby'=>'post_date', 'listing_order'=>'DESC', 'listing_size'=>'10', 'include_page_ids'=>'',
              'parent_page'=>'0', 'exclude_page_ids'=>'', 'post_categories'=>'-1', 'post_tags'=>'', 'include_post_ids'=>'', 'exclude_post_ids'=>'', 'cpost'=>array(), 'exclude_current_post'=>'1',
			  'thumb_show'=>'1', 'thumb_width'=>'100', 'thumb_height'=>'100', 'thumb_link'=>'1',
			  'display_show_title'=>'1', 'display_link_title'=>'0', 'display_cut_title_size'=>'100', 'display_cut_title_mode'=>'1',
			  'display_show_content'=>'1', 'display_link_content'=>'0', 'display_cut_content_size'=>'300', 'display_cut_content_mode'=>'1',
			  'display_show_author'=>'1', 'display_link_author'=>'0', 'display_author_label'=>'',
			  'display_show_date'=>'0', 'display_date_format'=>'Default', 'display_date_label'=>'Date: ',
			  'display_show_category'=>'0', 'display_category_link'=>'0', 'display_category_label'=>'', 'display_category_separator'=>'',
			  'display_show_tags'=>'0', 'display_tags_link'=>'0', 'display_tags_label'=>'', 'display_tags_separator'=>'',
			  'display_show_string_break'=>'1', 'display_string_break_str'=>'...', 'display_string_break_img'=>'', 'display_link_string_break'=>'1',
			  'allowed_html_tags'=>'', 'no_post_default_text'=>'No posts!', 'display_layout'=>'default.php'
		);
	}
	
	public function generate_codes($instance)
	{
		$shortcode = '';
		$phpcode = '';
		$defaults = $this->get_default_args();
		
		foreach($instance as $key=>$value)
		{
			if(in_array($key, array('shortcode', 'phpcode')) or (isset($defaults[$key]) and $defaults[$key] == $value) or (trim($value) == '')) continue;
            
            $shortcode .= ' '.$key.'="'.$value.'"';
            $phpcode .= "'".$key."'=>'".$value."', ";
		}
		
		$shortcode = '[WBMPL'.(trim($shortcode) ? ' '.trim($shortcode) : '').']';
		
		$php_str  = '<?php'.PHP_EOL;
		$php_str .= '$params = array('.trim($phpcode, ", ").');'.PHP_EOL;
        $php_str .= '$WBMPL_pro = WBMPL::getInstance("app.libraries.pro");'.PHP_EOL;
		$php_str .= 'echo $WBMPL_pro->mpl($params);'.PHP_EOL;
		$php_str .= '?>';
		$phpcode = $php_str;
		
		return array('shortcode'=>$shortcode, 'phpcode'=>$phpcode);
	}
	
	public function render_title($title, $instance, $post)
	{
		/** get default instance **/
		if(!$instance) $instance = $this->get_default_args();
		if(!$instance['display_show_title']) return '';
		
		$title = strip_tags($title, $instance['allowed_html_tags']);
		$need_to_cut = false;
		
		if($instance['display_cut_title_mode'] == 1) $cutted = substr($title, 0, $instance['display_cut_title_size']);
		elseif($instance['display_cut_title_mode'] == 2)
        {
            $ex = explode(' ', $title);
            $ex = array_slice($ex, 0, $instance['display_cut_title_size']);
            $cutted = implode(' ', $ex);
        }
        
		if($title != $cutted)
		{
			$title = $cutted;
			$need_to_cut = true;
		}
        
		$break_str = '';
		if($instance['display_show_string_break']) $break_str = trim($instance['display_string_break_img']) != '' ? '<img src="'.$instance['display_string_break_img'].'" class="wbpml_list_break_image" />' : $instance['display_string_break_str'];
		
		if($instance['display_link_title'])
		{
			if($need_to_cut)
			{
				if($instance['display_link_string_break']) $title = '<a href="'.$post['rendered']['link'].'" target="'.$instance['widget_url_target'].'" class="wbpml_list_title_link">'.$title." ".$break_str.'</a>';
				else $title = '<a href="'.$post['rendered']['link'].'" target="'.$instance['widget_url_target'].'" class="wbpml_list_title_link">'.$title.'</a> '.$break_str;
			}
			else $title = '<a href="'.$post['rendered']['link'].'" target="'.$instance['widget_url_target'].'" class="wbpml_list_title_link">'.$title.'</a>';
		}
		else
		{
			if($need_to_cut)
			{
				if($instance['display_link_string_break']) $title = $title.' <a href="'.$post['rendered']['link'].'" target="'.$instance['widget_url_target'].'" class="wbpml_list_string_break_link">'.$break_str.'</a>';
				else $title = $title." ".$break_str;
			}
		}
        
		return $this->close_html_tags($title);
	}
	
    public function render_date($post_id, $instance)
    {
        if(strtolower($instance['display_date_format']) == 'default') $format = get_option('date_format');
        else $format = $instance['display_date_format'];
        
		$date = get_the_date($format, $post_id);
        if(trim($instance['display_date_label'])) $date = __($instance['display_date_label'], WBMPL_TEXTDOMAIN).' '.$date;
        
        return $date;
    }
    
    public function render_field($post_id, $key, $single = false)
    {
		return get_post_meta($post_id, $key, $single);
    }
    
    public function render_author($author_id, $instance)
    {
        $link = get_author_posts_url($author_id);
        
        $display_name = get_the_author_meta('display_name', $author_id);
        if(trim($display_name)) $display_name = get_the_author_meta('nickname', $author_id);
        
        $author = '';
        if($instance['display_link_author']) $author = '<a href="'.$link.'" target="'.$instance['widget_url_target'].'">'.$display_name.'</a>';
        else $author = $display_name;
        
        if(trim($instance['display_author_label'])) $author = __($instance['display_author_label'], WBMPL_TEXTDOMAIN).' '.$author;
        
        return $author;
    }
    
    public function render_categories($post_id, $instance)
    {
        $categories = $this->get_categories($post_id);
        
        $str = '';
        if(!count($categories)) return $str;
        
        $separator = trim($instance['display_category_separator']) ? $instance['display_category_separator'] : ', ';
        foreach($categories as $category)
        {
            $category_name = __($category['name'], WBMPL_TEXTDOMAIN);
            if($instance['display_category_link']) $category_name = '<a href="'.$category['link'].'" target="'.$instance['widget_url_target'].'">'.$category_name.'</a>';
            
            $str .= $category_name.$separator;
        }
        
        if(trim($instance['display_category_label'])) $str = __($instance['display_category_label'], WBMPL_TEXTDOMAIN).$str;
        
        return trim($str, $separator);
    }
    
    public function render_tags($post_id, $instance)
    {
        $tags = $this->get_tags($post_id);
        
        $str = '';
        if(!count($tags)) return $str;
        
        $separator = trim($instance['display_tags_separator']) ? $instance['display_tags_separator'] : ', ';
        foreach($tags as $tag)
        {
            $tag_name = __($tag['name'], WBMPL_TEXTDOMAIN);
            if($instance['display_tags_link']) $tag_name = '<a href="'.$tag['link'].'" target="'.$instance['widget_url_target'].'">'.$tag_name.'</a>';
            
            $str .= $tag_name.$separator;
        }
        
        if(trim($instance['display_tags_label'])) $str = __($instance['display_tags_label'], WBMPL_TEXTDOMAIN).$str;
        
        return trim($str, $separator);
    }
    
	public function render_thumbnail($thumbnail, $instance, $post)
	{
		/** get default instance **/
		if(!$instance) $instance = $this->get_default_args();
		if(!$instance['thumb_show']) return '';
		
		if($instance['thumb_link']) $thumbnail = '<a href="'.$post['rendered']['link'].'" target="'.$instance['widget_url_target'].'" class="wbpml_list_thumbnail_link">'.$thumbnail.'</a>';
		
		return $thumbnail;
	}
	
	public function render_content($content, $instance, $post)
	{
		/** get default instance **/
		if(!$instance) $instance = $this->get_default_args();
		if(!$instance['display_show_content']) return '';
		
		$content = strip_tags($content, $instance['allowed_html_tags']);
        $content = strip_shortcodes($content);
		$need_to_cut = false;
		
		if($instance['display_cut_content_mode'] == 1) $cutted = substr($content, 0, $instance['display_cut_content_size']);
		elseif($instance['display_cut_content_mode'] == 2)
        {
            $ex = explode(' ', $content);
            $ex = array_slice($ex, 0, $instance['display_cut_title_size']);
            $cutted = implode(' ', $ex);
        }
		
		if($content != $cutted)
		{
			$content = $cutted;
			$need_to_cut = true;
		}
		
		$break_str = '';
		if($instance['display_show_string_break']) $break_str = trim($instance['display_string_break_img']) != '' ? '<img src="'.$instance['display_string_break_img'].'" class="wbpml_list_break_image" />' : $instance['display_string_break_str'];
		
		if($instance['display_link_content'])
		{
			if($need_to_cut)
			{
				if($instance['display_link_string_break']) $content = '<a href="'.$post['rendered']['link'].'" target="'.$instance['widget_url_target'].'" class="wbpml_list_content_link">'.$content." ".$break_str.'</a>';
				else $content = '<a href="'.$post['rendered']['link'].'" target="'.$instance['widget_url_target'].'" class="wbpml_list_content_link">'.$content.'</a> '.$break_str;
			}
			else $content = '<a href="'.$post['rendered']['link'].'" target="'.$instance['widget_url_target'].'" class="wbpml_list_content_link">'.$content.'</a>';
		}
		else
		{
			if($need_to_cut)
			{
				if($instance['display_link_string_break']) $content = $content.' <a href="'.$post['rendered']['link'].'" target="'.$instance['widget_url_target'].'" class="wbpml_list_string_break_link">'.$break_str.'</a>';
				else $content = $content." ".$break_str;
			}
		}
		
		return $this->close_html_tags($content);
	}
    
    public function get_post_types()
    {
        return get_post_types(array('public'=>true, '_builtin'=>false));
    }
    
    public function close_html_tags($html)
	{
        if(!trim($html)) return $html;
        
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        
		@$doc = new DOMDocument();
		@$doc->loadHTML($html);
		@$str = $doc->saveHTML();
		$ex1 = explode('<body>', $str);
		$ex2 = explode('</body>', $ex1[1]);
        
		return $ex2[0];
	}
    
	public function render_widget_title($title, $instance)
	{
		/** If title is hidden **/
		if(!$instance['show_widget_title']) return NULL;
		if($instance['widget_title_url']) $title = '<a href="'.$instance['widget_title_url'].'" target="'.$instance['widget_url_target'].'" class="wbpml_widget_title_link">'.$title.'</a>';
		
		return $title;
	}
    
    public function generate_container_classes($widget_id, $instance)
    {
        $layout_name = str_replace('.php', '', $instance['display_layout']);
        return ($widget_id ? 'id="'.$this->get_container_id($widget_id).'" ' : '').'class="wbmpl_main_container wbmpl_main_container_'.$layout_name.(trim($instance['widget_css_classes']) != '' ? ' '.$instance['widget_css_classes'] : '').'"';
    }
    
    public function generate_dynamic_styles($instance, $widget_id)
    {
        /** ignore applying main color is enabled **/
        if(isset($instance['widget_main_color_ignore']) and $instance['widget_main_color_ignore']) return;
        
        $css = '<style type="text/css">
        #'.$this->posts->get_container_id($widget_id).' .wbmpl_list_title,
        #'.$this->posts->get_container_id($widget_id).' .wbmpl_list_title a,
        #'.$this->posts->get_container_id($widget_id).' .wbmpl_list_author a,
        #'.$this->posts->get_container_id($widget_id).' .wbmpl_list_categories a,
        #'.$this->posts->get_container_id($widget_id).' .wbmpl_list_tags a
        {color: '.$instance['widget_main_color'].'}
        </style>';
        
        $this->factory->params('footer', $css);
    }
    
    public function get_container_id($widget_id)
    {
        return 'wbmpl_main_container'.$widget_id;
    }
}