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
 * @copyright  Copyright (c) 2010 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: MysqliTest.php 358 2010-09-24 11:51:09Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
if( !defined('TESTS_JADVA_BASE_DIR') ) {
	define('TESTS_JADVA_BASE_DIR', dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR);
}
//----------------------------------------------------------------------------------------------------------------------
require_once TESTS_JADVA_BASE_DIR . 'TestHelper.php';
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Installer_OutputFormatter_Array */
require_once 'Jadva/Installer/OutputFormatter/Array.php';
/** @see Jadva_Installer_Database_Mysqli */
require_once 'Jadva/Installer/Database/Mysqli.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class tests the Jadva_Installer_Database_Mysqli class.
 *
 * This class connects to a MySQL database, jadva_test, and will destroy any data therein.
 *
 * @category   JAdVA
 * @package    Jadva
 * @subpackage UnitTests
 */
class Jadva_Installer_Database_MysqliTest extends PHPUnit_Framework_TestCase
{
	public static function getOptions()
	{
		return array(
			'outputFormatter'  => new Jadva_Installer_OutputFormatter_Array,
			'restoreDirectory' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Restore' . DIRECTORY_SEPARATOR,
			'databaseName'     => 'jadva_test',
		);
	}

	protected $_mysqlConn = NULL;

	public function setUp()
	{
		$this->testFileDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Mysqli' . DIRECTORY_SEPARATOR;

		if( NULL === $this->_mysqlConn ) {
			$this->_mysqlConn = new mysqli(
				'db',
				'root',
				'toor',
				'mysql',
				3306,
			);
		}

		$this->_mysqlConn->query('DROP DATABASE IF EXISTS jadva_test;');
		$this->_mysqlConn->query('CREATE DATABASE jadva_test;');
		$this->_mysqlConn->query('CREATE USER "Jadva_Test"@"%" IDENTIFIED BY "Jadva_Test";');
		$this->_mysqlConn->query('GRANT ALL PRIVILEGES ON jadva_test.* TO "Jadva_Test"@"%";');
	}

	public function __destruct()
	{
		if( $this->_mysqlConn ) {
			$this->_mysqlConn->close();
		}
	}

	public function testNoContent()
	{
		$installer = $this->createInstaller('NoContent');

		$installer->install();

		$this->assertInstallResult(
			$installer,
			0, 0, 0, 0, 0, 1, 12, 0, 6,
			array(
				'foo' => 1
			)
		);
	}

	public function testCreateTable()
	{
		$installer = $this->createInstaller('CreateTable');

		$installer->install();

		$this->assertInstallResult(
			$installer,
			0, 0, 0, 0, 0, 1, 12, 0, 6,
			array(
				'foo' => 1
			),
			array(
				'test',
			)
		);
	}

	public function testCreateAlterTable()
	{
		$installer = $this->createInstaller('CreateAlterTable');

		$installer->install();

		$this->assertInstallResult(
			$installer,
			0, 0, 0, 0, 0, 1, 13, 0, 7,
			array(
				'foo' => 2
			),
			array(
				'test',
			)
		);
	}
	
	public function testIsEssential()
	{
		$installer = $this->createInstaller('IsEssential');

		$installer->install();

		$this->assertMessageNotExists($installer, 'foo.1 requires content for bar.1, but none was given');

		$this->assertInstallResult(
			$installer,
			0, 0, 0, 0, 0, 1, 10, 0, 5,
			array()
		);
	}
	
	public function testIsEssentialExists()
	{
		$installer = $this->createInstaller('IsEssentialExists');

		$installer->install();

		$this->assertMessageNotExists($installer, 'foo.1 requires content for bar.1, but none was given');

		$this->assertInstallResult(
			$installer,
			0, 0, 0, 0, 0, 1, 13, 0, 7,
			array(
				'foo' => 1,
				'bar' => 1,
			),
			array(
				'bar',
			)
		);
	}

	public function testIsEssentialMissing()
	{
		$installer = $this->createInstaller('IsEssentialMissing');

		$installer->install();

		$this->assertMessageExists($installer, 'foo.1 requires content for bar.1, but none was given');

		$this->assertInstallResult(
			$installer,
			0, 0, 1, 1, 0, 0, 4, 0, 0,
			array()
		);
	}

	public function testIsEssentialMissing2()
	{
		$installer = $this->createInstaller('IsEssentialMissing2');

		$installer->install();

		$this->assertMessageExists($installer, 'third.1 requires content for first.1, but none was given');

		$this->assertInstallResult(
			$installer,
			0, 0, 1, 1, 0, 0, 4, 0, 0,
			array()
		);
	}

	//
	// ADDITIONAL ASSERTING FUNCTIONS
	//

	public function createInstaller($dirName)
	{
		$options = self::getOptions();

		$installer = new Jadva_Installer_Database_Mysqli($options);
		$installer->setCredentials('Jadva_Test', 'Jadva_Test', 'db', 3306)
			->addDirectory($this->testFileDir . $dirName . DIRECTORY_SEPARATOR);

		return $installer;
	}


	public function assertMessageExists(Jadva_Installer_Database_Mysqli $installer, $message)
	{
		$messageFound = FALSE;
		foreach($installer->getOutputFormatter()->getMessages() as $info) {
			if( $info[1] === $message ) {
				$messageFound = TRUE;
				break;
			}
		}

		$this->assertTrue($messageFound);
	}

	public function assertMessageNotExists(Jadva_Installer_Database_Mysqli $installer, $message)
	{
		$messageFound = FALSE;
		foreach($installer->getOutputFormatter()->getMessages() as $info) {
			if( $info[1] === $message ) {
				$messageFound = TRUE;
				break;
			}
		}

		$this->assertFalse($messageFound);
	}

	public function assertInstallResult(
		Jadva_Installer_Database_Mysqli $installer,
		$emerg, $alert, $crit, $err, $warning, $notice, $info, $debug, $success,
		array $scriptLevels, array $createdTables = array()
	) {
		foreach($installer->getOutputFormatter()->getLevelCounts() as $level => $count) {
			$this->assertEquals($$level, $count, 'Message count for ' . $level . ' is off');
		}

		if( $info < 8 ) {
			//The version table wasn't checked, so doesn't exist
			return;
		}

		$this->_mysqlConn->select_db('jadva_test');
		$result = $this->_mysqlConn->query('SELECT * FROM jadva_installer_database_table_versions');
		if( FALSE === $result ) {
			trigger_error($this->_mysqlConn->error);
			return;
		}

		while( $obj = $result->fetch_object() ){
			$this->assertArrayHasKey($obj->scriptName, $scriptLevels);
			$this->assertEquals($scriptLevels[$obj->scriptName], $obj->scriptVersion);
			unset($scriptLevels[$obj->scriptName]);
		}
		$this->assertTrue(empty($scriptLevels));


		$result = $this->_mysqlConn->query('SHOW TABLES');
		if( FALSE === $result ) {
			trigger_error($this->_mysqlConn->error);
			return;
		}

		while( $obj = $result->fetch_object() ){
			if( 'jadva_installer_database_table_versions' === $obj->Tables_in_jadva_test ) {
				continue;
			}

			$key = array_search($obj->Tables_in_jadva_test, $createdTables);
			$this->assertFalse(is_bool($key), 'Created a table that shouldn\'t be created');
			unset($createdTables[$key]);
		}
		$this->assertTrue(empty($createdTables), 'Not all tables were created');
	}
}
//----------------------------------------------------------------------------------------------------------------------
