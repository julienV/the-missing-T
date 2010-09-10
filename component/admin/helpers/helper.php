<?php
/**
* @version    $Id$ 
* @package    Missingt
* @copyright  Copyright (C) 2008 Julien Vonthron. All rights reserved.
*/

class MissingtAdminHelper {

  function buildMenu()
  {
    $user = & JFactory::getUser();
    
    $view = JRequest::getVar('view');
    $controller = JRequest::getVar('controller');
    
    //Create Submenu
    JSubMenuHelper::addEntry( JText::_( 'COM_MISSINGT_HOME' ), 'index.php?option=com_missingt', ($view == ''));
    JSubMenuHelper::addEntry( JText::_( 'COM_MISSINGT_TRANSLATIONS' ), 'index.php?option=com_missingt&view=files', ($view == 'files'));
    JSubMenuHelper::addEntry( JText::_( 'COM_MISSINGT_COMPONENTS' ), 'index.php?option=com_missingt&view=components', ($view == 'components'));
  }
  
  function getRealPOST() 
  {
    $pairs = explode("&", file_get_contents("php://input"));
    $vars = array();
    foreach ($pairs as $pair) {
        $nv = explode("=", $pair);
        $name = urldecode($nv[0]);
        $value = urldecode($nv[1]);
        $vars[$name] = $value;
    }
    return $vars;
	}  
    
	function _convertToIni($array)
	{	
		$handlerIni = & JRegistryFormat::getInstance('INI');
		$object = new StdClass;
		
		foreach($array as $k=>$v) 
		{
			if (strpos($k, 'KEY_') === 0) {
				$key = substr($k, 4);
				$object->$key = $v;
			}
		}
		
		$string = $handlerIni->objectToString($object,null);	
		
		return $string;
	}
	
	
	function checkHistory($path)
	{
		$db = &JFactory::getDBO();
		
		if (strstr($path, JPATH_SITE)) {
			$file = substr($path, strlen(JPATH_SITE)+1); 
		}
		
		$fullpath = JPATH_SITE.DS.$file;
		if (!file_exists($fullpath)) {
			return true;
		}
		
		$query = ' SELECT sha ' 
		       . ' FROM #__missingt_history ' 
		       . ' WHERE file = ' . $db->Quote($file)
		       . ' ORDER BY id DESC'
		       ;
		$db->setQuery($query);
		$res = $db->loadResult();
		
		if (!$res || sha1_file($fullpath) != $res) {
			return self::updateHistory($file);
		}
		return true;
	}
	
	function updateHistory($file)
	{
		$fullpath = JPATH_SITE.DS.$file;
		
		// update history table
		$history = &JTable::getInstance('history', 'MissingtTable');
		$history->file = $file;
		$history->text = file_get_contents($fullpath);
		$history->note = JText::_('COM_MISSINGT_HISTORY_NOTE_EXTERNAL_CHANGES');
		if (!($history->check() && $history->store())) {
			$this->setError('COM_MISSINGT_ERROR_WRITING_HISTORY');
			return false;			
		}
		
		return true;
	}
	
	function parseIni($data)
	{
		$lines = explode("\n", $data);
			
		$obj = new stdClass();
		$obj->lines = array();

		$sec_name = '';
		$unparsed = 0;
		if (!$lines) {
			return $obj;
		}

		$i = 0;
		foreach ($lines as $line)
		{
			// ignore comments
			if ($line && $line{0} == ';') {
				$obj->lines['not_a_key_line'.$i++] = $line;
				continue;
			}

			$line = trim($line);			

			if ($line == '') {
				$obj->lines['not_a_key_line'.$i++] = $line;
				continue;
			}

			$lineLen = strlen($line);
			
			if ($pos = strpos($line, '='))
			{
				$property = trim(substr($line, 0, $pos));

				// property is assumed to be ascii
				if ($property && $property{0} == '"')
				{
					$propLen = strlen( $property );
					if ($property{$propLen-1} == '"') {
						$property = stripcslashes(substr($property, 1, $propLen - 2));
					}
				}
				// AJE: 2006-11-06 Fixes problem where you want leading spaces
				// for some parameters, eg, class suffix
				// $value = trim(substr($line, $pos +1));
				$value = substr($line, $pos +1);

				if (strpos($value, '|') !== false && preg_match('#(?<!\\\)\|#', $value))
				{
					$newlines = explode('\n', $value);
					$values = array();
					foreach($newlines as $newlinekey=>$newline) {

						// Explode the value if it is serialized as an arry of value1|value2|value3
						$parts	= preg_split('/(?<!\\\)\|/', $newline);
						$array	= (strcmp($parts[0], $newline) === 0) ? false : true;
						$parts	= str_replace('\|', '|', $parts);

						foreach ($parts as $key => $value)
						{
							if ($value == 'false') {
								$value = false;
							}
							else if ($value == 'true') {
								$value = true;
							}
							else if ($value && $value{0} == '"')
							{
								$valueLen = strlen( $value );
								if ($value{$valueLen-1} == '"') {
									$value = stripcslashes(substr($value, 1, $valueLen - 2));
								}
							}
							if(!isset($values[$newlinekey])) $values[$newlinekey] = array();
							$values[$newlinekey][] = str_replace('\n', "\n", $value);
						}

						if (!$array) {
							$values[$newlinekey] = $values[$newlinekey][0];
						}
					}

					$obj->lines[$property] = $values[$newlinekey];
				}
				else
				{
					//unescape the \|
					$value = str_replace('\|', '|', $value);

					if ($value == 'false') {
						$value = false;
					}
					else if ($value == 'true') {
						$value = true;
					}
					else if ($value && $value{0} == '"')
					{
						$valueLen = strlen( $value );
						if ($value{$valueLen-1} == '"') {
							$value = stripcslashes(substr($value, 1, $valueLen - 2));
						}
					}

					$obj->lines[$property] = str_replace('\n', "\n", $value);
				}
			}
			else
			{
				$obj->lines['not_a_key_line'.$i++] = $line;
				continue;
			}
		}

		return $obj;
	}
	
	function arrayToIni($data)
	{
		$res = '';
		foreach ($data as $key => $line)
		{
			if (strpos($key, 'not_a_key_lin') === 0) {
				// Escape any pipe characters before storing
				$line = trim($line);
				$line	= str_replace('|', '\|', $line);
				$line	= str_replace(array("\r\n", "\n"), '\\n', $line);
				if ($line && !($line{0} == ';')) { // ini comments should start with a semi-colon
					$line = ';'.$line;
				}
				$res .= $line."\n";
			}
			else {				
				$line	= str_replace('|', '\|', $line);
				$line	= str_replace(array("\r\n", "\n"), '\\n', $line);
				$res .= $key."=".$line."\n";
			}
		}
		return $res;
	}
}
?>