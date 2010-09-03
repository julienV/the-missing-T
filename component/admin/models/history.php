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
 * Joomla Missingt Component History Model
 *
 * @author Julien Vonthron <julien.vonthron@gmail.com>
 * @package   Missingt
 * @since 0.4
 */
class MissingtModelHistory extends JModel
{
 /**
   * history id
   *
   * @var int
   */
  var $_id = null;
 /**
   * file
   *
   * @var string
   */
  var $_file = null;

  /**
   * data array
   *
   * @var array
   */
  var $_data = null;
  
  var $_total = null;

  /**
   * Constructor
   *
   * @since 0.9
   */
  function __construct()
  {
    parent::__construct();

    $file = JRequest::getVar('file', '', '', 'string');
    $this->setFile($file);
    $cid = JRequest::getVar('cid', '', '', 'array');
    $this->setId($cid[0]);
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
   * Method to set the file name
   *
   * @access  public
   * @param int event identifier
   */
  function setFile($id)
  {
    // Set venue id and wipe data
    $this->_file  = $id;
    $this->_data  = null;
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
   * builds path to target file
   * 
   * @return string path
   */
  function getTarget()
  {
  	if ($this->_id) {
			$query = ' SELECT h.file ' 
				       . ' FROM #__missingt_history AS h '
				       . ' WHERE h.id = ' . $this->_db->Quote($this->_id)
				       ;
			$this->_db->setQuery($query);
			return $this->_db->loadResult();  		
  	}  	
  	else
  	{
  		return $this->_file;
  	} 
  }
  	
	function getHistory()
	{		
		if (empty($this->_data))
		{
			$path = $this->getTarget();
			
			$query = $this->_buildQuery()
			       . ' ORDER BY h.id DESC ';
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObjectList();
		}
		return $this->_data;
	}
	
	function _buildQuery()
	{
		$path = $this->getTarget();
		$query = ' SELECT h.id, h.last_modified, h.note, u.username ' 
			       . ' FROM #__missingt_history AS h '
			       . ' LEFT JOIN #__users AS u on u.id = h.modified_by ' 
			       . ' WHERE h.file = ' . $this->_db->Quote($path)
			       ;
		return $query;
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
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
	 * return changes between specified cids, or between
	 */
	function getChanges()
	{
    $cid = JRequest::getVar('cid', '', '', 'array');
    JArrayHelper::toInteger($cid);
    if (!count($cid)) {
    	return false;
    }

    $id = $cid[0];
    
    $query = ' SELECT h.* ' 
           . ' FROM #__missingt_history AS h ' 
           . ' INNER JOIN #__missingt_history AS orig ON orig.file = h.file ' 
           . ' WHERE h.id <= ' . $id
           . '   AND orig.id = ' . $id
           . ' ORDER BY h.id DESC '
           . ' LIMIT 0,2'
           ;
    $this->_db->setQuery($query);
    $res = $this->_db->loadObjectList();
    
    if (!$res) {
    	return false;
    }
    
    if (!isset($res[1])) {
    	$before = new stdclass();
    	$before->text = '';
    	$before->last_modified = null;
    }
    else {
    	$before = $res[1];
    }
    $after  = $res[0];
    
    $helper = & JRegistryFormat::getInstance('INI');
		$object = $helper->stringToObject($before->text);
		$before->strings = get_object_vars($object);
		$object = $helper->stringToObject($after->text);
		$after->strings = get_object_vars($object);
		
		$keys = array_merge(array_keys($before->strings), array_keys($after->strings));
		
		$changes = array();
		foreach ($keys as $key)
		{
			$change = new stdclass();
			if (isset($before->strings[$key])) 
			{
				if (!isset($after->strings[$key]) || $after->strings[$key] != $before->strings[$key]) 
				{
					$change->src  = $before->strings[$key];
					$change->dest = (isset($after->strings[$key]) ? $after->strings[$key] : '');
					$changes[$key] = $change;
				}
			}
			else if (isset($after->strings[$key])) 
			{
				$change->src  = '';
				$change->dest = $after->strings[$key];	
				$changes[$key] = $change;
			}
		}
		
		$data = new stdclass();
		$data->strings = $changes;
		$data->from = $before->last_modified;
		$data->to   = $after->last_modified;
		return $data;
	}
	
	/**
	 * returns stored file text for current version
	 * 
	 * @return string
	 */
	function getFileData()
	{
		$query = ' SELECT h.text ' 
		       . ' FROM #__missingt_history AS h' 
		       . ' WHERE id = ' . $this->_db->Quote($this->_id);
		$this->_db->setQuery($query);
		$res = $this->_db->loadResult();
		return $res;
	}
	
	/**
	 * replace current language file with stored data
	 * 
	 * @return boolean true on success
	 */
	function restore()
	{
  	jimport('joomla.filesystem.file');
    $user   = & JFactory::getUser();

  	$query = ' SELECT h.file, h.text ' 
		       . ' FROM #__missingt_history AS h' 
		       . ' WHERE id = ' . $this->_db->Quote($this->_id);
		$this->_db->setQuery($query);
		$res = $this->_db->loadObject();
		$path = JPATH_SITE.DS.$res->file;
		        
    if (file_exists($path) && !is_writable($path)) {
			$this->setError('COM_MISSINGT_ERROR_WRITING_FILE_NOT_WRITABLE');
			return false;    	
    }
		$ret = file_put_contents($path, $res->text);
		
		if (!$ret) {
			$this->setError('COM_MISSINGT_ERROR_WRITING_FILE');
			return false;
		}
		
		// update history table
		$history = $this->getTable('history', 'MissingtTable');
		$history->file = $res->file;
		$history->text = $res->text;
		$history->note = JText::_('COM_MISSINGT_HISTORY_NOTE_RESTORED_OLD_VERSION');
		if (!($history->check() && $history->store())) {
			$this->setError('COM_MISSINGT_ERROR_WRITING_HISTORY');
			return false;			
		}
		
		return true;
	}
	
	function remove($cid)
	{
		if (!is_array($cid) || empty($cid)) {
			$this->setError('COM_MISSINGT_HISTORY_NOTHING_TO_REMOVE');			
			return false;
		}
		$query = ' DELETE FROM #__missingt_history ' 
		       . ' WHERE id IN (' . implode(',',$cid) .') '
		       ;
		$this->_db->setQuery($query);
		$res = $this->_db->query();
		
		if (!$res) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		return true;
	}
}
?>
