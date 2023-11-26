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
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: AbstractTest.php 228 2009-07-24 10:52:35Z jaachan $
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
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_File_AbstractTest extends PHPUnit_Framework_TestCase
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

		$this->_path->rp_ALLTESTS_PHP = realpath('makefile');
		$this->_path->rp_DOT          = realpath('.') . DIRECTORY_SEPARATOR;

		foreach($this->_path as $var => $val) {
			if( isset($this->_url->{$var}) ) {
				continue;
			}

			$this->_url->{$var} = 'file://' . str_replace(DIRECTORY_SEPARATOR, '/', $val);
		}

	}

	public function testRoot()
	{
		$dir = Jadva_File_Abstract::getInstanceFor($this->_url->ROOT);

		$this->assertTrue($dir->isDir());
	}

	public function testNoSchemeIsFileScheme()
	{
		if( Jadva_File_Abstract::fileSchemeIsLinux() ) {
			$file = Jadva_File_Abstract::getInstanceFor('/test/a.out');

			$this->assertEquals(Jadva_File_Abstract::SCHEME_FILE, $file->getScheme());
			$this->assertTrue($file->isSchemeFile());
			$this->assertEquals('file:///test/a.out', $file->getUrl());
			$this->assertFalse($file->isDir());
		} else {
			$file = Jadva_File_Abstract::getInstanceFor('D:\\test\\a.out');

			$this->assertEquals(Jadva_File_Abstract::SCHEME_FILE, $file->getScheme());
			$this->assertTrue($file->isSchemeFile());
			$this->assertEquals('file://D:/test/a.out', $file->getUrl());
			$this->assertFalse($file->isDir());
		}
	}

	public function testOtherSchemeIsNotFileScheme()
	{
		$file = Jadva_File_Abstract::getInstanceFor($this->_url->FILE_NOT_EMPTY_EXT_DIR_FILE);
		$this->assertFalse($file->isSchemeFile());
	}

	public function testGetInstanceForExistingInstance()
	{
		$file = Jadva_File_Abstract::getInstanceFor('/test/a.out');

		$file2 = Jadva_File_Abstract::getInstanceFor($file);

		$this->assertEquals($file, $file2);
	}

	public function testNonExistingFile()
	{
		$file = Jadva_File_Abstract::getInstanceFor($this->_url->FILE_TEST_PHP);

		$this->assertEquals($this->_url->FILE_TEST_PHP, $file->getUrl());
		$this->assertEquals($this->_path->FILE_TEST_PHP, $file->getPath());
		$this->assertFalse($file->isDir());
		$this->assertFalse($file->exists());
	}

	public function testNonExistingDir()
	{
		$dir = Jadva_File_Abstract::getInstanceFor($this->_url->FILE_DIRECTORY);

		$this->assertEquals($this->_url->FILE_DIRECTORY, $dir->getUrl());
		$this->assertEquals($this->_path->FILE_DIRECTORY, $dir->getPath());
		$this->assertTrue($dir->isDir());
		$this->assertFalse($dir->exists());
	}

	public function testExistingFile()
	{
		$file = Jadva_File_Abstract::getInstanceFor($this->_url->FILE_LIST_CSV);

		$this->assertEquals($this->_url->FILE_LIST_CSV, $file->getUrl());
		$this->assertEquals($this->_path->FILE_LIST_CSV, $file->getPath());
		$this->assertFalse($file->isDir());
		$this->assertTrue($file->exists());
	}

	public function testExistingFileWithDirSep()
	{
		$file = Jadva_File_Abstract::getInstanceFor($this->_url->FILE_LIST_CSV . '/');

		$this->assertEquals($this->_url->FILE_LIST_CSV, $file->getUrl());
		$this->assertEquals($this->_path->FILE_LIST_CSV, $file->getPath());
		$this->assertFalse($file->isDir());
		$this->assertTrue($file->exists());
	}

	public function testExistingDir()
	{
		$dir = Jadva_File_Abstract::getInstanceFor($this->_url->FILE_EMPTY_EXT_DIR);

		$this->assertEquals($this->_url->FILE_EMPTY_EXT_DIR, $dir->getUrl());
		$this->assertEquals($this->_path->FILE_EMPTY_EXT_DIR, $dir->getPath());
		$this->assertTrue($dir->isDir());
		$this->assertTrue($dir->exists());
	}

	public function testExistingDirWithoutDirSep()
	{
		$dir = Jadva_File_Abstract::getInstanceFor(rtrim($this->_url->FILE_EMPTY_EXT_DIR, '/'));

		$this->assertEquals($this->_url->FILE_EMPTY_EXT_DIR, $dir->getUrl());
		$this->assertEquals($this->_path->FILE_EMPTY_EXT_DIR, $dir->getPath());
		$this->assertTrue($dir->isDir());
		$this->assertTrue($dir->exists());
	}

	public function testStartSlash()
	{
		$file = Jadva_File_Abstract::getInstanceFor(vfsStream::url('/' . self::FILE_LIST_CSV));

		$this->assertEquals($this->_url->FILE_LIST_CSV, $file->getUrl());
		$this->assertEquals($this->_path->FILE_LIST_CSV, $file->getPath());
		$this->assertFalse($file->isDir());
		$this->assertTrue($file->exists());
	}

	public function testStartSlashSlash()
	{
		$file = Jadva_File_Abstract::getInstanceFor(vfsStream::url('//' . self::FILE_LIST_CSV));

		$this->assertEquals($this->_url->FILE_LIST_CSV, $file->getUrl());
		$this->assertEquals($this->_path->FILE_LIST_CSV, $file->getPath());
		$this->assertFalse($file->isDir());
		$this->assertTrue($file->exists());
	}

	public function testDot()
	{
		$file = Jadva_File_Abstract::getInstanceFor(vfsStream::url('/./' . self::FILE_LIST_CSV));

		$this->assertEquals($this->_url->FILE_LIST_CSV, $file->getUrl());
		$this->assertEquals($this->_path->FILE_LIST_CSV, $file->getPath());
		$this->assertFalse($file->isDir());
		$this->assertTrue($file->exists());
	}

	public function testDotDot()
	{
		$file = Jadva_File_Abstract::getInstanceFor($this->_url->FILE_DIRECTORY . '/../' . self::FILE_LIST_CSV);

		$this->assertEquals($this->_url->FILE_LIST_CSV, $file->getUrl());
		$this->assertEquals($this->_path->FILE_LIST_CSV, $file->getPath());
		$this->assertFalse($file->isDir());
		$this->assertTrue($file->exists());
	}

	public function testFaultyDirectorySeparators()
	{
		$file = Jadva_File_Abstract::getInstanceFor(vfsStream::url(self::FILE_NOT_EMPTY_EXT_DIR . '\\' . self::FILE_NOT_EMPTY_EXT_DIR_FILE));

		$this->assertEquals($this->_url->FILE_NOT_EMPTY_EXT_DIR_FILE, $file->getUrl());
		$this->assertEquals($this->_path->FILE_NOT_EMPTY_EXT_DIR_FILE, $file->getPath());
		$this->assertFalse($file->isDir());
		$this->assertTrue($file->exists());
	}

	public function testFile()
	{
		$file = Jadva_File_Abstract::getInstanceFor($this->_path->FILE__);

		$this->assertEquals($this->_url->FILE__, $file->getUrl());
		$this->assertEquals($this->_path->FILE__, $file->getPath());
		$this->assertFalse($file->isDir());
		$this->assertTrue($file->exists());
	}

	public function testDir()
	{
		$dir = Jadva_File_Abstract::getInstanceFor($this->_path->DIR__);

		$this->assertEquals($this->_url->DIR__, $dir->getUrl());
		$this->assertEquals($this->_path->DIR__, $dir->getPath());
		$this->assertTrue($dir->isDir());
		$this->assertTrue($dir->exists());
	}

	public function testRealPathFile()
	{
		$file = Jadva_File_Abstract::realpath('makefile');

		$this->assertEquals($this->_url->rp_ALLTESTS_PHP, $file->getUrl());
		$this->assertEquals($this->_path->rp_ALLTESTS_PHP, $file->getPath());
		$this->assertFalse($file->isDir());
		$this->assertTrue($file->exists());
	}

	public function testRealPathDir()
	{
		$dir = Jadva_File_Abstract::realpath('.');

		$this->assertEquals($this->_url->rp_DOT, $dir->getUrl());
		$this->assertEquals($this->_path->rp_DOT, $dir->getPath());
		$this->assertTrue($dir->isDir());
		$this->assertTrue($dir->exists());
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotExists
	 */
	public function testVerifyExistanceNotExisting()
	{
		Jadva_File_Abstract::verifyExistance(str_replace('.txt', '.nef', $this->_testFileName), 0);
	}

	public function testVerifyExistanceExisting()
	{
		file_put_contents($this->_testFileName, 'About to check for existence');

		$file = Jadva_File_Abstract::verifyExistance($this->_testFileName, 0);

		$this->assertEquals($this->_testFileName, $file->getPath());
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotExists
	 */
	public function testVerifyExistanceNotExistingVfs()
	{
		Jadva_File_Abstract::verifyExistance(str_replace('.csv', '.nef', $this->_url->FILE_LIST_CSV), 0);
	}

	public function testVerifyExistanceExistingVfs()
	{
		$file = Jadva_File_Abstract::verifyExistance($this->_url->FILE_LIST_CSV, 0);

		$this->assertEquals($this->_url->FILE_LIST_CSV, $file->getUrl());
	}

	//----
	// Testing Access rights
	//---

	public function testVerifyFileReadableVfs()
	{
		$vfsFile = $this->_root->getChild(self::FILE_LIST_CSV);
		$vfsFile->chmod(0444);

		$file = Jadva_File_Abstract::verifyExistance($this->_url->FILE_LIST_CSV, Jadva_File_Abstract::FLAG_IS_READABLE);

		$this->assertEquals($this->_url->FILE_LIST_CSV, $file->getUrl());
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotReadable
	 */
	public function testVerifyFileNotReadableVfs()
	{
		$vfsFile = $this->_root->getChild(self::FILE_LIST_CSV);
		$vfsFile->chmod(0333);

		Jadva_File_Abstract::verifyExistance($this->_url->FILE_LIST_CSV, Jadva_File_Abstract::FLAG_IS_READABLE);
	}

	public function testVerifyFileWritableVfs()
	{
		$vfsFile = $this->_root->getChild(self::FILE_LIST_CSV);
		$vfsFile->chmod(0222);

		$file = Jadva_File_Abstract::verifyExistance($this->_url->FILE_LIST_CSV, Jadva_File_Abstract::FLAG_IS_WRITABLE);

		$this->assertEquals($this->_url->FILE_LIST_CSV, $file->getUrl());
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotWritable
	 */
	public function testVerifyFileNotWritableVfs()
	{
		$vfsFile = $this->_root->getChild(self::FILE_LIST_CSV);
		$vfsFile->chmod(0555);

		Jadva_File_Abstract::verifyExistance($this->_url->FILE_LIST_CSV, Jadva_File_Abstract::FLAG_IS_WRITABLE);
	}

	public function testVerifyFileExecutableVfs()
	{
		$vfsFile = $this->_root->getChild(self::FILE_LIST_CSV);
		$vfsFile->chmod(0111);

		$dir = Jadva_File_Abstract::verifyExistance($this->_url->FILE_LIST_CSV, Jadva_File_Abstract::FLAG_IS_EXECUTABLE);

		$this->assertEquals($this->_url->FILE_LIST_CSV, $dir->getUrl());
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotExecutable
	 */
	public function testVerifyFileNotExecutableVfs()
	{
		$vfsFile = $this->_root->getChild(self::FILE_LIST_CSV);
		$vfsFile->chmod(0666);

		Jadva_File_Abstract::verifyExistance($this->_url->FILE_LIST_CSV, Jadva_File_Abstract::FLAG_IS_EXECUTABLE);
	}

	//----
	// End testing Access rights
	//---

	public function testGetBasename()
	{
		$file = Jadva_File_Abstract::getInstanceFor($this->_url->FILE_NOT_EMPTY_EXT_DIR_FILE);
		$this->assertEquals(self::FILE_NOT_EMPTY_EXT_DIR_FILE, $file->getBasename());

		$dir = Jadva_File_Abstract::getInstanceFor($this->_url->FILE_NOT_EMPTY_EXT_DIR);
		$this->assertEquals(self::FILE_NOT_EMPTY_EXT_DIR, $dir->getBasename());

		$file = Jadva_File_Abstract::getInstanceFor($this->_path->FILE__);
		$this->assertEquals('AbstractTest.php', $file->getBasename());

		$dir = Jadva_File_Abstract::getInstanceFor($this->_path->DIR__);
		$this->assertEquals('File', $dir->getBasename());

	}

	public function testGetParentVfs()
	{
		$file = Jadva_File_Abstract::getInstanceFor($this->_url->FILE_NOT_EMPTY_EXT_DIR_FILE);
		$this->assertEquals($this->_url->FILE_NOT_EMPTY_EXT_DIR, $file->getParentUrl());
		$this->assertEquals($this->_path->FILE_NOT_EMPTY_EXT_DIR, $file->getParentPath());

		$dir = $file->getParent();
		$this->assertEquals($this->_url->FILE_NOT_EMPTY_EXT_DIR, $dir->getUrl());
		$this->assertEquals($this->_path->FILE_NOT_EMPTY_EXT_DIR, $dir->getPath());
	}

	public function testGetParent()
	{

		$file = Jadva_File_Abstract::getInstanceFor($this->_path->FILE__);
		$this->assertEquals($this->_url->DIR__, $file->getParentUrl());
		$this->assertEquals($this->_path->DIR__, $file->getParentPath());


		$dir = $file->getParent();
		$this->assertEquals($this->_url->DIR__, $dir->getUrl());
		$this->assertEquals($this->_path->DIR__, $dir->getPath());
	}
}
//----------------------------------------------------------------------------------------------------------------------
