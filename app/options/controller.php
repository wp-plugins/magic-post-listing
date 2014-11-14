<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

class WBMPL_options_controller extends WBMPL_controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        $this->import('app.options.tmpl.default');
    }
}