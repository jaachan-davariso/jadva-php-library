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
 * @version    $Id: RightsTest.php 252 2009-08-21 10:47:40Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
if( !defined('TESTS_JADVA_BASE_DIR') ) {
	define('TESTS_JADVA_BASE_DIR', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
}
//----------------------------------------------------------------------------------------------------------------------
require_once TESTS_JADVA_BASE_DIR . 'TestHelper.php';
//----------------------------------------------------------------------------------------------------------------------
require_once 'vfsStream/vfsStream.php';
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File_Abstract */
require_once 'Jadva/File/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class tests the Jadva_File_Abstract class.
 *
 * @category   JAdVA
 * @package    Jadva
 * @subpackage UnitTests
 */
class Jadva_File_RightsTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		if( !Jadva_File_Abstract::fileSchemeIsLinux() ) {
			$this->markTestSkipped(
				'Cannot tests rights on windows platform.'
			);
		}

		$this->_testFileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'testFile.txt';
		$this->_testDirectoryName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'testDirectory' . DIRECTORY_SEPARATOR;
	}

	protected function tearDown()
	{
		chmod($this->_testFileName, 0644);
		chmod($this->_testDirectoryName, 0755);
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotReadable
	 */
	public function testVerifyFileReadable()
	{
		chmod($this->_testFileName, 0333);
		Jadva_File_Abstract::verifyExistance($this->_testFileName, Jadva_File_Abstract::FLAG_IS_READABLE);
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotWritable
	 */
	public function testVerifyFileWritable()
	{
		chmod($this->_testFileName, 0555);
		Jadva_File_Abstract::verifyExistance($this->_testFileName, Jadva_File_Abstract::FLAG_IS_WRITABLE);
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotExecutable
	 */
	public function testVerifyFileExecutable()
	{
		chmod($this->_testFileName, 0666);
		Jadva_File_Abstract::verifyExistance($this->_testFileName, Jadva_File_Abstract::FLAG_IS_EXECUTABLE);
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotReadable
	 */
	public function testVerifyDirectoryReadable()
	{
		chmod($this->_testDirectoryName, 0333);
		Jadva_File_Abstract::verifyExistance($this->_testDirectoryName, Jadva_File_Abstract::FLAG_IS_READABLE);
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotWritable
	 */
	public function testVerifyDirectoryWritable()
	{
		chmod($this->_testDirectoryName, 0555);
		Jadva_File_Abstract::verifyExistance($this->_testDirectoryName, Jadva_File_Abstract::FLAG_IS_WRITABLE);
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotExecutable
	 */
	public function testVerifDirectoryExecutable()
	{
		chmod($this->_testDirectoryName, 0666);
		Jadva_File_Abstract::verifyExistance($this->_testDirectoryName, Jadva_File_Abstract::FLAG_IS_EXECUTABLE);
	}
}
//----------------------------------------------------------------------------------------------------------------------
