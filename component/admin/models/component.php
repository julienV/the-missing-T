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

require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'missingtline.class.php');

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
	protected $_id = null;

	/**
	 * cache data array
	 *
	 * @var array
	 */
	protected $_data = null;

	/**
	 * found words
	 * @var array
	 */
	protected $_foundWords      = array();
	/**
	 * current data from language file
	 * @var array
	 */
	protected $_currentLang     = array();
	/**
	 * excluded keys
	 * @var array
	 */
	protected $_exclude = array();
	
	/**
	 * current location: site | admin
	 * @var string
	 */
	protected $_location = 'admin';
	
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	public function __construct()
	{
		$app = &JFactory::getApplication();
		parent::__construct();

		$array = JRequest::getVar('cid', '', '', 'array');
		$this->setId($array[0]);

		$location = $app->getUserStateFromRequest( 'com_missingt.component.location', 'location', 'admin', 'string');
		$this->setLocation($location);
		
		$params = JComponentHelper::getParams('com_missingt');
		$excl = str_replace(' ', '', $params->get('exclude', ''));
		$excl = trim($excl);
		$this->_exclude = empty($excl) ? array() : explode(",", $excl);
	}
	
	/**
	 * set location: site | admin | sys
	 * @param string $loc
	 */
	public function setLocation($loc)
	{
		if (!in_array($loc, array('site', 'admin'))) {
			$loc = 'admin';
		}
		$this->setState('location', $loc);
		$this->_location = $loc;
	}
	
	/**
	 * get location: site | admin | sys
	 * @return string $loc
	 */
	public function getLocation()
	{
		return $this->_location;
	}

	/**
	 * return file location
	 * @return string file path
	 */
	public function getTarget()
	{
		if ($this->getLocation() == 'admin') {
			$path = JPATH_SITE.DS.'administrator'.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.ini';
		}
		else {
			$path = JPATH_SITE.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.ini';
		}
		return $path;
	}

	/**
	 * check if the target is writable
	 * @return boolean
	 */
	public function getIsWritable()
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
	 * Method to set the component name
	 *
	 * @access  public
	 * @param string component name
	 */
	public function setId($id)
	{
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
		if (empty($this->_data)) {
			$this->_data = $this->_getAllKeys();
		}
		
		return $this->_data;
	}

	function _getAllKeys()
	{
		$this->_loadLangFile();
		$this->_parsePhpFiles();
		$this->_parseXmlFiles();
		
		return $this->_data;
		 
	}

	/**
	 * loads the language file into _currentLang
	 */
	protected function _loadLangFile()
	{
		jimport('joomla.filesystem.file');
		
		switch ($this->getLocation())
		{
			case 'admin':
				$file = JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.ini';
				break;
			case 'site':
				$file = JPATH_SITE.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.ini';
				break;
		}
			
		if (JFile::exists($file))
		{
			MissingtAdminHelper::checkHistory($file);
			$this->_data = MissingTLine::iniStringToArray(file_get_contents($file));
		}
	}


	protected function _parsePhpFiles()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		switch ($this->getLocation())
		{
			case 'admin':
				if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->_id)) {
					$files = JFolder::files(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->_id, '.php', true, true);
				}
				else {
					return false;
				}
				break;
				
			case 'site':
				if (file_exists(JPATH_SITE.DS.'components'.DS.$this->_id)) {
					$files = JFolder::files(JPATH_SITE.DS.'components'.DS.$this->_id, '.php', true, true);
				}
				else {
					return false;
				}
				break;
				
			default:
		}

		$pattern = "/JText::_\(\s*\'([^']*)\'"
		. "|JText::_\(\s*\"([^\"]*)\""
		. "|JText::sprintf\(\s*\"([^\"]*)\""
		. "|JText::sprintf\(\s*\'([^']*)\'"
		. "|JText::script\(\s*\"([^\"]*)\""
		. "|JText::script\(\s*\'([^']*)\'"
		. "|JText::printf\(\s*\'([^']*)\'"
		. "|JHTML::_\(\s*[\'\"]grid.sort[\'\"]\s*,\s*\'([^']*)\'"
		. "|JHTML::_\(\s*[\'\"]grid.sort[\'\"]\s*,\s*\'([^']*)\'"
		. "|JText::printf\(\s*\"([^\"]*)\"/iU";

		/** ADMIN files **/
		$matches = array();
		if (count($files))
		{
			foreach ($files as $item)
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
						$this->_addFoundKey($m, $shortname);
					}
				}
			}
		}
	}

	/**
	 * look for strings in xml files
	 * 
	 * @param string $type admin | sys | site
	 */
	protected function _parseXmlFiles()
	{
		switch ($this->getLocation())
		{
			case 'admin':
				if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->_id))
				{
					$adminFiles =  JFolder::files(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->_id, '.xml', true, true, array($this->_id.'.xml'));
					foreach ($adminFiles as $file)
					{
						$this->_parseXmlFile($file);	
					}
				}
				
				if (file_exists(JPATH_SITE.DS.'components'.DS.$this->_id.DS.'views')) 
				{
					$files = JFolder::files(JPATH_SITE.DS.'components'.DS.$this->_id.DS.'views', '.xml$', true, true, array($this->_id.'.xml'));
					foreach ((array) $files as $file)
					{
						$this->_parseXmlFile($file);	
					}
				}
				break;
				
			case 'site':
				if (file_exists(JPATH_SITE.DS.'components'.DS.$this->_id))
				{
					$files =  JFolder::files(JPATH_SITE.DS.'components'.DS.$this->_id, '.xml', true, true, array($this->_id.'.xml', 'views')); // do not parse the views folder
					foreach ($files as $item)
					{
						$this->_parseXmlFile($item);
					}
				}
				break;				
		}
	}

	/**
	 * look for title, label, description in xml file
	 * 
	 * @param string $file path to file
	 */
	protected function _parseXmlFile($file)
	{
		$pattern = '/(?:title|label|description)="([^"]+)"|<option[^>]*>([^<]*)<\/option>/iU';	
		$contents = JFile::read($file);
		$shortname = strstr($file, $this->_id);
		$shortname = substr(strstr($shortname,'/'), 1);
		if (preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER ))
		{
			foreach ($matches as $match)
			{
				if (count($match) == 3) {
					$this->_addFoundKey($match[2], $shortname);
				}
				else {
					$this->_addFoundKey($match[1], $shortname);
				}
			}
		}
		return true;
	}

	/**
	 * check if the key is already present, otherwise adds it
	 * 
	 * @param string $key
	 * @param string $section file where it is being used
	 */
	protected function _addFoundKey($key, $section)
	{
		$key = strtoupper(trim($key));
		if (empty($key) || in_array($key, $this->_exclude)) {
			return;
		}
		
		// is already in language file ?
		foreach ($this->_data as $k => $line)
		{
			if ($line->key == $key)
			{
				$this->_data[$k]->foundin[] = $section;
				return true;
			}
		}
		// not found, let's add it !
		$obj = new MissingTLine();
		$obj->key = $key;
		$obj->foundin[] = $section;
		$this->_data[] = $obj;
		return true;
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

	/**
	 * export posted data to ini file
	 * 
	 * @return string
	 */
	function getResult()
	{
		$keys = JRequest::getVar('line_key', array(), 'post', 'array');
		$vals = JRequest::getVar('line_val', array(), 'post', 'array', JREQUEST_ALLOWHTML);
		
		$text = '';
		foreach($keys as $k => $key)
		{
			if (!empty($key)) {
				$text .= $keys[$k].'="'.$vals[$k]."\"\n";
			}
			else {
				$text .= $vals[$k]."\n";
			}
		}
		return $text;
	}


	/**
	 * export only missing data to ini file
	 * 
	 * @return string
	 */
	public function getResultMissing()
	{
		$keys = JRequest::getVar('line_key', array(), 'post', 'array');
		$vals = JRequest::getVar('line_val', array(), 'post', 'array', JREQUEST_ALLOWHTML);
		
		$text = '';
		foreach($keys as $k => $key)
		{
			if (!empty($key) && empty($vals[$k])) {
				$text .= $keys[$k].'="'.$vals[$k]."\"\n";
			}
		}
		return $text;
	}	
}
