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
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Database_ScriptLists
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: List.php 44 2008-09-26 10:01:22Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Tc_Graph */
require_once 'Jadva/Tc/Graph.php';

/** @see Jadva_Installer_Database_TableNode */
require_once 'Jadva/Installer/Database/TableNode.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class represents a list of Database Script/Version nodes for a single database type
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Database_ScriptLists
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Installer_Database_TableNode_List extends Jadva_Tc_Graph
{
	//------------------------------------------------
	/**
	 * Default constructor. Initialises the graph and sets the database type
	 *
	 * @param  string  $dbType  The database type this script list is for
	 */
	public function __construct($dbType)
	{
		parent::__construct();
		$this->_dbType = (string) $dbType;
	}
	//------------------------------------------------
	/**
	 * Returns the database type this script is for
	 *
	 * @return  string  The database type this script is for
	 */
	public function getDbType()
	{
		return $this->_dbType;
	}
	//------------------------------------------------
	/**
	 * Adds a node to the graph
	 *
	 * Extends the parent by setting the scriptName and scriptVersion from the options.
	 *
	 * @return  Jadva_Tc_Node  The new node
	 */
	public function addNode(array $options = array())
	{
		if( !array_key_exists('scriptName', $options) ) {
			trigger_error('Missing argument "scriptName" for ' . __METHOD__, E_USER_WARNING);
		}

		if( !array_key_exists('scriptVersion', $options) ) {
			trigger_error('Missing argument "scriptVersion" for ' . __METHOD__, E_USER_WARNING);
		}

		$node                = parent::addNode();
		$node->dbType        = $this->_dbType;
		$node->scriptName    = $options['scriptName'];
		$node->scriptVersion = $options['scriptVersion'];

		//Add all the nodes that represent earlier versions of the node
		if( $node->scriptVersion > 1 ) {
			$prevNode = $this->getNode($node->scriptName, $node->scriptVersion - 1);
			$this->addEdge($node, $prevNode);
		}

		return $node;
	}
	//------------------------------------------------
	/**
	 * Searches through the list of nodes for the node with the requested name and version
	 *
	 * @param  string   $scriptName     The name of the script to look for
	 * @param  integer  $scriptVersion  The version of the script to look for
	 *
	 * @return FALSE|Jadva_Tc_Node  The requested node, or FALSE if it couldn't be found
	 */
	public function findNode($scriptName, $scriptVersion)
	{
		foreach($this->_nodeList as $node) {
			if( ($node->getScriptName() == $scriptName) && ($node->getScriptVersion() == $scriptVersion) ) {
				return $node;
			}
		}
		return FALSE;
	}
	//------------------------------------------------
	/**
	 * Returns a node with the given scriptName and scriptVersion.
	 *
	 * Searches if the node already exists, creates it otherwise
	 */
	public function getNode($scriptName, $scriptVersion)
	{
		$node = $this->findNode($scriptName, $scriptVersion);
		if( FALSE === $node ) {
			$node = $this->addNode(array(
				'scriptName'    => $scriptName,
				'scriptVersion' => $scriptVersion,
			));
		}

		return $node;
	}
	//------------------------------------------------
	/**
	 * Contains the database type this script list is for
	 * @var  string
	 */
	protected $_dbType   = NULL;
	//------------------------------------------------
	/**
	 * Contains the class used for creating the added nodes
	 * @var  string
	 */
	protected $_nodeClass = 'Jadva_Installer_Database_TableNode';
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
