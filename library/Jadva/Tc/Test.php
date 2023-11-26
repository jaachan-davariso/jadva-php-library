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
/** @see Jadva_Test_Abstract */
require_once 'Jadva/Test/Abstract.php';

/** @see Jadva_Tc_Graph_Test */
require_once 'Jadva/Tc/Graph/Test.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Tests for the TC components of the JAdVA library
 *
 * @category   JAdVA
 * @package    Jadva_Tc
 * @subpackage Jadva_Tc_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Tc_Test extends Jadva_Test_Abstract
{
	//------------------------------------------------
	/**
	 * Execute the tests
	 *
	 * @return void
	 */
	//------------------------------------------------
	public function executeTests()
	{
		$graphTest = new Jadva_Tc_Graph_Test;
		$graphTest->executeTests();
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
