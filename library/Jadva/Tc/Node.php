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
 * This class represenst a node object
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
class Jadva_Tc_Node extends Jadva_Tc_Object
{
	//------------------------------------------------
	/**
	 * The colour of this node is unknown
	 */
	const COLOUR_UNKNOWN = 'colourUnknown';
	//------------------------------------------------
	/**
	 * The colour of this node is white
	 */
	const COLOUR_WHITE   = 'colourWhite';
	//------------------------------------------------
	/**
	 * The colour of this node is grey
	 */
	const COLOUR_GREY    = 'colourGrey';
	//------------------------------------------------
	/**
	 * The colour of this node is black
	 */
	const COLOUR_BLACK   = 'colourBlack';
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * Sets the colour to unkown
	 */
	public function __construct($parentObject)
	{
		parent::__construct();

		$this->_parent        = $parentObject;
		$this->_colour        = self::COLOUR_UNKNOWN;
		$this->_predecessor   = NULL;

		$this->_edgesOutgoing = array();
		$this->_edgesIncoming = array();

		$this->_readOnly      = FALSE;
	}
	//------------------------------------------------
	/**
	 * clone magic function
	 *
	 * You may not alter clones of nodes
	 */
	public function __clone()
	{
		$this->_readOnly = TRUE;
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
	 * Sets the colour
	 *
	 * @param  string  $newColour  The new colour
	 *
	 * @return  Jadva_Tc_Node  Provides a fluent interface
	 */
	public function setColour($newColour)
	{
		$this->_bailIfDead();
		$this->_bailIfReadOnly();
		$this->_colour = (string) $newColour;
		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the colour
	 *
	 * @return  string  The colour
	 */
	public function getColour()
	{
		$this->_bailIfDead();
		return $this->_colour;
	}
	//------------------------------------------------
	/**
	 * Sets the discovery time
	 *
	 * @param  integer  $newDiscoveryTime  The new discovery time
	 *
	 * @return  Jadva_Tc_Node  Provides a fluent interface
	 */
	public function setDiscoveryTime($newDiscoveryTime)
	{
		$this->_bailIfDead();
		$this->_bailIfReadOnly();
		$this->_discoveryTime = (integer) $newDiscoveryTime;
		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the discovery time
	 *
	 * @return  integer  The discovery time
	 */
	public function getDiscoveryTime()
	{
		$this->_bailIfDead();

		return $this->_discoveryTime;
	}
	//------------------------------------------------
	/**
	 * Sets the finishing time
	 *
	 * @param  integer  $newFinishingTime  The new finishing time
	 *
	 * @return  Jadva_Tc_Node  Provides a fluent interface
	 */
	public function setFinishingTime($newFinishingTime)
	{
		$this->_bailIfDead();
		$this->_bailIfReadOnly();
		$this->_finishingTime = (integer) $newFinishingTime;
		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the finishing time
	 *
	 * @return  integer  The finishing time
	 */
	public function getFinishingTime()
	{
		$this->_bailIfDead();
		return $this->_finishingTime;
	}
	//------------------------------------------------
	/**
	 * Sets the distance from the source in node count
	 *
	 * @param  NULL|integer  $newDistance  The new distance
	 *
	 * @return  NULL|integer  Provides a fluent interface
	 */
	public function setDistance($newDistance)
	{
		$this->_bailIfDead();
		$this->_bailIfReadOnly();

		$this->_distance = (int) $newDistance;
	}
	//------------------------------------------------
	/**
	 * Returns the distance from the source in node count
	 *
	 * @return  NULL|integer The distance
	 */
	public function getDistance()
	{
		$this->_bailIfDead();
		return $this->_distance;
	}
	//------------------------------------------------
	/**
	 * Sets the distance from the source in total edge length
	 *
	 * @param  NULL|integer  $newDistanceLength  The new distance
	 *
	 * @return  NULL|integer  Provides a fluent interface
	 */
	public function setDistanceLength($newDistanceLength)
	{
		$this->_bailIfDead();
		$this->_bailIfReadOnly();

		$this->_distanceLength = (int) $newDistanceLength;
	}
	//------------------------------------------------
	/**
	 * Returns the distance from the source in total edge length
	 *
	 * @return  NULL|integer The distance
	 */
	public function getDistanceLength()
	{
		$this->_bailIfDead();
		return $this->_distanceLength;
	}
	//------------------------------------------------
	/**
	 * Sets the predecessor
	 *
	 * @param  NULL|Jadva_Tc_Node  $newPredecessor  The new predecessor
	 *
	 * @return  NULL|Jadva_Tc_Node  Provides a fluent interface
	 */
	public function setPredecessor($newPredecessor)
	{
		$this->_bailIfDead();
		$this->_bailIfReadOnly();
		if( (NULL !== $newPredecessor) && !($newPredecessor instanceof Jadva_Tc_Node)) {
			trigger_error('Argument 1 passed to ' . __METHOD__ . '() must be an instance of Jadva_Tc_Node or NULL', E_USER_ERROR);
		}

		$this->_predecessor = $newPredecessor;
	}
	//------------------------------------------------
	/**
	 * Returns the predecessor
	 *
	 * @return  NULL|Jadva_Tc_Node The predecessor
	 */
	public function getPredecessor()
	{
		$this->_bailIfDead();
		return $this->_predecessor;
	}
	//------------------------------------------------
	/**
	 * Sets the source node that the distance parameters count for
	 *
	 * @param  NULL|Jadva_Tc_Node  $newSource  The new source
	 *
	 * @return  NULL|Jadva_Tc_Node  Provides a fluent interface
	 */
	public function setSource($newSource)
	{
		$this->_bailIfDead();
		$this->_bailIfReadOnly();
		if( (NULL !== $newSource) && !($newSource instanceof Jadva_Tc_Node)) {
			trigger_error('Argument 1 passed to ' . __METHOD__ . '() must be an instance of Jadva_Tc_Node or NULL', E_USER_ERROR);
		}

		$this->_source = $newSource;
	}
	//------------------------------------------------
	/**
	 * Returns the source
	 *
	 * @return  NULL|Jadva_Tc_Node The source
	 */
	public function getSource()
	{
		$this->_bailIfDead();
		return $this->_source;
	}
	//------------------------------------------------
	/**
	 * Adds an edge that starts at this node
	 *
	 * @todo  Don't think we need $parent or $nodeTo
	 *
	 * @param  mixed          $parent  The parent object
	 * @param  Jadva_Tc_Edge  $edge    The edge to add
	 * @param  Jadva_Tc_Node  $nodeTo  The node that edge is to
	 *
	 * @return  Jadva_Tc_Node  Provides a fluent interface
	 */
	public function addEdge($parent, Jadva_Tc_Edge $edge, Jadva_Tc_Node $nodeTo)
	{
		$this->_bailIfDead();
		$this->_bailIfReadOnly();

		if( $parent !== $this->_parent ) {
			require_once 'Jadva/Tc/Exception.php';
			throw new Jadva_Tc_Exception('Parent object mismatch');
		}

		if(
			($edge->getNodeFrom()->getIdentity() !== $this->getIdentity())
		&&
			($edge->getNodeTo()->getIdentity()   !== $this->getIdentity())
		) {
			require_once 'Jadva/Tc/Exception.php';
			throw new Jadva_Tc_Exception('Node mismatch');
		}

		if(
			($edge->getNodeFrom()->getIdentity() !== $nodeTo->getIdentity())
		&&
			($edge->getNodeTo()->getIdentity()   !== $nodeTo->getIdentity())
		) {
			require_once 'Jadva/Tc/Exception.php';
			throw new Jadva_Tc_Exception('Node mismatch');
		}

		//If the node is to ourselves, it will be added to both lists.
		if( $edge->getNodeFrom()->getIdentity() === $this->getIdentity() ) {
			$this->_edgesOutgoing[$edge->getIdentity()] = $edge;
		}

		if( $edge->getNodeTo()->getIdentity()   === $this->getIdentity() ) {
			$this->_edgesIncoming[$edge->getIdentity()] = $edge;
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Removes an edge that starts at this node
	 *
	 * @todo  What if this edge is a loop?
	 *
	 * @param  Jadva_Tc_Edge  $edge    The edge to remove
	 *
	 * @return  Jadva_Tc_Node  Provides a fluent interface
	 */
	public function removeOutgoingEdge(Jadva_Tc_Edge $edge)
	{
		$this->_bailIfDead();
		$this->_bailIfReadOnly();

		if( $edge->getParent() !== $this->getParent() ) {
			require_once 'Jadva/Tc/Exception.php';
			throw new Jadva_Tc_Exception('Parent mismatch');
		}

		unset($this->_edgesOutgoing[$edge->getIdentity()]);

		return $this;
	}
	//------------------------------------------------
	/**
	 * Removes an edge that ends at this node
	 *
	 * @todo  What if this edge is a loop?
	 *
	 * @param  Jadva_Tc_Edge  $edge    The edge to remove
	 *
	 * @return  Jadva_Tc_Node  Provides a fluent interface
	 */
	public function removeIncomingEdge(Jadva_Tc_Edge $edge)
	{
		$this->_bailIfDead();
		$this->_bailIfReadOnly();

		if( $edge->getParent() !== $this->getParent() ) {
			require_once 'Jadva/Tc/Exception.php';
			throw new Jadva_Tc_Exception('Parent mismatch');
		}

		unset($this->_edgesIncoming[$edge->getIdentity()]);
	}
	//------------------------------------------------
	/**
	 * Returns a list of all edges that start at this node
	 *
	 * @return  array
	 */
	public function getOutgoingEdges()
	{
		$this->_bailIfDead();
		return $this->_edgesOutgoing;
	}
	//------------------------------------------------
	/**
	 * Returns a list of all edges that end at this node
	 *
	 * @return  array
	 */
	public function getIncomingEdges()
	{
		$this->_bailIfDead();
		return $this->_edgesIncoming;
	}
	//------------------------------------------------
	/**
	 * Contains whether this class instance is read-only
	 *
	 * @var  boolean
	 */
	protected $_readOnly;
	//------------------------------------------------
	/**
	 * Contains the parent object
	 *
	 * @var  mixed
	 */
	protected $_parent;
	//------------------------------------------------
	/**
	 * Contains the colour
	 *
	 * @var  string
	 */
	protected $_colour;
	//------------------------------------------------
	/**
	 * Contains the predecessor
	 *
	 * @var  NULL|Jadva_Tc_Node
	 */
	protected $_predecessor;
	//------------------------------------------------
	/**
	 * Contains the source
	 *
	 * @var  NULL|Jadva_Tc_Node
	 */
	protected $_source;
	//------------------------------------------------
	/**
	 * Contains the discovery time
	 *
	 * @var  mixed
	 */
	protected $_discoveryTime;
	//------------------------------------------------
	/**
	 * Contains the finishing time
	 *
	 * @var  mixed
	 */
	protected $_finishingTime;
	//------------------------------------------------
	/**
	 * Contains the list of edges that start at this node
	 *
	 * @var  mixed
	 */
	protected $_edgesOutgoing;
	//------------------------------------------------
	/**
	 * Contains the list of edges that end at this node
	 *
	 * @var  mixed
	 */
	protected $_edgesIncoming;
	//------------------------------------------------
	/**
	 * Contains the distance from the source in node count
	 *
	 * @var  integer
	 */
	protected $_distance;
	//------------------------------------------------
	/**
	 * Contains the distance from the source in edge length
	 *
	 * @var  integer
	 */
	protected $_distanceLength;
	//------------------------------------------------
	/**
	 * Deletes all the incoming and outgoing edges, and removes the references to them
	 *
	 * @return  void
	 */
	protected function _innerDelete()
	{
		foreach($this->_edgesOutgoing as $edge) {
			$edge->delete();
		}
		$this->_edgesOutgoing = array();

		foreach($this->_edgesIncoming as $edge) {
			$edge->delete();
		}
		$this->_edgesIncoming = array();

		$this->_parent   = NULL;
	}
	//------------------------------------------------
	/**
	 * Throws an exception if this object is read-only
	 *
	 * @throws  Jadva_Tc_Exception  When this object is read-only
	 * @return  void
	 */
	protected function _bailIfReadOnly()
	{
		if( TRUE === $this->_readOnly ) {
			require_once 'Jadva/Tc/Exception.php';
			throw new Jadva_Tc_Exception('This object is read-only and cannot be changed anymore');
		}
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
