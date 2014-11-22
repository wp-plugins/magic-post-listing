<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

abstract class WBMPL_base extends WBMPL
{
	final public function getDB()
    {
        return WBMPL::getInstance('app.libraries.db');
    }
    
    final public function getPosts()
    {
        return WBMPL::getInstance('app.libraries.posts');
    }
    
    final public function getRequest()
    {
        return WBMPL::getInstance('app.libraries.request');
    }
    
    final public function getFile()
    {
        return WBMPL::getInstance('app.libraries.filesystem', 'WBMPL_file');
    }
    
    final public function getFolder()
    {
        return WBMPL::getInstance('app.libraries.filesystem', 'WBMPL_folder');
    }
    
    final public function getPath()
    {
        return WBMPL::getInstance('app.libraries.filesystem', 'WBMPL_path');
    }
    
    final public function getMain()
    {
        return WBMPL::getInstance('app.libraries.main');
    }
    
    final public function getFactory()
    {
        return WBMPL::getInstance('app.libraries.factory');
    }
    
    final public function getPRO()
    {
        return WBMPL::getInstance('app.libraries.pro');
    }
}