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
 * Joomla Missingt history table
 *
 * @author Julien Vonthron <julien.vonthron@gmail.com>
 * @package   Missingt
 * @since 0.4
 */
class MissingtTableHistory extends JTable
{
	/**
	 * Primary Key
	 * @var int
	 */
	var $id 				= null;
	/** @var string fill path */
	var $file			= null;
	/** @var string file sha 40 characters*/
	var $sha 			= null;
	/**
	 * changes note
	 * @var string
	 */
	var $note     = null;
	/**
	 * full text
	 * @var string
	 */
	var $text     = null;
	/**
	 * last modified date
	 * @var string datetime
	 */
	var $last_modified = null;
	/**
	 * modified my
	 * @var int user id
	 */
	var $modified_by = 0;

	/**
	* @param database A database connector object
	*/
	function MissingtTableHistory(& $db) {
		parent::__construct('#__missingt_history', 'id', $db);
	}

	// overloaded check function
	function check()
	{
		if (empty($this->file))
		{
			$this->setError(JText::_('COM_MISSINGT_TABLE_HISTORY_FILE_REQUIRED'));
			return false;
		}
		if (empty($this->text))
		{
			$this->setError(JText::_('COM_MISSINGT_TABLE_HISTORY_TEXT_REQUIRED'));
			return false;
		}
		
		if (empty($this->sha))
		{
			$this->sha = sha1($this->text);
		}		
		if (empty($this->last_modified))
		{
			jimport('joomla.utilities.date');
			$date = new JDate();
			$this->last_modified = $date->toMysql();
		}
		if (!($this->modified_by))
		{
			$user = &JFactory::getUser();
			$this->modified_by = $user->get('id');
		}
		return true;
	}
	
	/**
	 * check if the file was changed compared to previous version
	 * 
	 * return boolean true if file was changed
	 */
	function hasChanges()
	{
		$query = ' SELECT h.sha ' 
		       . ' FROM #__missingt_history AS h ' 
		       . ' WHERE h.file = ' . $this->_db->Quote($this->file)
		       . ' ORDER BY h.id DESC'
		       ;
		$this->_db->setQuery($query);
		$res = $this->_db->loadResult();
		
		if ($this->sha == $res) {
			return false;
		}
		return true;
	}
	
	function store($updateNulls=false)
	{			
		// do not store if there are no changes
		if (!$this->hasChanges()) {			
			return true;
		}
		return parent::store($updateNulls);
	}
}
?>