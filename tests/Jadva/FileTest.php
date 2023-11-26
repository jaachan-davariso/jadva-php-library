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
 * @version    $Id: FileTest.php 255 2009-08-21 12:02:18Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
if( !defined('TESTS_JADVA_BASE_DIR') ) {
	define('TESTS_JADVA_BASE_DIR', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
}
//----------------------------------------------------------------------------------------------------------------------
require_once TESTS_JADVA_BASE_DIR . 'TestHelper.php';
//----------------------------------------------------------------------------------------------------------------------
require_once 'vfsStream/vfsStream.php';
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File */
require_once 'Jadva/File.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class tests the Jadva_File class.
 *
 * @category   JAdVA
 * @package    Jadva
 * @subpackage UnitTests
 */
class Jadva_FileTest extends PHPUnit_Framework_TestCase
{
	const FILE_TEST_PHP  = 'test.php';
	const FILE_DIRECTORY = 'directory';
	const FILE_LIST_CSV  = 'list.csv';
	const FILE_EMPTY_EXT_DIR = 'emptyExistingDir';
	const FILE_NOT_EMPTY_EXT_DIR = 'notEmptyExistingDir';
	const FILE_NOT_EMPTY_EXT_DIR_FILE = 'file.txt';

	protected function setUp()
	{
		$this->_root = new vfsStreamDirectory('root');

		vfsStreamWrapper::register();
		vfsStreamWrapper::setRoot($this->_root);

		$file = vfsStream::newFile(self::FILE_LIST_CSV);
		$file->setContent('1,2,3' . "\n" . '1,4,9' . "\n" . '1,16,81' . "\n");
		$this->_root->addChild($file);

		$dir = new vfsStreamDirectory(self::FILE_EMPTY_EXT_DIR);
		$this->_root->addChild($dir);

		$dir = new vfsStreamDirectory(self::FILE_NOT_EMPTY_EXT_DIR);
		$this->_root->addChild($dir);

		$file = vfsStream::newFile(self::FILE_NOT_EMPTY_EXT_DIR_FILE);
		$file->setContent('This is text.');
		$dir->addChild($file);

		$this->_testFileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'testFile.txt';

		$this->_path = new stdClass;
		$this->_path->ROOT                        = '';
		$this->_path->FILE_TEST_PHP               = self::FILE_TEST_PHP;
		$this->_path->FILE_DIRECTORY              = self::FILE_DIRECTORY . '/';
		$this->_path->FILE_LIST_CSV               = self::FILE_LIST_CSV;
		$this->_path->FILE_EMPTY_EXT_DIR          = self::FILE_EMPTY_EXT_DIR . '/';
		$this->_path->FILE_NOT_EMPTY_EXT_DIR      = self::FILE_NOT_EMPTY_EXT_DIR . '/';
		$this->_path->FILE_NOT_EMPTY_EXT_DIR_FILE = $this->_path->FILE_NOT_EMPTY_EXT_DIR . self::FILE_NOT_EMPTY_EXT_DIR_FILE;

		$this->_url = new stdClass;
		foreach($this->_path as $var => $val) {
			$this->_url->{$var} = vfsStream::url($val);
		}

		$this->_path->FILE__ = __FILE__;
		$this->_path->DIR__  = dirname(__FILE__) . DIRECTORY_SEPARATOR;

		$this->_path->rp_ALLTESTS_PHP = realpath('AllTests.php');
		$this->_path->rp_DOT          = realpath('.') . DIRECTORY_SEPARATOR;

		$this->_path->Jadva_File               = $this->_path->DIR__ . 'File' . DIRECTORY_SEPARATOR;
		$this->_path->testDirectory            = $this->_path->Jadva_File . 'testDirectory' . DIRECTORY_SEPARATOR;
		$this->_path->testDirectoryTestFile    = $this->_path->testDirectory . 'testFile.txt';
		$this->_path->testFile                 = $this->_path->Jadva_File . 'testFile.txt';
		$this->_path->testImage                = $this->_path->Jadva_File . 'testImage.gif';
		$this->_path->testDirectorySubDir      = $this->_path->testDirectory . 'subDir' . DIRECTORY_SEPARATOR;
		$this->_path->testDirectoryTestFileOne = $this->_path->testDirectory . 'testFileOne' . DIRECTORY_SEPARATOR;

		foreach($this->_path as $var => $val) {
			if( isset($this->_url->{$var}) ) {
				continue;
			}

			$this->_url->{$var} = 'file://' . str_replace(DIRECTORY_SEPARATOR, '/', $val);
		}
	}

	protected function tearDown()
	{
		@unlink($this->_path->testDirectory . 'testFile.txt');
	}

	public function testVerifyFileExistance()
	{
		$file = Jadva_File::verifyExistance($this->_path->FILE__, 0);
		$this->assertEquals($this->_url->FILE__, $file->getUrl());
		$this->assertEquals($this->_path->FILE__, $file->getPath());
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotaFile
	 */
	public function testVerifyFileExistanceNotaFile()
	{
		Jadva_File::verifyExistance($this->_path->DIR__, 0);
	}

	public function testFileSize()
	{
		$file = Jadva_File::getInstanceFor($this->_path->FILE__);
		$this->assertEquals(filesize(__FILE__), $file->getSize());
	}

	public function testGetExtension()
	{
		$file = Jadva_File::getInstanceFor($this->_path->FILE__);
		$this->assertTrue($file->hasExtension());
		$this->assertEquals('php', $file->getExtension());
	}

	public function testIsImage()
	{
		$file = Jadva_File::getInstanceFor($this->_path->testImage);
		$this->assertTrue($file->isImage());
		$file = Jadva_File::getInstanceFor($this->_path->testFile);
		$this->assertFalse($file->isImage());
	}

	public function testCopy()
	{
		$contents = file_get_contents($this->_path->testFile);

		$file = Jadva_File::getInstanceFor($this->_url->testFile);
		$result = $file->copy(Jadva_File::getInstanceFor($this->_url->testDirectory));

		$this->assertTrue($result);
		$this->assertEquals($contents, file_get_contents($this->_path->testDirectoryTestFile));
	}

	public function testCopyExisting()
	{
		file_put_contents($this->_path->testDirectoryTestFile, 'Minus one');

		$contents = file_get_contents($this->_path->testFile);

		$file = Jadva_File::getInstanceFor($this->_url->testFile);
		$result = $file->copy(Jadva_File::getInstanceFor($this->_url->testDirectory));

		$this->assertTrue($result);
		$this->assertEquals($contents, file_get_contents($this->_path->testDirectoryTestFile));
	}

	public function testMove()
	{
		file_put_contents($this->_path->testFile, 'About to move you');

		$contents = file_get_contents($this->_path->testFile);

		$file = Jadva_File::getInstanceFor($this->_url->testFile);
		$result = $file->move(Jadva_File::getInstanceFor($this->_url->testDirectory));

		$this->assertTrue($result);

		$this->assertEquals($this->_path->testDirectoryTestFile, $file->getPath());
		$this->assertEquals($contents, file_get_contents($this->_path->testDirectoryTestFile));

		$this->assertFalse(file_exists($this->_path->testFile));
	}

	public function testMoveExisting()
	{
		file_put_contents($this->_path->testFile, 'About to move you');
		file_put_contents($this->_path->testDirectoryTestFile, 'Minus one');

		$contents = file_get_contents($this->_path->testFile);

		$file = Jadva_File::getInstanceFor($this->_url->testFile);
		$result = $file->move(Jadva_File::getInstanceFor($this->_url->testDirectory));

		$this->assertTrue($result);

		$this->assertEquals($this->_path->testDirectoryTestFile, $file->getPath());
		$this->assertEquals($contents, file_get_contents($this->_path->testDirectoryTestFile));

		$this->assertFalse(file_exists($this->_path->testFile));
	}

	public function testRemove()
	{
		$file = Jadva_File::getInstanceFor($this->_url->testFile);
		$file->remove();
		$this->assertFalse(file_exists($this->_path->testFile));
	}
}
//----------------------------------------------------------------------------------------------------------------------
