<?php
/**
* @version    $Id$ 
* @package    Missingt
* @copyright  Copyright (C) 2008 Julien Vonthron. All rights reserved.
* @license    GNU/GPL, see LICENSE.php
* Missingt is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * Joomla Missingt Component component Model
 *
 * @author Julien Vonthron <julien.vonthron@gmail.com>
 * @package   Missingt
 * @since 0.3
 */
class MissingtModelComponent extends JModel
{
 /**
   * id
   *
   * @var string
   */
  var $_id = null;

  /**
   * cache data array
   *
   * @var array
   */
  var $_data = null;
  
  var $_adminFoundWords      = array();
  var $_frontFoundWords      = array();
  var $_currentLangFront     = array();
  var $_currentLangAdmin     = array();
  
  /**
   * Constructor
   *
   * @since 0.9
   */
  function __construct()
  {
  	$app = &JFactory::getApplication();
    parent::__construct();

    $array = JRequest::getVar('cid', '', '', 'array');
    $this->setId($array[0]);
    
    $location = $app->getUserStateFromRequest( 'com_missingt.component.location', 'location', 'frontend', 'string');
    $this->setState('location', $location);
  }
  
  function getTarget()
  {  	
  	if ($this->getState('location') == 'backend') {
  		$path = JPATH_SITE.DS.'administrator'.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.ini';
  	}
  	else {
  		$path = JPATH_SITE.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.ini';  		
  	}
  	return $path; 
  }
  
  function getIsWritable()
  {
  	$file = $this->getTarget();
  	if (file_exists($file)) {
  		return is_writable($file);
  	}
  	else {
  		return is_writable(dirname($file));
  	}
  }

  /**
   * Method to set the identifier
   *
   * @access  public
   * @param int event identifier
   */
  function setId($id)
  {
    // Set venue id and wipe data
    $this->_id      = $id;
    $this->_data  = null;
  }

  /**
   * Logic for the event edit screen
   *
   * @access public
   * @return array
   * @since 0.9
   */
  function getData()
  {
  	$data = $this->_getAllKeys();
      
  	$admin = array();
  	foreach ($data->admin as $key => $value)
  	{
  		if (!isset($admin[$value->files[0]])) {
  			$admin[$value->files[0]] = array($key => $value);
  		}
  		else {
  			$admin[$value->files[0]][$key] = $value;
  		}
  	}
  	$front = array();
  	foreach ($data->front as $key => $value)
  	{
  		if (!isset($front[$value->files[0]])) {
  			$front[$value->files[0]] = array($key => $value);
  		}
  		else {
  			$front[$value->files[0]][$key] = $value;
  		}
  	}
  	$data->admin = $admin;
  	$data->front = $front;
  	$this->_data = $data;

    return $data;
  }
  
  function _getAllKeys()
  {
    if (empty($this->_data))
    {
      $this->_adminLangFile = JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.ini';
      $this->_frontLangFile = JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.ini';
      $this->_loadLangFile($this->_adminLangFile, 'admin');
      $this->_loadLangFile($this->_frontLangFile, 'front');
      
      $this->_parsePhpFiles($this->_id);
      $this->_parseXmlFiles($this->_id);
      
      $this->_addNotUsed('admin');
      $this->_addNotUsed('front');
      
      
      $data = new stdclass();
      $data->admin = $this->_adminFoundWords;
      $data->front = $this->_frontFoundWords;
      $this->_data = $data;
    }

    return $this->_data;
  	
  }

  function _loadLangFile($file, $location)
  {
    jimport('joomla.filesystem.file');
  	$helper = & JRegistryFormat::getInstance('INI');
			
  	if (JFile::exists($file))
  	{
  		$object = $helper->stringToObject(file_get_contents($file));
	  	if ($location == 'front') {
				$this->_currentLangFront = get_object_vars($object);  		
	  	}
	  	else {
	  		$this->_currentLangAdmin = get_object_vars($object);
	  	}
  	}
  }
  

  function _parsePhpFiles()
  {
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$adminFiles =  JFolder::files(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->_id, '.php', true, true);
		$frontFiles = JFolder::files(JPATH_ROOT.DS.'components'.DS.$this->_id, '.php', true, true);

		$pattern = "/JText::_\(\s*\'(.*)\'\s*\)"
             . "|JText::_\(\s*\"(.*)\"\s*\)"
             . "|JText::sprintf\(\s*\"(.*)\""
             . "|JText::sprintf\(\s*\'(.*)\'"
             . "|JText::printf\(\s*\'(.*)\'"
             . "|JText::printf\(\s*\"(.*)\"/iU";

		/** ADMIN files **/
		$matches = array();
		if (count($adminFiles))
    {
      foreach ($adminFiles as $item)
      {
        $contents = JFile::read($item);        
        $shortname = strstr($item, $this->_id);
        $shortname = substr(strstr($shortname,'/'), 1);
        preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER );
        
        foreach ($matches as $match)
        {
          foreach ($match as $key=> $m)
          {
            $m = ltrim($m);
            $m = rtrim($m);
            if ($m==''|| $key==0) {
            	continue;
            }
            $this->_addFoundWord($m,'admin', $shortname);
          }
        }
      }
    }

