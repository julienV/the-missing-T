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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Missingt Component Files Model
 *
 * @package Joomla
 * @subpackage Missingt
 * @since		0.1
 */
class MissingtModelFiles extends JModel
{
	/**
	 * data array
	 *
	 * @var array
	 */
	var $_data = null;
	
	/**
	 * files array
	 *
	 * @var array
	 */
	var $_files = null;

	/**
	 * total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Webcast id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();
		
		$app = &JFactory::getApplication();

		global $option;

    $limit      = $app->getUserStateFromRequest( $option.'.files.limit', 'limit', $app->getCfg('list_limit'), 'int');
    $limitstart = $app->getUserStateFromRequest( $option.'.files.limitstart', 'limitstart', 0, 'int' );
    $search     = $app->getUserStateFromRequest( $option.'.files.search', 'search', '', 'string');
    $from       = $app->getUserStateFromRequest( $option.'.files.from', 'from', 'en-GB', 'string' );
    $to         = $app->getUserStateFromRequest( $option.'.files.to', 'to', '', 'string');
    $type       = $app->getUserStateFromRequest( $option.'.files.location', 'location', 'frontend', 'string');
    
    $app->setUserState($option.'.files.search', $search);
    $app->setUserState($option.'.files.from', $from);
    $this->setTo($to);
    $app->setUserState($option.'.files.type', $type);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);

	}
	
	/**
	 * sets 'to', making sure it's not empty
	 * @param string to language code
	 */
	function setTo($to)
	{
		global $option;
		$app = &JFactory::getApplication();

		if (!$to) 
		{
			$lang =& JFactory::getLanguage();
			$languages = $lang->getKnownLanguages();
			$current   = $lang->getTag();
			
			if ($current != 'en-GB') {
				$to = $current;				
			}
			else 
			{
				foreach ($languages as $l) 
				{
					if ($l['tag'] != 'en-GB') 
					{
						$to = $l['tag'];	
						break;	
					}
				}				
			}
			
			if (!$to) 
			{
				JError::raiseWarning(0, JText::_('COM_MISSINGT_ERROR_ONLY_ENGLISH_INSTALLED'));
				$to = 'en-GB';
			}
		}
		$app->setUserState($option.'.files.to', $to);
	}

		/**
	 * Method to set the identifier
	 *
	 * @access	public
	 * @param	int Category identifier
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id	    = $id;
		$this->_data = null;
	}

	/**
	 * Method to get item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		if (empty($this->_data))
		{
			global $option;
			$app = &JFactory::getApplication();
		
			$files = $this->_getFiles();
			$pagination = $this->getPagination();
			
			$files = array_slice($this->_files, $pagination->limitstart, $pagination->limit);
						
			$data = array();
			foreach ($files as $k => $file)
			{
				$obj = new stdclass();
				$obj->file = $file;
				$this->_auditFile($obj, $app->getUserState($option.'.files.from'), $app->getUserState($option.'.files.to'));
				$data[] = $obj;
			}
			$this->_data = $data;
		}
		
		return $this->_data;
	}
	
	/**
	 * get translation stats for the file
	 * @param string full file path
	 * @param string from language code
	 * @param string to language code
	 */
	function _auditFile(&$file, $from, $to)
	{
		$helper  = & JRegistryFormat::getInstance('INI');
		$object  = $helper->stringToObject(file_get_contents($file->file));
		$strings = get_object_vars($object);
		$strings = array_filter($strings, array($this, '_filterempty')); // filter empty strings for comparison
		
		$file->total   = count($strings);
			
		$target_path = str_replace($from, $to, $file->file);
		
		if (!file_exists($target_path)) {
			$file->translated = 0;
		}
		else
		{
			$helper  = & JRegistryFormat::getInstance('INI');
			$object  = $helper->stringToObject(file_get_contents($target_path));
			$strings_target = get_object_vars($object);
			$strings_target = array_filter($strings_target, array($this, '_filterempty')); // filter empty strings for comparison
			$file->translated = count(array_intersect_key($strings, $strings_target));
		}
	}
	
	/**
	 * callback funtion to filter empty values of array
	 * @param $string
	 */
	function _filterempty($element)
	{
		return (!empty($element));
	}

	/**
	 * Total nr of items
	 *
	 * @access public
	 * @return integer
	 * @since 0.9
	 */
	function getTotal()
	{
		$files = $this->_getFiles();
		return count($files);
	}

	function _getFiles()
	{	
		global $option;
		$app = &JFactory::getApplication();
		if (empty($this->_files))
		{
			$search = $app->getUserState($option.'.files.search');
			$from   = $app->getUserState($option.'.files.from');
			$type   = $app->getUserState($option.'.files.location');
			if ($type == 'backend') {
				$files = JFolder::files(JPATH_SITE.DS.'administrator'.DS.'language'.DS.$from, $search, false, true);
			}
			else {
				$files = JFolder::files(JPATH_SITE.DS.'language'.DS.$from, $search, false, true);
			}
			sort($files);
			$this->_files = $files;
		}
		return $this->_files;
	}
	
	function _getTargetFiles()
	{
		global $option;
		$app = &JFactory::getApplication();
		
		$search = $app->getUserState($option.'.files.search');
		$to     = $app->getUserState($option.'.files.to');
		$from   = $app->getUserState($option.'.files.from');
		$type   = $app->getUserState($option.'.files.location');
		
		if ($to == $from)
		{
			return $this->getData();
		}
	
		if ($type == 'backend') {
			$files = JFolder::files(JPATH_SITE.DS.'administrator'.DS.'language'.DS.$to, $search, false, false);
		}
		else {
			$files = JFolder::files(JPATH_SITE.DS.'language'.DS.$to, $search, false, false);
		}
		return $files;
	}
	
	function getLanguages()
	{
		$folders = JFolder::folders(JPATH_SITE.DS.'language');
		sort($folders);
		return $folders;
	}
	
	/**
	 * Method to get a pagination object for the venues
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to remove a file
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function delete($cid)
	{
		$cids = implode( ',', $cid );

		if (count( $cid ))
		{
			//
		}
	}
}
?>