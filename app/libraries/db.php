<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();

class WBMPL_db extends WBMPL_base
{
    public function __construct()
    {
    }
    
	public function q($query, $type = '')
	{
		/** db prefix **/
		$query = self::_prefix($query);
		
		/** convert type to lowercase **/
		$type = strtolower($type);
		
		/** call select function if query type if select **/
		if($type == 'select') return self::select($query);
        
		/** db object **/
		$database = self::get_DBO();
		
		if($type == 'insert')
		{
			$database->query($query);
			return $database->insert_id;
		}
		
		return $database->query($query);
		
	}
    
	public function num($query, $table = '')
	{
		if(trim($table) != '')
		{
			$query = "SELECT COUNT(*) FROM `#__$table`";
		}
		
		/** db prefix **/
		$query = self::_prefix($query);
		
		/** db object **/
		$database = self::get_DBO();
		return $database->get_var($query);
	}
    
	public function select($query, $result = 'loadObjectList')
	{
		/** db prefix **/
		$query = self::_prefix($query);
		
		/** db object **/
		$database = self::get_DBO();
		
		if($result == 'loadObjectList') return $database->get_results($query, OBJECT_K);
		elseif($result == 'loadObject') return $database->get_row($query, OBJECT);
		elseif($result == 'loadAssocList') return $database->get_results($query, ARRAY_A);
		elseif($result == 'loadAssoc') return $database->get_row($query, ARRAY_A);
		elseif($result == 'loadResult') return $database->get_var($query);
		else return $database->get_results($query, OBJECT_K);
	}
    
	public function get($selects, $table, $field, $value, $return_object = true, $condition = '')
	{
		$fields = '';
		
		if(is_array($selects))
		{
			foreach($selects as $select) $fields .= '`'.$select.'`,';
			$fields = trim($fields, ' ,');
		}
		else
		{
			$fields = $selects;
		}
		
		if(trim($condition) == '') $condition = "`$field`='$value'";
		$query = "SELECT $fields FROM `#__$table` WHERE $condition";
		
		/** db prefix **/
		$query = self::_prefix($query);
		
		/** db object **/
		$database = self::get_DBO();
		
		if($selects != '*' and !is_array($selects)) return $database->get_var($query);
		elseif($return_object)
		{
			return $database->get_row($query);
		}
		elseif(!$return_object)
		{
			return $database->get_row($query, ARRAY_A);
		}
		else
		{
			return $database->get_row($query);
		}
	}
	
	public function _prefix($query)
	{
		$wpdb = self::get_DBO();
		return str_replace('#__', $wpdb->prefix, $query);
	}
    
	public function get_DBO()
	{
		global $wpdb;
		return $wpdb;
	}
}