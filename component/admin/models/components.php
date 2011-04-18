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
class MissingtModelComponents extends JModel
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
  
  function __construct()
  {
    parent::__construct();

		global $mainframe, $option;

    $limit      = $mainframe->getUserStateFromRequest( $option.'.components.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
    $limitstart = $mainframe->getUserStateFromRequest( $option.'.components.limitstart', 'limitstart', 0, 'int' );
    $search     = $mainframe->getUserStateFromRequest( $option.'.components.search', 'search', '', 'string' );
		$search     = strtolower($search);
    
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('search', $search);
  }

  function getData()
  {
    if (empty($this->_data))
    {
      $this->_getComponents();
    }
		$pagination = $this->getPagination();
		
		return array_slice($this->_data, $pagination->limitstart, $pagination->limit);
  }
  
  function _getComponents()
  {
    if (empty($this->_data))
    {
      jimport('joomla.filesysem.folder');
      $components = JFolder::folders(JPATH_ADMINISTRATOR.DS.'components', 'com_'.$this->getState('search') );
      $this->_data = $components;
    }
  	return $this->_data;
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
		$all = $this->_getComponents();
		return count($all);
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

}
?>