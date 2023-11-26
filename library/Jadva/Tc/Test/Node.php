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
 * @version    $Id: Node.php 43 2008-09-26 09:04:34Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * A node, used in testing the components
 *
 * @category   JAdVA
 * @package    Jadva_Tc
 * @subpackage Jadva_Tc_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Tc_Test_Node extends Jadva_Tc_Node
{
	//------------------------------------------------
	/**
	 * Checks whether a node has been destructed
	 *
	 * @param  integer  $nodeId  The identity of the node to check
	 *
	 * @return  TRUE if the node has been destructed, FALSE otherwise
	 */
	public static function destructCalledForNode($nodeId)
	{
		return array_key_exists($nodeId, self::$_destroyedNodes);
	}
	//------------------------------------------------
	/**
	 * Stored the node's identity in the list of destroyed node identities
	 */
	public function __destruct()
	{
		self::$_destroyedNodes[$this->getIdentity()] = TRUE;
		parent::__destruct();
	}
	//------------------------------------------------
	/**
	 * Contains the identity of the nodes that are destroyed
	 * @var  array
	 */
	protected static $_destroyedNodes = array();
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
