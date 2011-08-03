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
 * Joomla Missingt Component file Model
 *
 * @author Julien Vonthron <julien.vonthron@gmail.com>
 * @package   Missingt
 * @since 0.1
 */
class MissingtModelFile extends JModel
{
 /**
   * id
   *
   * @var string
   */
  var $_id = null;

  /**
   * data array
   *
   * @var array
   */
  var $_data = null;

  /**
   * Constructor
   *
   * @since 0.9
   */
  function __construct()
  {
    parent::__construct();

    $array = JRequest::getVar('cid', '', '', 'array');
    $this->setId($array[0]);
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
  function &getData()
  {
    if (empty($this->_data))
    {
    	$res = new stdclass();
    	// original file
    	$source = $this->getSource();
    	$helper = & JRegistryFormat::getInstance('INI');
			$object = $helper->stringToObject(file_get_contents($source));
			$res->from = get_object_vars($object);
			
			// target file
			$path = $this->getTarget();
  		MissingtAdminHelper::checkHistory($path);
			if (file_exists($path))
			{
				$object = $helper->stringToObject(file_get_contents($path));
				$strings = get_object_vars($object);
				
				$present = array();
				foreach ($res->from as $k => $v) 
				{
					if (isset($strings[$k]) && !empty($strings[$k])) {
						$present[$k] = $strings[$k];
					}
				}				
				$res->to = $present;
			}
			else {
				$res->to = array();
			}
			
			$this->_data = $res;
    }

    return $this->_data;
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
    
    $text = $this->_convertToIni($data);
    
    if (file_exists($target) && !is_writable($target)) {
			$this->setError('COM_MISSINGT_ERROR_WRITING_FILE_NOT_WRITABLE');
			return false;    	
    }
		$ret = file_put_contents($target, $text);
		
		if (!$ret) {
			$this->setError('COM_MISSINGT_ERROR_WRITING_FILE');
			return false;
		}
		
		// update history table
		$history = $this->getTable('history', 'MissingtTable');
		$history->file = substr($target, strlen(JPATH_SITE)+1);
		$history->text = $text;
		if (!($history->check() && $history->store())) {
			$this->setError('COM_MISSINGT_ERROR_WRITING_HISTORY');
			return false;			
		}
		
    return true;
  }
  
  function getResult()
  {
  	$post = MissingtAdminHelper::getRealPOST();	    
    $text = $this->_convertToIni($post);
    return $text;
  }
  
  function getWritable()
  {
  	$file = $this->getTarget();
  	
  	if (file_exists($file)) {
  		return is_writable($file);
  	}
  	else return is_writable(dirname($file));
  }
  
  /**
   * builds path to source file
   * 
   * @return string path
   */
  function getSource()
  {
  	$option = JRequest::getCmd('option');
  	
		$app = &JFactory::getApplication();
		$location   = $app->getUserState($option.'.files.location');
		$filename = basename($this->_id);
		$pospoint = strpos($filename, '.');
		$lang     = substr($filename, 0, $pospoint);
		
		if ($location == 'backend') {
			$path = JPATH_SITE.DS.'administrator'.DS.'language'.DS.$lang.DS.$filename;
		}
		else {
			$path = JPATH_SITE.DS.'language'.DS.$lang.DS.$filename;
		}
		
		return $path;  	
  }
  
  /**
   * builds path to target file
   * 
   * @return string path
   */
  function getTarget()
  {
  	$option = JRequest::getCmd('option');
		$app = &JFactory::getApplication();
		$location = $app->getUserState($option.'.files.location');
		$to       = $app->getUserState($option.'.files.to');
		$filename = basename($this->_id);
		$pospoint = strpos($filename, '.');
		$target = $to.substr($filename, $pospoint);
		
		if ($location == 'backend') {
			$path = JPATH_SITE.DS.'administrator'.DS.'language'.DS.$to.DS.$target;
		}
		else {
			$path = JPATH_SITE.DS.'language'.DS.$to.DS.$target;
		}
		
		return $path;  	
  }
  
	function _convertToIni($array)
	{	
		$object = new StdClass;
		
		foreach($array as $k=>$v) 
		{
			if (strpos($k, 'KEY_') === 0) {
				$key = substr($k, 4);
				$object->$key = $v;
			}
		}
		
		$string = $this->objectToString($object,null);	
		
		return $string;
	}
	
/**
	 * Converts an object into an INI formatted string
	 * 	-	Unfortunately, there is no way to have ini values nested further than two
	 * 		levels deep.  Therefore we will only go through the first two levels of
	 * 		the object.
	 *
	 * @access public
	 * @param object $object Data Source Object
	 * @param array  $param  Parameters used by the formatter
	 * @return string INI Formatted String
	 */
	function objectToString( &$object, $params )
	{
		// Initialize variables
		$retval = '';
		$prepend = '';

		// First handle groups (or first level key/value pairs)
		foreach (get_object_vars( $object ) as $key => $level1)
		{
			if (is_object($level1))
			{
				// This field is an object, so we treat it as a section
				$retval .= "[".$key."]\n";
				foreach (get_object_vars($level1) as $key => $level2)
				{
					if (!is_object($level2) && !is_array($level2))
					{
						// Join lines
						$level2		= str_replace('|', '\|', $level2);
						$level2		= str_replace(array("\r\n", "\n"), '\\n', $level2);
						$retval		.= $key."=\"".$level2."\"\n";
					}
				}
				$retval .= "\n";
			}
			elseif (is_array($level1))
			{
				foreach ($level1 as $k1 => $v1)
				{
					// Escape any pipe characters before storing
					$level1[$k1]	= str_replace('|', '\|', $v1);
					$level1[$k1]	= str_replace(array("\r\n", "\n"), '\\n', $v1);
				}

				// Implode the array to store
				$prepend	.= $key."=\"".implode('|', $level1)."\"\n";
			}
			else
			{
				// Join lines
				$level1		= str_replace('|', '\|', $level1);
				$level1		= str_replace(array("\r\n", "\n"), '\\n', $level1);
				$prepend	.= $key."=\"".$level1."\"\n";
			}
		}

		return $prepend."\n".$retval;
	}
}
?>
