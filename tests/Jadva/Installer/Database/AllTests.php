<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * JAdVA library tests
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
 * @package    Jadva
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2009 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: AllTests.php 358 2010-09-24 11:51:09Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
if( !defined('TESTS_JADVA_BASE_DIR') ) {
	define('TESTS_JADVA_BASE_DIR', dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR);
}
//----------------------------------------------------------------------------------------------------------------------
require_once TESTS_JADVA_BASE_DIR . 'TestHelper.php';
//----------------------------------------------------------------------------------------------------------------------
if( !defined('PHPUnit_MAIN_METHOD') ) {
	define('PHPUnit_MAIN_METHOD', 'Jadva_Installer_Database_AllTests::main');
}
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Installer_Database_MysqliTest */
require_once 'Jadva/Installer/Database/MysqliTest.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class runs all the tests in the JAdVA PHP Library
 *
 * @category   JAdVA
 * @package    Jadva
 * @subpackage UnitTests
 */
class Jadva_Installer_Database_AllTests
{
	public static function main()
	{
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('JAdVA PHP Library - Jadva_Installer_Database');

		$suite->addTestSuite('Jadva_Installer_Database_MysqliTest');

		return $suite;
	}
}
//----------------------------------------------------------------------------------------------------------------------
if( PHPUnit_MAIN_METHOD == 'Jadva_Installer_Database_AllTests::main' ) {
	Jadva_Installer_Database_AllTests::main();
}
//----------------------------------------------------------------------------------------------------------------------
