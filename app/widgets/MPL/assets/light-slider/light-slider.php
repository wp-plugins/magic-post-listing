<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

wp_enqueue_script('light-slider', $this->main->URL('WBMPL').'app/widgets/MPL/assets/light-slider/js/jquery.light-slider.min.js');
wp_enqueue_style('light-slider', $this->main->URL('WBMPL').'app/widgets/MPL/assets/light-slider/css/light-slider.css');

$javascript = '<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").lightSlider(
    {
        item: '.(isset($instance['layout_item']) ? $instance['layout_item'] : 3).',
        slideMove: '.(isset($instance['layout_slide_move']) ? $instance['layout_slide_move'] : 1).',
        mode: "'.(isset($instance['layout_mode']) ? $instance['layout_mode'] : 'slide').'",
        vertical: '.((isset($instance['layout_vertical']) and $instance['layout_vertical']) ? 'true' : 'false').',
        verticalHeight: '.(isset($instance['layout_vertical_height']) ? $instance['layout_vertical_height'] : 200).',
        pager: '.((isset($instance['layout_pager']) and $instance['layout_pager']) ? 'true' : 'false').',
        controls: '.((isset($instance['layout_controls']) and $instance['layout_controls']) ? 'true' : 'false').',
        loop: '.((isset($instance['layout_loop']) and $instance['layout_loop']) ? 'true' : 'false').',
        auto: '.((isset($instance['layout_auto']) and $instance['layout_auto']) ? 'true' : 'false').',
        rtl: '.((isset($instance['layout_rtl']) and $instance['layout_rtl']) ? 'true' : 'false').',
        speed: '.(isset($instance['layout_speed']) ? $instance['layout_speed'] : 400).',
        pause: '.(isset($instance['layout_pause']) ? $instance['layout_pause'] : 2000).'
    });
});

function wbmpl_light_slider_fix_width'.$this->widget_id.'()
{
    var width = jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").width();
    var increase = parseInt(width)+(parseInt(width)*0.09);
    
    jQuery("#'.$this->posts->get_container_id($this->widget_id).' ul").css("width", increase);
}
</script>';

$this->factory->params('footer', $javascript);