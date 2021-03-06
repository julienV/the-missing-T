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
    JSubMenuHelper::addEntry( JText::_( 'COM_MISSINGT_ABOUT' ), 'index.php?option=com_missingt&view=about', ($view == 'about'));
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
}
?>