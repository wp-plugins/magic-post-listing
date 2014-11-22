<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

$css = '<style type="text/css">
#'.$this->posts->get_container_id($this->widget_id).' ul li{margin: 0 0 15px;}
#'.$this->posts->get_container_id($this->widget_id).' ul li p{margin: 0;}
</style>';

$this->factory->params('footer', $css);