    /** FRONT files **/
    $matches = array();
    if (count($frontFiles))
    {
    	foreach ($frontFiles as $item)
    	{
    		$contents = JFile::read($item);
        $shortname = strstr($item, $this->_id);
        $shortname = substr(strstr($shortname,'/'), 1);
    		preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER );

    		foreach ($matches as $match)
    		{
    			foreach ($match as $key=> $m)
    			{
    				$m = ltrim($m);
    				$m = rtrim($m);
    				if ($m=='' || $key==0) {
    					continue;
    				}
    				$this->_addFoundWord($m,'front', $shortname);
    			}
    		}
    	}
    }
  }
  

  function _parseXmlFiles()
  {
    $adminFiles =  JFolder::files(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->_id, '.xml', true, true, array($this->_id.'.xml'));
    $frontFiles = JFolder::files(JPATH_ROOT.DS.'components'.DS.$this->_id, '.xml$', true, true, array($this->_id.'.xml'));
    #*******************ADMIN XML PARSING***************************************
    foreach ($adminFiles as $item)
    {
    	$shortname = strstr($item, $this->_id);
    	$shortname = substr(strstr($shortname,'/'), 1);
      $xml = new JParameter('', $item);
      if (!$xml->getGroups()) {
      	continue;
      }
      $allProperties = $xml->getProperties(false);
      
      foreach ($xml->getGroups() as $key => $group)
      {
//        if ($key!='_default')
//        {
          $this->_xmlParsingAdmin[] = $key;
          foreach ($allProperties['_xml'][$key]->_children as $param)
          {
          	$this->_addFoundWord($param->attributes('label'), 'admin', $shortname);
            $this->_addFoundWord($param->attributes('description'), 'admin', $shortname);
            if (count($param->_children)>0)
            {
              foreach ($param->_children as $options)
              {
                if ($options->name()=='option')
                {
                  $this->_addFoundWord($options->data(), 'admin', $shortname);
                }
              }
            }
          }
//        }
      }
    }
  }
  
  function _addNotUsed($type)
  {
  	$from = ($type == 'admin' ? $this->_currentLangAdmin : $this->_currentLangFront);
  	$used = ($type == 'admin' ? $this->_adminFoundWords : $this->_frontFoundWords);
  	$notused = array_diff_key($from, $used);
  	
  	foreach ($notused as $key => $value) {
  		$this->_addFoundWord($key, $type, JText::_('COM_MISSINGT_STRING_NOT_USED'));
  	}
  }  

  function _addFoundWord($string, $type, $section)
  {
    if (trim($string)=='') {
    	return;
    }
    $key = strtoupper(trim($string));
    switch($type)
    {
      case 'admin':
      	if (!isset($this->_adminFoundWords[$key])) 
      	{
      		$found = new stdclass();
      		$found->files   = array($section);
      		if (in_array($key, array_keys($this->_currentLangAdmin))) {
      			$found->defined = true;
      			$found->value = $this->_currentLangAdmin[$key];
      		}
      		else {
      			$found->defined = false;
      			$found->value ='';      			
      		}
      		$this->_adminFoundWords[$key] = $found;
      	}
      	else {
      		$this->_adminFoundWords[$key]->files[] = array($section);
      	}
        break;

      case 'front':
      	if (!isset($this->_frontFoundWords[$key])) 
      	{
      		$found = new stdclass();
      		$found->files   = array($section);
      		if (in_array($key, array_keys($this->_currentLangFront))) {
      			$found->defined = true;
      			$found->value = $this->_currentLangFront[$key];
      		}
      		else {
      			$found->defined = false;
      			$found->value ='';      			
      		}
      		$this->_frontFoundWords[$key] = $found;
      	}
      	else {
      		$this->_frontFoundWords[$key]->files[] = array($section);
      	}
        break;
    }
  }

  /**
   * Method to store the venue
   *
   * @access  public
   * @return  boolean True on success
   * @since 1.5
   */
  function store($data)
  {
  	jimport('joomla.filesystem.file');
  	
    $user   = & JFactory::getUser();
    $config   = & JFactory::getConfig();

    $target = $this->getTarget();
    
    $text = $this->getResult();
    
    if (file_exists($target) && !is_writable($target)) {
			$this->setError('COM_MISSINGT_ERROR_WRITING_FILE_NOT_WRITABLE');
			return false;    	
    }
		$ret = file_put_contents($target, $text);
		
		if (!$ret) {
			$this->setError('COM_MISSINGT_ERROR_WRITING_FILE');
			return false;
		}
    return true;
  }
  
  function getResult()
  {
  	$post = MissingtAdminHelper::getRealPOST();
  	$data = $this->_getAllKeys();
  	$src = ($this->getState('location') == 'frontend' ? $data->front : $data->admin );
  	  
		foreach($post as $k=>$v) 
		{
			if (strpos($k, 'KEY_') === 0) 
			{
				if (isset($src[substr($k, 4)])) {
					$src[substr($k, 4)]->value = $v;
				}
			}
		}
		
		$text = $this->_convertToIni($src);
    return $text;  	
  }
  
  function _convertToIni($src)
  {
  	$text = "# en-GB.".$this->_id.".ini\n";
		$text .="# generated: " . gmdate('D, d M Y H:i:s') . " GMT\n\n";
  
		$key_files = array();
  	foreach ($src as $key => $value)
  	{
  		if (!isset($key_files[$value->files[0]])) {
  			$key_files[$value->files[0]] = array($key => $value);
  		}
  		else {
  			$key_files[$value->files[0]][$key] = $value;
  		}
  	}
  	
  	foreach ($key_files as $file => $keys)
  	{
  		$text .= "\n";
  		$text .= "# $file\n";
			foreach ($keys as $key => $value)
			{
				$val = str_replace('|', '\|', $value->value);
				$val = str_replace(array("\r\n", "\n"), '\\n', $val);
				$text .= $key."=".$val."\n";
			}
  	}
		return $text;
  }
}
?>
