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
 * @version    $Id: Graph.php 44 2008-09-26 10:01:22Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Tc_Graph */
require_once 'Jadva/Tc/Graph.php';

/** @see Jadva_Tc_Test_Node */
require_once 'Jadva/Tc/Test/Node.php';

/** @see Jadva_Tc_Test_Edge */
require_once 'Jadva/Tc/Test/Edge.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * A graph, used in testing the components
 *
 * @category   JAdVA
 * @package    Jadva_Tc
 * @subpackage Jadva_Tc_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Tc_Test_Graph extends Jadva_Tc_Graph
{
	//------------------------------------------------
	/**
	 * Checks whether a graph has been destructed
	 *
	 * @param  integer  $graphId  The identity of the graph to check
	 *
	 * @return  TRUE if the graph has been destructed, FALSE otherwise
	 */
	public static function destructCalledForGraph($graphId)
	{
		return array_key_exists($graphId, self::$_destroyedGraphs);
	}
	//------------------------------------------------
	/**
	 * Stored the graph's identity in the list of destroyed graph identities
	 */
	public function __destruct()
	{
		self::$_destroyedGraphs[$this->getIdentity()] = TRUE;
		parent::__destruct();
	}
	//------------------------------------------------
	/**
	 * Contains the identity of the graphs that are destroyed
	 * @var  array
	 */
	protected static $_destroyedGraphs = array();
	//------------------------------------------------
	/**
	 * Contains the class used for creating the added nodes
	 * @var  string
	 */
	protected $_nodeClass = 'Jadva_Tc_Test_Node';
	//------------------------------------------------
	/**
	 * Contains the class used for creating the added edges
	 * @var  string
	 */
	protected $_edgeClass = 'Jadva_Tc_Test_Edge';
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
