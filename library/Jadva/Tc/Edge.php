<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * JAdVA library
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.JaAchan.com/software/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@jaachan.com so we can send you a copy immediately.
 *
 * If you have a modification you would like to see included, (in
 * order to distribute it), email the changes to
 * softwaremodifications@jaachan.com. Should the modifications be
 * accepted, they will be released as soon as possible, and,
 * should you want to, you will be noted as contributor.
 *
 * @category   JAdVA
 * @package    Jadva_Tc
 * @subpackage Jadva_Tc_Graph
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Mysqli.php 39 2008-09-25 13:59:21Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class represenst an edge object.
 *
 * At the moment, you can pass any object as parent object. This may change in the future, take caution.
 *
 * @todo       Determine suitable parent object interface
 *
 * @category   JAdVA
 * @package    Jadva_Tc
 * @subpackage Jadva_Tc_Graph
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Tc_Edge extends Jadva_Tc_Object
{
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @param  mixed          $parent    The parent object this edge belongs to
	 * @param  Jadva_Tc_Node  $nodeFrom  The node at which this edge starts
	 * @param  Jadva_Tc_Node  $nodeTo    The node at which this edge ends
	 * @param  boolean        $directed  Not implemented yet. Should be: Whether this edge is a directed edge or not
	 */
	public function __construct($parent, Jadva_Tc_Node $nodeFrom, Jadva_Tc_Node $nodeTo, $directed)
	{
		parent::__construct();

		$this->_parent   = $parent;
		$this->_nodeFrom = $nodeFrom;
		$this->_nodeTo   = $nodeTo;

		$nodeFrom->addEdge($parent, $this, $nodeTo);
		$nodeTo  ->addEdge($parent, $this, $nodeTo);
	}
	//------------------------------------------------
	/**
	 * Returns the parent object
	 *
	 * @return  mixed  The parent object
	 */
	public function getParent()
	{
		$this->_bailIfDead();
		return $this->_parent;
	}
	//------------------------------------------------
	/**
	 * Returns the node at which this edge starts
	 *
	 * @return  Jadva_Tc_Node  The node at which this edge starts
	 */
	public function getNodeFrom()
	{
		$this->_bailIfDead();
		return $this->_nodeFrom;
	}
	//------------------------------------------------
	/**
	 * Returns the node at which this edge ends
	 *
	 * @return  Jadva_Tc_Node  The node at which this edge ends
	 */
	public function getNodeTo()
	{
		$this->_bailIfDead();

		return $this->_nodeTo;
	}
	//------------------------------------------------
	/**
	 * Contains the parent object
	 *
	 * @var  mixed
	 */
	protected $_parent;
	//------------------------------------------------
	/**
	 * Contains the node at which this edge starts
	 *
	 * @var  Jadva_Tc_Node
	 */
	protected $_nodeFrom;
	//------------------------------------------------
	/**
	 * Contains the node at which this edge ends
	 *
	 * @var  Jadva_Tc_Node
	 */
	protected $_nodeTo;
	//------------------------------------------------
	/**
	 * Removes itself from the from and to nodes, and cleans up the references
	 */
	protected function _innerDelete()
	{
		$this->_nodeFrom->removeOutgoingEdge($this);
		$this->_nodeTo  ->removeIncomingEdge($this);

		$this->_parent   = NULL;
		$this->_nodeFrom = NULL;
		$this->_nodeTo   = NULL;
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
