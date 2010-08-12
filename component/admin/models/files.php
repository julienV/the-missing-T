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

		global $mainframe, $option;

    $limit      = $mainframe->getUserStateFromRequest( $option.'.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
    $limitstart = $mainframe->getUserStateFromRequest( $option.JRequest::getCmd( 'view').'.limitstart', 'limitstart', 0, 'int' );
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);

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
		$files = $this->_getFiles();
		$pagination = $this->getPagination();

		return array_slice($this->_data, $pagination->limitstart, $pagination->limit);
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
		if (empty($this->_data))
		{
			$search = JRequest::getVar('search', '', 'request', 'string');
			$from   = JRequest::getVar('from', 'en-GB', 'request', 'string');
			$files = JFolder::files(JPATH_SITE.DS.'language'.DS.$from, $search, false, true);
			sort($files);
			$this->_data = $files;
		}
		return $this->_data;
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