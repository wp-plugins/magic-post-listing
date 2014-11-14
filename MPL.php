<?php
/**
	Plugin Name: Magic Post Listing
	Plugin URI: http://webilia.com
	Description: An awesome plugin for listing the posts and creating posts/pages sliders
	Author: Webilia Team
	Version: 1.1
	Author URI: http://webilia.com
**/

/** MPL Execution **/
define('_WBMPLEXEC_', 1);

/** derectory separator **/
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/** MPL ABS PATH **/
define('_WBMPL_ABSPATH_', dirname(__FILE__) .DS);
define('_WBMPL_BASENAME_', basename(_WBMPL_ABSPATH_));

if(!class_exists('WBMPL')) require_once _WBMPL_ABSPATH_.'WB.php';

$WBMPL = WBMPL::instance();
$WBMPL->init();