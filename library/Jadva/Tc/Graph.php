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
/** @see Jadva_Tc_Object */
require_once 'Jadva/Tc/Object.php';

/** @see Jadva_Tc_Node */
require_once 'Jadva/Tc/Node.php';

/** @see Jadva_Tc_Edge */
require_once 'Jadva/Tc/Edge.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class represenst a graph object
 *
 * @category   JAdVA
 * @package    Jadva_Tc
 * @subpackage Jadva_Tc_Graph
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Tc_Graph extends Jadva_Tc_Object
{
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_nodeList = array();
		$this->_edgeList = array();

		if( !class_exists($this->_nodeClass, TRUE) ) {
			require_once 'Jadva/Tc/Graph/Exception.php';
			throw new Jadva_Tc_Graph_Exception('Undefined node class "' . $this->_nodeClass . '"');
		}

		if( ('Jadva_Tc_Node' !== $this->_nodeClass ) && !is_subclass_of($this->_nodeClass, 'Jadva_Tc_Node') ) {
			require_once 'Jadva/Tc/Graph/Exception.php';
			throw new Jadva_Tc_Graph_Exception('Node class must inherit from Jadva_Tc_Node');
		}

		if( !class_exists($this->_edgeClass, TRUE) ) {
			require_once 'Jadva/Tc/Graph/Exception.php';
			throw new Jadva_Tc_Graph_Exception('Undefined edge class "' . $this->_edgeClass . '"');
		}

		if( ('Jadva_Tc_Edge' !== $this->_edgeClass ) && !is_subclass_of($this->_edgeClass, 'Jadva_Tc_Edge') ) {
			require_once 'Jadva/Tc/Graph/Exception.php';
			throw new Jadva_Tc_Graph_Exception('Edge class must inherit from Jadva_Tc_Edge');
		}

		$this->_dfsFinished        = TRUE;  //No nodes
		$this->_bfsFinished        = TRUE;  //No nodes
		$this->_graphContainsCycle = FALSE; //No nodes
	}
	//------------------------------------------------
	/**
	 * Adds a node to the graph
	 *
	 * @param  array  $options  The options (ignored in this graph, can be used in descendants)
	 *
	 * @return  Jadva_Tc_Node  The new node that wast just added
	 */
	public function addNode(array $options = array())
	{
		$newNode = new $this->_nodeClass($this);
		$this->_nodeList[] = $newNode;
		$this->_dfsFinished = FALSE;
		$this->_bfsFinished = FALSE;
		return $newNode;
	}
	//------------------------------------------------
	/**
	 * Adds an edge to the graph
	 *
	 * @param  Jadva_Tc_Node  $nodeOne  The node that the new edge starts from
	 * @param  Jadva_Tc_Node  $nodeTwo  The node that the new edge ends in
	 * @param  array          $options  The options (ignored in this graph, can be used in descendants)
	 *
	 * @return  Jadva_Tc_Edge  The new edge that wast just added
	 */
	public function addEdge(Jadva_Tc_Node $nodeOne, Jadva_Tc_Node $nodeTwo, array $options = array())
	{
		$newEdge = new $this->_edgeClass($this, $nodeOne, $nodeTwo, FALSE);
		$this->_edgeList[] = $newEdge;
		$this->_dfsFinished = FALSE;
		$this->_bfsFinished = FALSE;
		return $newEdge;
	}
	//------------------------------------------------
	/**
	 * Removes an edge from the graph
	 *
	 * @param  Jadva_Tc_Edge  $edge  The edge to remove
	 *
	 * @return  void
	 */
	public function removeEdge(Jadva_Tc_Edge $edge)
	{
		if( $edge->getParent() !== $this ) {
			require_once 'Jadva/Tc/Exception.php';
			throw new Jadva_Tc_Exception('Parent mismatch');
		}

		$edge->delete();
		$this->_dfsFinished = FALSE;
		$this->_bfsFinished = FALSE;
	}
	//------------------------------------------------
	/**
	 * Returns TRUE if this graph contains cycles, FALSE otherwise
	 *
	 * @return  boolean  TRUE if this graph contains cycles, FALSE otherwise
	 */
	public function containsCycle()
	{
		$this->_dfs();

		return $this->_graphContainsCycle;
	}
	//------------------------------------------------
	/**
	 * Returns (clones of) the nodes in this graph, sorted topological
	 *
	 * @param  boolean  $clone  (OPTIONAL) Whether to create clones; by default this is on to memory leaks
	 *
	 * @return  array  The nodes
	 */
	public function getNodesAsTopologicalSort($clone = TRUE)
	{
		$this->_dfs();

		if( $this->containsCycle() ) {
			require_once 'Jadva/Tc/Graph/Exception.php';
			throw new Jadva_Tc_Graph_Exception('Cannot retrieve topological sort for cyclic graph');
		}

		$return = array();
		foreach($this->_nodeList as $node) {
			$return[ $node->getIdentity() ] = $clone ? clone $node : $node;
		}

		uasort($return, create_function('$a, $b', 'return $b->finishingTime < $a->finishingTime;'));

		return $return;
	}
	//------------------------------------------------
	/**
	 * Does a Depth First Search with the given callbacks
	 *
	 * The array should contain only valid callbacks (see call_user_func). The preNodeVisit is called before a node
	 * is visited, the postNodeVisit is called after it is visited.
	 *
	 * @param  array  $callbacks  The functions to call when visiting
	 *
	 * @return  void
	 */
	public function dfs(array $callbacks)
	{
		$this->_dfsFinished = FALSE;

		$this->_dfs($callbacks);
	}
	//------------------------------------------------
	/**
	 * Does a Breadth-first Search with the given callbacks
	 *
	 * The array should contain only valid callbacks (see call_user_func). The preNodeVisit is called before a node
	 * is visited, the postNodeVisit is called after it is visited.
	 *
	 * @param  Jadva_Tc_Node  $source     (OPTIONAL) The node to start form. Will take any node if not passed.
	 * @param  array          $callbacks  (OPTIONAL) The functions to call when visiting
	 *
	 * @return  void
	 */
	public function bfs($source = NULL, array $callbacks = array())
	{
		//Parameter overloading
		if( is_array($source) ) {
			$callbacks = $source;
			$source = NULL;
		}

		//See if there's anything to search at all
		if( empty($this->_nodeList) ) {
			return;
		}

		//Pick a source if none given, check if it is
		if( empty($source) ) {
			$source = $this->_nodeList[0];
		} elseif( $source->getParent() != $this ) {
			require_once 'Jadva/Tc/Graph/Exception.php';
			throw new Jadva_Tc_Graph_Exception('Invalid source; does not belong to this graph');
		}

		//Tell the _bfs function to do a new search
		$this->_bfsFinished = FALSE;

		//And go
		$this->_bfs($source, $callbacks);
	}
	//------------------------------------------------
	/**
	 * Contains all the nodes in this graph
	 *
	 * @var  array
	 */
	protected $_nodeList;
	//------------------------------------------------
	/**
	 * Contains all the edge in this graph
	 *
	 * @var  array
	 */
	protected $_edgeList;
	//------------------------------------------------
	/**
	 * When $this->_dfsFinished, this variable contains TRUE if this graph contains cycles, FALSE otherwise
	 *
	 * @var  array
	 */
	protected $_graphContainsCycle;
	//------------------------------------------------
	/**
	 * Contains whether a DFS was finished for the current nodes/edges
	 *
	 * @var  boolean
	 */
	protected $_dfsFinished;
	//------------------------------------------------
	/**
	 * Contains whether a BFS was finished for the current nodes/edges
	 *
	 * @var  boolean
	 */
	protected $_bfsFinished;
	//------------------------------------------------
	/**
	 * Contains the clock for the DFS function
	 *
	 * @var  boolean
	 */
	protected $_dfsTime;
	//------------------------------------------------
	/**
	 * Contains the clock for the BFS function
	 *
	 * @var  boolean
	 */
	protected $_bfsTime;
	//------------------------------------------------
	/**
	 * Contains the class used for creating the added nodes
	 * @var  string
	 */
	protected $_nodeClass = 'Jadva_Tc_Node';
	//------------------------------------------------
	/**
	 * Contains the class used for creating the added edges
	 * @var  string
	 */
	protected $_edgeClass = 'Jadva_Tc_Edge';
	//------------------------------------------------
	/**
	 * Does a Depth First Search on the current graph
	 *
	 * @param  array  $callbacks  Contains callbacks for events in the DFS
	 *
	 * @return void
	 */
	protected function _dfs(array $callbacks = array())
	{
		if( $this->_dfsFinished ) {
			return;
		}

		$this->_graphContainsCycle = FALSE;

		//Set the colour to white (undiscovered) and clear other properties we'll set later
		foreach($this->_nodeList as $node) {
			$node->colour        = Jadva_Tc_Node::COLOUR_WHITE;
			$node->predecessor   = NULL;
			$node->discoveryTime = NULL;
			$node->finishingTime = NULL;
		}

		//Start the clock
		$this->_dfsTime = 0;

		//Visit all nodes
		foreach($this->_nodeList as $node) {
			//But only if they're undiscovered
			if( Jadva_Tc_Node::COLOUR_WHITE === $node->colour ) {
				$this->_dfsNodeVisit($node, $callbacks);
			}
		}

		$this->_dfsFinished = TRUE;
	}
	//------------------------------------------------
	/**
	 * Visits a node in a DFS
	 *
	 * @todo  Add support for $callbacks
	 *
	 * @param  Jadva_Tc_Node  $nodeU      The node to visit
	 * @param  array          $callbacks  Not implemented yet. Should handle callbacks for events in the DFS
	 *
	 * @return void
	 */
	protected function _dfsNodeVisit(Jadva_Tc_Node $nodeU, array $callbacks = array())
	{
		//Set the colour to grey (discovered), increase the time and set the discovery time
		$nodeU->colour        = Jadva_Tc_Node::COLOUR_GREY;
		$nodeU->discoveryTime = ++$this->_dfsTime;

		//Visit all the nodes reachable from this node
		foreach($nodeU->getOutgoingEdges() as $edge) {
			$nodeV = $edge->getNodeTo();

			//If the node is grey (discovered), this edge is a Back Edge
			if( Jadva_Tc_Node::COLOUR_GREY === $nodeV->colour ) {
				$this->_graphContainsCycle = TRUE;
			}

			//If the node is white (undiscovered), visit it
			if( Jadva_Tc_Node::COLOUR_WHITE === $nodeV->colour ) {
				$nodeV->predecessor = $nodeU;
				$this->_dfsNodeVisit($nodeV, $callbacks);
			}
		}

		if( array_key_exists('preNodeVisit', $callbacks) ) {
			call_user_func($callbacks['preNodeVisit'], $nodeU);
		}

		//Set the colour to black (finished), increase the time and set the finishing time
		$nodeU->colour        = Jadva_Tc_Node::COLOUR_BLACK;
		$nodeU->finishingTime = ++$this->_dfsTime;

		if( array_key_exists('postNodeVisit', $callbacks) ) {
			call_user_func($callbacks['postNodeVisit'], $nodeU);
		}
	}
	//------------------------------------------------
	/**
	 * Does a Breadth First Search on the current graph
	 *
	 * @param  Jadva_Tc_Node  $source     The source of the search
	 * @param  array          $callbacks  Contains callbacks for events in the BFS
	 *
	 * @return void
	 */
	protected function _bfs(Jadva_Tc_Node $source, array $callbacks = array())
	{
		if( $this->_bfsFinished ) {
			return;
		}

		//Set the colour to white (undiscovered) and clear other properties we'll set later
		foreach($this->_nodeList as $node) {
			$node->colour        = Jadva_Tc_Node::COLOUR_WHITE;
			$node->predecessor   = NULL;
			$node->discoveryTime = NULL;
			$node->finishingTime = NULL;

			$node->source = $source;
			$node->distance = INF;
		}

		//Source is slightly different
		$source->colour = Jadva_Tc_Node::COLOUR_GREY;
		$source->distance = 0;

		$queue = array($source);
		while( count($queue) ) {
			$nodeU = array_shift($queue);
			foreach($nodeU->getOutgoingEdges() as $edge) {
				$nodeV = $edge->getNodeTo();

				if( Jadva_Tc_Node::COLOUR_WHITE !== $nodeV->colour ) {
					continue;
				}


				$nodeV->colour = Jadva_Tc_Node::COLOUR_GREY;
				$nodeV->distance = $nodeU->distance + 1;
				$nodeV->distanceLength = $nodeU->distance + $edge->length;
				$nodeV->predecessor = $nodeU;

				$queue[] = $nodeV;
			}

			if( array_key_exists('preNodeVisit', $callbacks) ) {
				call_user_func($callbacks['preNodeVisit'], $nodeU);
			}

			$nodeU->colour = Jadva_Tc_Node::COLOUR_BLACK;

			if( array_key_exists('postNodeVisit', $callbacks) ) {
				call_user_func($callbacks['postNodeVisit'], $nodeU);
			}
		}

		$this->_bfsFinished = TRUE;
	}
	//------------------------------------------------
	/**
	 * Cleans up the references to the child objects
	 *
	 * @return void
	 */
	protected function _innerDelete()
	{
		foreach($this->_nodeList as $node) {
			$node->delete();
		}

		//All the edges are already dead

		unset($this->_nodeList);
		unset($this->_edgeList);
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
