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
	var $_siteFoundWords      = array();
	var $_sysFoundWords      = array();
	var $_currentLangSite     = array();
	var $_currentLangAdmin     = array();
	var $_currentLangSys     = array();
	
	protected $_exclude = array();
	
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

		$location = $app->getUserStateFromRequest( 'com_missingt.component.location', 'location', 'site', 'string');
		$this->setState('location', $location);
		
		$params = JComponentHelper::getParams('com_missingt');
		$excl = str_replace(' ', '', $params->get('exclude', ''));
		$excl = trim($excl);
		$this->_exclude = empty($excl) ? array() : explode(",", $excl);
	}

	function getTarget()
	{
		if ($this->getState('location') == 'admin') {
			$path = JPATH_SITE.DS.'administrator'.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.ini';
		}
		else if ($this->getState('location') == 'sys') {
			$path = JPATH_SITE.DS.'administrator'.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.sys.ini';
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
		
		$sys = array();
		foreach ($data->sys as $key => $value)
		{
			if (!isset($admin[$value->files[0]])) {
				$sys[$value->files[0]] = array($key => $value);
			}
			else {
				$sys[$value->files[0]][$key] = $value;
			}
		}
		
		$site = array();
		foreach ($data->site as $key => $value)
		{
			if (!isset($site[$value->files[0]])) {
				$site[$value->files[0]] = array($key => $value);
			}
			else {
				$site[$value->files[0]][$key] = $value;
			}
		}
		$data->admin = $admin;
		$data->site = $site;
		$data->sys   = $sys;
		$this->_data = $data;

		return $data;
	}

	function _getAllKeys()
	{
		if (empty($this->_data))
		{
			$this->_adminLangFile = JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.ini';
			$this->_sysLangFile = JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.sys.ini';
			$this->_siteLangFile = JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->_id.'.ini';
			$this->_loadLangFile($this->_adminLangFile, 'admin');
			$this->_loadLangFile($this->_sysLangFile, 'sys');
			$this->_loadLangFile($this->_siteLangFile, 'site');

			$this->_parsePhpFiles();
			$this->_parseXmlFiles('admin');
			$this->_parseXmlFiles('sys');
			$this->_parseXmlFiles('site');

			$this->_addNotUsed('admin');
			$this->_addNotUsed('sys');
			$this->_addNotUsed('site');


			$data = new stdclass();
			$data->admin = $this->_adminFoundWords;
			$data->sys   = $this->_sysFoundWords;
			$data->site = $this->_siteFoundWords;
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
			MissingtAdminHelper::checkHistory($file);
			$object = $helper->stringToObject(file_get_contents($file));
			if ($location == 'site') {
				$this->_currentLangSite = get_object_vars($object);
			}
			else if ($location == 'sys') {
				$this->_currentLangSys = get_object_vars($object);
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

		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->_id)) {
			$adminFiles = JFolder::files(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->_id, '.php', true, true);
		}
		else {
			$adminFiles = array();
		}
		if (file_exists(JPATH_SITE.DS.'components'.DS.$this->_id)) {
			$siteFiles = JFolder::files(JPATH_SITE.DS.'components'.DS.$this->_id, '.php', true, true);
		}
		else {
			$siteFiles = array();
		}

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
		if (count($siteFiles))
		{
			foreach ($siteFiles as $item)
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
						$this->_addFoundWord($m,'site', $shortname);
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
	function _parseXmlFiles($type)
	{
		switch ($type)
		{
			case 'admin':
				if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->_id))
				{
					$adminFiles =  JFolder::files(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->_id, '.xml', true, true, array($this->_id.'.xml'));
					foreach ($adminFiles as $file)
					{
						$this->_parseXmlFile($file, $type);	
					}
				}
				break;
				
			case 'sys':
				if (file_exists(JPATH_SITE.DS.'components'.DS.$this->_id.DS.'views')) 
				{
					$files = JFolder::files(JPATH_SITE.DS.'components'.DS.$this->_id.DS.'views', '.xml$', true, true, array($this->_id.'.xml'));
					foreach ((array) $files as $file)
					{
						$this->_parseXmlFile($file, 'sys');	
					}
				}
				break;
				
			case 'site':
				if (file_exists(JPATH_SITE.DS.'components'.DS.$this->_id))
				{
					$files =  JFolder::files(JPATH_SITE.DS.'components'.DS.$this->_id, '.xml', true, true, array($this->_id.'.xml', 'views')); // do not parse the views folder
					foreach ($files as $item)
					{
						$this->_parseXmlFile($item, 'site');
					}
				}
				break;				
		}
	}

	/**
	 * look for title, label, description in xml file
	 * 
	 * @param string $file path to file
	 * @param string $type admin | sys | site
	 */
	function _parseXmlFile($file, $type)
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
					$this->_addFoundWord($match[2], $type, $shortname);
				}
				else {
					$this->_addFoundWord($match[1], $type, $shortname);
				}
			}
		}
		return true;
	}


	function _addNotUsed($type)
	{
		switch ($type)
		{
			case 'admin':
				$from = $this->_currentLangAdmin;
				$used = $this->_adminFoundWords;
				break;
			case 'sys':
				$from = $this->_currentLangSys;
				$used = $this->_sysFoundWords;
				break;
			case 'site':
				$from = $this->_currentLangSite;
				$used = $this->_siteFoundWords;
				break;
		}
		$notused = array_diff_key($from, $used);
		 
		foreach ($notused as $key => $value) {
			$this->_addFoundWord($key, $type, JText::_('COM_MISSINGT_STRING_NOT_USED'));
		}
	}

	function _addFoundWord($string, $type, $section)
	{
		if (trim($string)=='' || in_array($string, $this->_exclude)) {
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
					if (in_array($key, array_keys($this->_currentLangAdmin)) && !empty($this->_currentLangAdmin[$key])) {
						$found->defined = 1;
						$found->value = $this->_currentLangAdmin[$key];
					}
					else {
						$found->defined = 0;
						$found->value ='';
					}
					$this->_adminFoundWords[$key] = $found;
				}
				else {
					$this->_adminFoundWords[$key]->files[] = array($section);
				}
				break;
				
			case 'sys':
				if (!isset($this->_sysFoundWords[$key]))
				{
					$found = new stdclass();
					$found->files   = array($section);
					if (in_array($key, array_keys($this->_currentLangSys)) && !empty($this->_currentLangSys[$key])) {
						$found->defined = 1;
						$found->value = $this->_currentLangSys[$key];
					}
					else {
						$found->defined = 0;
						$found->value ='';
					}
					$this->_sysFoundWords[$key] = $found;
				}
				else {
					$this->_sysFoundWords[$key]->files[] = array($section);
				}
				break;

			case 'site':
				if (!isset($this->_siteFoundWords[$key]))
				{
					$found = new stdclass();
					$found->files   = array($section);
					if (in_array($key, array_keys($this->_currentLangSite))) {
						$found->defined = true;
						$found->value = $this->_currentLangSite[$key];
					}
					else {
						$found->defined = false;
						$found->value ='';
					}
					$this->_siteFoundWords[$key] = $found;
				}
				else {
					$this->_siteFoundWords[$key]->files[] = array($section);
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
		$post = MissingtAdminHelper::getRealPOST();
		
		$data = $this->_getAllKeys();
		
		switch ($this->getState('location'))
		{
			case 'admin':
				$src = $data->admin;
				break;
			case 'sys':
				$src = $data->sys;
				break;
			case 'site':
				$src = $data->site;
				break;
		}
			
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


	/**
	 * export only previously missing data to ini file
	 * 
	 * @return string
	 */
	function getResultMissing()
	{
		$post = MissingtAdminHelper::getRealPOST();
		$data = $this->_getAllKeys();
		switch ($this->getState('location'))
		{
			case 'admin':
				$src = $data->admin;
				break;
			case 'sys':
				$src = $data->sys;
				break;
			case 'site':
				$src = $data->site;
				break;
		}
		
		$res = array();
		foreach ($post as $k => $v)
		{
			if (strpos($k, 'KEY_') === 0)
			{
				$key = substr($k, 4);
				if (isset($src[$key]) && $src[$key]->defined == 0) 
				{
					$val = $src[$key];
					$val->value = $v;
					$res[$key] = $val;
				}
			}
		}
		$text = $this->_convertToIni($res);
		return $text;
	}

	function _convertToIni($src)
	{
		$text = "; en-GB.".$this->_id.".ini\n";
		$text .="; generated: " . gmdate('D, d M Y H:i:s') . " GMT\n\n";

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
			$text .= "; $file\n";
			foreach ($keys as $key => $value)
			{
				$val = str_replace('|', '\|', $value->value);
				$val = str_replace(array("\r\n", "\n"), '\\n', $val);
				$text .= $key."=\"".$val."\"\n";
			}
		}
		return $text;
	}
}
?>
