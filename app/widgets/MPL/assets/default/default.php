<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

$css = '<style type="text/css">
#'.$this->posts->get_container_id($this->widget_id).' ul li{margin: 0 0 15px;}
#'.$this->posts->get_container_id($this->widget_id).' ul li p{margin: 0;}
</style>';

$this->factory->params('footer', $css);

/** Responsive cares for Grid **/
if((isset($instance['layout_display']) and $instance['layout_display'] == '2'))
{
    $javascript = '<script type="text/javascript">
    jQuery(document).ready(function()
    {
        var windowSize = jQuery(window).width();
        if(windowSize <= 800 && windowSize > 480)
        {
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").removeClass("wbmpl_grid'.$instance['layout_grid_size'].'");
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").addClass("wbmpl_grid2");
        }
        else if(windowSize <= 480)
        {
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").removeClass("wbmpl_grid'.$instance['layout_grid_size'].'");
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").addClass("wbmpl_grid1");
        }
    });
    
    jQuery(window).resize(function()
    {
        var windowSize = jQuery(window).width();
        if(windowSize > 800)
        {
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").removeClass("wbmpl_grid2");
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").removeClass("wbmpl_grid1");
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").addClass("wbmpl_grid'.$instance['layout_grid_size'].'");
        }
        else if(windowSize <= 800 && windowSize > 480)
        {
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").removeClass("wbmpl_grid'.$instance['layout_grid_size'].'");
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").removeClass("wbmpl_grid1");
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").addClass("wbmpl_grid2");
        }
        else if(windowSize <= 480)
        {
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").removeClass("wbmpl_grid2");
            jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").addClass("wbmpl_grid1");
        }
    });
    </script>';

    $this->factory->params('footer', $javascript);
}