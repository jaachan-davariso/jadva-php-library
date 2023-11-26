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
 * @subpackage Jadva_Tc_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Mysqli.php 39 2008-09-25 13:59:21Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Tc_Graph */
require_once 'Jadva/Tc/Graph.php';

/** @see Jadva_Tc_Test_Graph */
require_once 'Jadva/Tc/Test/Graph.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Tests for the Graph object
 *
 * @category   JAdVA
 * @package    Jadva_Tc
 * @subpackage Jadva_Tc_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Tc_Graph_Test extends Jadva_Test_Abstract
{
	//------------------------------------------------
	/**
	 * Execute the tests
	 *
	 * @return void
	 */
	public function executeTests()
	{
		$this->basicTests();
		$this->cycleTests();
		$this->topologicalSortTests();
	}
	//------------------------------------------------
	/**
	 * Execute the basic tests
	 *
	 * @return void
	 */
	public function basicTests()
	{
		$this->creationAndDestructionGraph();
		$this->creationAndDestructionGraphWithOneNode();
		$this->creationAndDestructionGraphWithTwoNodes();
		$this->creationAndDestructionGraphWithTwoNodesOneEdge();
	}
	//------------------------------------------------
	/**
	 * Execute the cycle tests
	 *
	 * @return void
	 */
	public function cycleTests()
	{
		$node = array();
		$edge = array();

		$graph = new Jadva_Tc_Graph;
		$this->_assertFalse($graph->containsCycle(), '"Detect cycle", for graph with 0 node(s), 0 self-loop(s), 0 edge(s) and 0 removed edge(s)', TRUE);

		$node[1] = $graph->addNode();
		$this->_assertFalse($graph->containsCycle(), '"Detect cycle", for graph with 1 node(s), 0 self-loop(s), 0 edge(s) and 0 removed edge(s)', TRUE);

		$edge[1][1] = $graph->addEdge($node[1], $node[1]);
		$this->_assertTrue ($graph->containsCycle(), '"Detect cycle", for graph with 1 node(s), 1 self-loop(s), 0 edge(s) and 0 removed edge(s)', TRUE);

		$graph->removeEdge($edge[1][1]);
		$this->_assertFalse($graph->containsCycle(), '"Detect cycle", for graph with 1 node(s), 0 self-loop(s), 0 edge(s) and 1 removed edge(s)', TRUE);

		$node[2] = $graph->addNode();
		$this->_assertFalse($graph->containsCycle(), '"Detect cycle", for graph with 2 node(s), 0 self-loop(s), 0 edge(s) and 1 removed edge(s)', TRUE);

		$edge[1][2] = $graph->addEdge($node[1], $node[2]);
		$this->_assertFalse($graph->containsCycle(), '"Detect cycle", for graph with 2 node(s), 0 self-loop(s), 1 edge(s) and 1 removed edge(s)', TRUE);

		$edge[2][2] = $graph->addEdge($node[2], $node[2]);
		$this->_assertTrue ($graph->containsCycle(), '"Detect cycle", for graph with 2 node(s), 1 self-loop(s), 1 edge(s) and 1 removed edge(s)', TRUE);

		$graph->removeEdge($edge[2][2]);
		$this->_assertFalse($graph->containsCycle(), '"Detect cycle", for graph with 2 node(s), 0 self-loop(s), 1 edge(s) and 2 removed edge(s)', TRUE);

		$edge[2][1] = $graph->addEdge($node[2], $node[1]);
		$this->_assertTrue ($graph->containsCycle(), '"Detect cycle", for graph with 2 node(s), 0 self-loop(s), 2 edge(s) and 2 removed edge(s)', TRUE);

		$graph->removeEdge($edge[2][1]);
		$this->_assertFalse($graph->containsCycle(), '"Detect cycle", for graph with 2 node(s), 0 self-loop(s), 1 edge(s) and 3 removed edge(s)', TRUE);

		$node[3] = $graph->addNode();
		$this->_assertFalse($graph->containsCycle(), '"Detect cycle", for graph with 3 node(s), 0 self-loop(s), 1 edge(s) and 3 removed edge(s)', TRUE);

		$edge[2][3] = $graph->addEdge($node[2], $node[3]);
		$this->_assertFalse($graph->containsCycle(), '"Detect cycle", for graph with 3 node(s), 0 self-loop(s), 2 edge(s) and 3 removed edge(s)', TRUE);

		$edge[3][1] = $graph->addEdge($node[3], $node[1]);
		$this->_assertTrue ($graph->containsCycle(), '"Detect cycle", for graph with 3 node(s), 0 self-loop(s), 3 edge(s) and 3 removed edge(s)', TRUE);

		$graph->delete();
		unset($graph);
	}
	//------------------------------------------------
	/**
	 * Execute the topological sort tests
	 *
	 * @return void
	 */
	public function topologicalSortTests()
	{
		$graph = new Jadva_Tc_Graph;

		$node[0]       = $graph->addNode();
		$identity[0]   = $node[0]->getIdentity();
		$list          = $graph->getNodesAsTopologicalSort();
		$this->_assertTrue(is_array($list),   '"Retrieve topological sort", returned variable is an array', FALSE);
		$this->_assertTrue(count($list) == 1, '"Retrieve topological sort", returned 1 node', TRUE);

		$identityMatch = array_key_exists($identity[0], $list);
		$this->_assertTrue($identityMatch,    '"Retrieve topological sort", returned the right identity', TRUE);

		$edge[0][0]    = $graph->addEdge($node[0], $node[0]);
		try {
			$list      = $graph->getNodesAsTopologicalSort();
			$success   = FALSE;
		} catch( Jadva_Tc_Graph_Exception $e ) {
			$success   = TRUE;
		}
		$this->_assertTrue($success, '"Retrieve topological sort", retrieving a cyclic graph', TRUE);
		$graph->removeEdge($edge[0][0]);

		$node[1]       = $graph->addNode();
		$identity[1]   = $node[1]->getIdentity();
		$list          = $graph->getNodesAsTopologicalSort();
		$this->_assertTrue(count($list) == 2, '"Retrieve topological sort", returned 2 nodes', TRUE);

		$identityMatch = array_key_exists($identity[0], $list) && array_key_exists($identity[1], $list);
		$this->_assertTrue($identityMatch   , '"Retrieve topological sort", returned the right identity', TRUE);

		$edge[0][1]    = $graph->addEdge($node[0], $node[1]);
		$list          = $graph->getNodesAsTopologicalSort();
		$identityOrder = array($identity[1], $identity[0]);

		$this->_assertIdentityListMatches($list, $identityOrder, '"Retrieve topological sort", with 2 linked nodes', TRUE);

		$node[2]       = $graph->addNode();
		$identity[2]   = $node[2]->getIdentity();
		$edge[2][0]    = $graph->addEdge($node[0], $node[1]);
		$list          = $graph->getNodesAsTopologicalSort();
		$identityOrder = array($identity[1], $identity[0], $identity[2]);
		$this->_assertIdentityListMatches($list, $identityOrder, '"Retrieve topological sort", with 3 linked nodes', TRUE);

		$graph->delete();
		unset($graph);
	}
	//------------------------------------------------
	/**
	 * Execute the creation and destruction of a graph test
	 *
	 * @return void
	 */
	public function creationAndDestructionGraph()
	{
		$graph = new Jadva_Tc_Test_Graph;
		$graphIdentity = $graph->getIdentity();
		unset($graph);
		$graphDestructed = Jadva_Tc_Test_Graph::destructCalledForGraph($graphIdentity);
		$this->_assertTrue($graphDestructed, 'Creation and destruction of graph', FALSE);
	}
	//------------------------------------------------
	/**
	 * Execute the creation and destruction of a graph with one node test
	 *
	 * @return void
	 */
	public function creationAndDestructionGraphWithOneNode()
	{
		$graph = new Jadva_Tc_Test_Graph;
		$graphIdentity = $graph->getIdentity();

		$node = $graph->addNode();
		$nodeIdentity = $node->getIdentity();
		unset($node);

		$graph->delete();
		$nodeDeleted = Jadva_Tc_Test_Node::destructCalledForNode($nodeIdentity);
		$this->_assertTrue($nodeDeleted, 'Creation and destruction of node added to graph', FALSE);

		unset($graph);
		$graphDestructed = Jadva_Tc_Test_Graph::destructCalledForGraph($graphIdentity);
		$this->_assertTrue($graphDestructed, 'Creation and destruction of graph after adding a node', FALSE);
	}
	//------------------------------------------------
	/**
	 * Execute the creation and destruction of a graph with two nodes test
	 *
	 * @return void
	 */
	public function creationAndDestructionGraphWithTwoNodes()
	{
		$graph = new Jadva_Tc_Test_Graph;
		$graphIdentity = $graph->getIdentity();

		$node = $graph->addNode();
		$nodeIdentity1 = $node->getIdentity();
		unset($node);

		$node = $graph->addNode();
		$nodeIdentity2 = $node->getIdentity();
		unset($node);

		$graph->delete();

		$nodeDeleted1 = Jadva_Tc_Test_Node::destructCalledForNode($nodeIdentity1);
		$nodeDeleted2 = Jadva_Tc_Test_Node::destructCalledForNode($nodeIdentity2);
		$this->_assertTrue($nodeDeleted1 && $nodeDeleted2, 'Creation and destruction of two node added to a graph', FALSE);

		unset($graph);
		$graphDestructed = Jadva_Tc_Test_Graph::destructCalledForGraph($graphIdentity);
		$this->_assertTrue($graphDestructed, 'Creation and destruction of a graph after adding two nodes', FALSE);
	}
	//------------------------------------------------
	/**
	 * Execute the creation and destruction of a graph with two nodes and one edge test
	 *
	 * @return void
	 */
	public function creationAndDestructionGraphWithTwoNodesOneEdge()
	{
		$graph = new Jadva_Tc_Test_Graph;
		$graphIdentity = $graph->getIdentity();

		$node1 = $graph->addNode();
		$nodeIdentity1 = $node1->getIdentity();

		$node2 = $graph->addNode();
		$nodeIdentity2 = $node2->getIdentity();

		$edge = $graph->addEdge($node1, $node2);
		$edgeIdentity = $edge->getIdentity();
		unset($node1);
		unset($node2);
		unset($edge);

		$graph->delete();

		$nodeDeleted1 = Jadva_Tc_Test_Node::destructCalledForNode($nodeIdentity1);
		$nodeDeleted2 = Jadva_Tc_Test_Node::destructCalledForNode($nodeIdentity2);
		$this->_assertTrue($nodeDeleted1 && $nodeDeleted2, 'Creation and destruction of two node with an edge added to a graph', FALSE);

		$edgeDeleted = Jadva_Tc_Test_Edge::destructCalledForEdge($edgeIdentity);
		$this->_assertTrue($edgeDeleted, 'Creation and destruction an edge added to a graph', FALSE);

		unset($graph);
		$graphDestructed = Jadva_Tc_Test_Graph::destructCalledForGraph($graphIdentity);
		$this->_assertTrue($graphDestructed, 'Creation and destruction of a graph after adding two nodes and an edge', FALSE);
	}
	//------------------------------------------------
	/**
	 * Asserts whether a list of identity matches in order
	 *
	 * @param  array    $list           The list of objects to check
	 * @param  array    $identityOrder  The list of identities to match to $list (in order)
	 * @param  string   $message        The message to display
	 * @param  boolean  $continue       Whether to continue if this test fails
	 *
	 * @return void
	 */
	protected function _assertIdentityListMatches(array $list, array $identityOrder, $message, $continue)
	{
		$key_list      = array_keys($list);
		$success       = TRUE;
		foreach($key_list as $index => $key) {
			$success = $success && ($identityOrder[$index] == $list[$key]->getIdentity());
		}
		$this->_assertTrue($success, $message, $continue);
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
