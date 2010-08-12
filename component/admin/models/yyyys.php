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
 * Missingt Component Yyyys Model
 *
 * @package Joomla
 * @subpackage Missingt
 * @since		0.1
 */
class MissingtModelYyyys extends JModel
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
		// Lets load the venues if they doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
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
	 * Method to build the query for the venues
	 *
	 * @access private
	 * @return string
	 * @since 0.9
	 */
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = 'SELECT o.*, u.email, u.name AS author'
				. ' FROM #__missingt_yyyy AS o'
				. ' LEFT JOIN #__users AS u ON u.id = o.checked_out'
				. $where
				. $orderby
				;

		return $query;
	}

	/**
	 * Method to build the orderby clause of the query for the venues
	 *
	 * @access private
	 * @return string
	 * @since 0.9
	 */
	function _buildContentOrderBy()
	{
		global $mainframe, $option;
		
		$view = JRequest::getCmd( 'view');
		
		$filter_order		= $mainframe->getUserStateFromRequest( $option.$view.'.filter_order', 'filter_order', 'o.ordering', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.$view.'.filter_order_Dir', 'filter_order_Dir', '', 'word' );

	//	$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', l.ordering';
		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to build the where clause of the query for the venues
	 *
	 * @access private
	 * @return string
	 * @since 0.9
	 */
	function _buildContentWhere()
	{
		global $mainframe, $option;
    $view = JRequest::getCmd( 'view');

		$filter_state 		= $mainframe->getUserStateFromRequest( $option.$view.'.filter_state', 'filter_state', '', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.$view.'.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.$view.'.search', 'search', '', 'string' );
		$search 			= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		/*
		* Filter state
		*/
		if ( $filter_state ) {
			if ( $filter_state == 'P' ) {
				$where[] = 'o.published = 1';
			} else if ($filter_state == 'U' ) {
				$where[] = 'o.published = 0';
			}
		}

		/*
		* Search yyyys
		*/
		if ($search && $filter == 1) {
			$where[] = ' LOWER(o.name) LIKE \'%'.$search.'%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	/**
	 * Method to (un)publish a yyyy
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function publish($cid = array(), $publish = 1)
	{
		$user 	=& JFactory::getUser();
		$userid = $user->get('id');

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__missingt_yyyy'
					. ' SET published = '. (int) $publish
					. ' WHERE id IN ('. $cids .')'
					. ' AND ( checked_out = 0 OR ( checked_out = ' .$userid. ' ) )'
					;

			$this->_db->setQuery( $query );

			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
	}

	/**
	 * Method to move a yyyy
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function move($direction)
	{
		$row =& JTable::getInstance('yyyy', 'MissingtTable');

		if (!$row->load( $this->_id ) ) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->move( $direction )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	
  /**
   * Method to save item order
   *
   * @access  public
   * @return  boolean True on success
   * @since 1.5
   */
  function saveorder($cid = array(), $order)
  {
    $row  =& $this->getTable('yyyy', 'MissingtTable');

    // update ordering values
    for( $i=0; $i < count($cid); $i++ )
    {
      $row->load( (int) $cid[$i] );

      if ($row->ordering != $order[$i])
      {
        $row->ordering = $order[$i];
        if (!$row->store()) {
          $this->setError($this->_db->getErrorMsg());
          return false;
        }
      }
    }
    
    return true;
  }

	/**
	 * Method to remove a yyyy
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
			$cids = implode( ',', $cid );

			$query = 'DELETE FROM #__missingt_yyyy'
					. ' WHERE id IN ('. $cids .')'
					;

			$this->_db->setQuery( $query );

			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
	}
}
?>