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
 * @package    Jadva_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Mysqli.php 39 2008-09-25 13:59:21Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * Tests for the Graph object
 *
 * @category   JAdVA
 * @package    Jadva_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
abstract class Jadva_Test_Abstract
{
	//------------------------------------------------
	/**
	 * Executes the tests
	 */
	abstract public function executeTests();
	//------------------------------------------------
	/**
	 * Asserts that $condition is FALSE
	 *
	 * @param  mixed    $condition  The condition to parse
	 * @param  string   $message    The message to display
	 * @param  boolean  $continue   Whether to continue on the tests if this one fails
	 *
	 * @return void
	 */
	protected function _assertFalse($condition, $message, $continue)
	{
		$this->_displayTest($message, FALSE === $condition, $continue);
	}
	//------------------------------------------------
	/**
	 * Asserts that $condition is TRUE
	 *
	 * @param  mixed    $condition  The condition to parse
	 * @param  string   $message    The message to display
	 * @param  boolean  $continue   Whether to continue on the tests if this one fails
	 *
	 * @return void
	 */
	protected function _assertTrue($condition, $message, $continue)
	{
		$this->_displayTest($message, TRUE === $condition, $continue);
	}
	//------------------------------------------------
	/**
	 * Asserts that $expResult is strictly equal to $actResult
	 *
	 * @param  mixed    $expResult  The expected result
	 * @param  mixed    $actResult  The actual result
	 * @param  string   $message    The message to display
	 * @param  boolean  $continue   Whether to continue on the tests if this one fails
	 *
	 * @return void
	 */
	protected function _assertEqualsStrict($expResult, $actResult, $message, $continue = TRUE)
	{
		$this->_displayTest($message, $expResult === $actResult, $continue);
	}
	//------------------------------------------------
	/**
	 * Displays a message
	 *
	 * @param  string   $message    The message to display
	 * @param  boolean  $success    Whether the test was successfull
	 * @param  boolean  $continue   Whether to continue on the tests if this one fails
	 *
	 * @return void
	 */
	protected function _displayTest($message, $success, $continue)
	{
		echo str_pad($message, 120, '.');
		if( $success ) {
			echo '[PASS]';
		} else {
			echo '[FAIL]';
		}
		echo PHP_EOL;
		if( !$success && !$continue ) {
			echo 'Critical test failed; cannot continue.' . PHP_EOL;
			exit;
		}
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
