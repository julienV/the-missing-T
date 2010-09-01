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
}
?>