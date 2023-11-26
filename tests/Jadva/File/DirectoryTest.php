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
 * @version    $Id: DirectoryTest.php 252 2009-08-21 10:47:40Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
if( !defined('TESTS_JADVA_BASE_DIR') ) {
	define('TESTS_JADVA_BASE_DIR', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
}
//----------------------------------------------------------------------------------------------------------------------
require_once TESTS_JADVA_BASE_DIR . 'TestHelper.php';
//----------------------------------------------------------------------------------------------------------------------
require_once 'vfsStream/vfsStream.php';
require_once 'vfsStream/vfsStreamDirectory.php';
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File_Directory */
require_once 'Jadva/File/Directory.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class tests the Jadva_File_Directory class.
 *
 * @category   JAdVA
 * @package    Jadva
 * @subpackage UnitTests
 */
class Jadva_File_DirectoryTest extends PHPUnit_Framework_TestCase
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

		$this->_path->Jadva_File                    = $this->_path->DIR__;
		$this->_path->testDirectory                 = $this->_path->DIR__ . 'testDirectory' . DIRECTORY_SEPARATOR;
		$this->_path->testDirectorySubDir           = $this->_path->testDirectory . 'subDir' . DIRECTORY_SEPARATOR;
		$this->_path->testDirectoryTestFileOne      = $this->_path->testDirectory . 'testFileOne';

		$this->_path->testDirectoryWithFiles                 = $this->_path->Jadva_File . 'testDirectoryWithFiles' . DIRECTORY_SEPARATOR;
		$this->_path->testDirectoryWithFilesFileOne          = $this->_path->testDirectoryWithFiles . 'fileOne.txt';
		$this->_path->testDirectoryWithFilesFileTwo          = $this->_path->testDirectoryWithFiles . 'fileTwo.txt';
		$this->_path->testDirectoryWithFilesDirOne           = $this->_path->testDirectoryWithFiles . 'dirOne' . DIRECTORY_SEPARATOR;
		$this->_path->testDirectoryWithFilesDirOneFileThree  = $this->_path->testDirectoryWithFilesDirOne . 'fileThree.txt';

		$this->_path->testFile                  = $this->_path->Jadva_File . 'testFile.txt';
		$this->_path->testImage                 = $this->_path->Jadva_File . 'testImage.gif';
		$this->_path->testDirectorySubDir       = $this->_path->testDirectory . 'subDir' . DIRECTORY_SEPARATOR;
		$this->_path->testDirectoryTestFileOne  = $this->_path->testDirectory . 'testFileOne';


		$this->_path->testDirectoryTestDirectoryWithFiles                = $this->_path->testDirectory . 'testDirectoryWithFiles' . DIRECTORY_SEPARATOR;
		$this->_path->testDirectoryTestDirectoryWithFilesFileOne         = $this->_path->testDirectoryTestDirectoryWithFiles . 'fileOne.txt';
		$this->_path->testDirectoryTestDirectoryWithFilesFileTwo         = $this->_path->testDirectoryTestDirectoryWithFiles . 'fileTwo.txt';
		$this->_path->testDirectoryTestDirectoryWithFilesDirOne          = $this->_path->testDirectoryTestDirectoryWithFiles . 'dirOne' . DIRECTORY_SEPARATOR;
		$this->_path->testDirectoryTestDirectoryWithFilesDirOneFileThree = $this->_path->testDirectoryTestDirectoryWithFilesDirOne . 'fileThree.txt';

		foreach($this->_path as $var => $val) {
			if( isset($this->_url->{$var}) ) {
				continue;
			}

			$this->_url->{$var} = 'file://' . str_replace(DIRECTORY_SEPARATOR, '/', $val);
		}

		if( !is_dir($this->_path->testDirectoryWithFiles) ) {
			mkdir($this->_path->testDirectoryWithFiles);
		}

		if( !is_dir($this->_path->testDirectoryWithFilesDirOne) ) {
			mkdir($this->_path->testDirectoryWithFilesDirOne);
		}

		file_put_contents($this->_path->testDirectoryWithFilesFileOne, 'One (1)');
		file_put_contents($this->_path->testDirectoryWithFilesFileTwo, 'Two (2)');
		file_put_contents($this->_path->testDirectoryWithFilesDirOneFileThree, 'Three (3)');
	}

	public function tearDown()
	{
		@rmdir($this->_path->testDirectorySubDir);
		@unlink($this->_path->testDirectoryTestFileOne);

		if( is_dir($this->_path->testDirectoryTestDirectoryWithFiles) || file_exists($this->_path->testDirectoryTestDirectoryWithFiles) ) {
			@exec('rm -rf ' . $this->_path->testDirectoryTestDirectoryWithFiles);
		}

		if( is_dir($this->_path->testDirectoryWithFiles) || file_exists($this->_path->testDirectoryWithFiles) ) {
			@exec('rm -rf ' . $this->_path->testDirectoryWithFiles);
		}

		chmod($this->_path->testDirectory, 0755);
	}

	public function testVerifyDirectoryExistance()
	{
		$dir = Jadva_File_Directory::verifyExistance($this->_path->DIR__, 0);
		$this->assertEquals($this->_url->DIR__, $dir->getUrl());
		$this->assertEquals($this->_path->DIR__, $dir->getPath());
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotaDirectory
	 */
	public function testVerifyDirectoryExistanceNotaDirectory()
	{
		Jadva_File_Directory::verifyExistance($this->_path->FILE__, 0);
	}

	//----
	// Testing Access rights
	//---

	public function testVerifyDirectoryReadableVfs()
	{
		$vfsDir = $this->_root->getChild(self::FILE_NOT_EMPTY_EXT_DIR);
		$vfsDir->chmod(0444);

		$file = Jadva_File_Abstract::verifyExistance($this->_url->FILE_NOT_EMPTY_EXT_DIR, Jadva_File_Abstract::FLAG_IS_READABLE);

		$this->assertEquals($this->_url->FILE_NOT_EMPTY_EXT_DIR, $file->getUrl());
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotReadable
	 */
	public function testVerifyDirectoryNotReadableVfs()
	{
		$vfsDir = $this->_root->getChild(self::FILE_NOT_EMPTY_EXT_DIR);
		$vfsDir->chmod(0333);

		Jadva_File_Abstract::verifyExistance($this->_url->FILE_NOT_EMPTY_EXT_DIR, Jadva_File_Abstract::FLAG_IS_READABLE);
	}

	public function testVerifyDirectoryWritableVfs()
	{
		$vfsDir = $this->_root->getChild(self::FILE_NOT_EMPTY_EXT_DIR);
		$vfsDir->chmod(0222);

		$file = Jadva_File_Abstract::verifyExistance($this->_url->FILE_NOT_EMPTY_EXT_DIR, Jadva_File_Abstract::FLAG_IS_WRITABLE);

		$this->assertEquals($this->_url->FILE_NOT_EMPTY_EXT_DIR, $file->getUrl());
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotWritable
	 */
	public function testVerifyDirectoryNotWritableVfs()
	{
		$vfsDir = $this->_root->getChild(self::FILE_NOT_EMPTY_EXT_DIR);
		$vfsDir->chmod(0555);

		Jadva_File_Abstract::verifyExistance($this->_url->FILE_NOT_EMPTY_EXT_DIR, Jadva_File_Abstract::FLAG_IS_WRITABLE);
	}

//PHP does the is_executable on directories all wrong
//	public function testVerifyDirectoryExecutableVfs()
//	{
//		$vfsDir = $this->_root->getChild(self::FILE_NOT_EMPTY_EXT_DIR);
//		$vfsDir->chmod(0111);
//
//		$dir = Jadva_File_Abstract::verifyExistance($this->_url->FILE_NOT_EMPTY_EXT_DIR, Jadva_File_Abstract::FLAG_IS_EXECUTABLE);
//
//		$this->assertEquals($this->_url->FILE_NOT_EMPTY_EXT_DIR, $dir->getUrl());
//	}

	/**
	 * @expectedException  Jadva_File_Exception_NotExecutable
	 */
	public function testVerifyDirectoryNotExecutableVfs()
	{
		$vfsDir = $this->_root->getChild(self::FILE_NOT_EMPTY_EXT_DIR);
		$vfsDir->chmod(0666);

		Jadva_File_Abstract::verifyExistance($this->_url->FILE_NOT_EMPTY_EXT_DIR, Jadva_File_Abstract::FLAG_IS_EXECUTABLE);
	}

	//----
	// End testing Access rights
	//---

	public function testDirEnsureExistance()
	{
		$dir = Jadva_File_Abstract::getInstanceFor($this->_path->testDirectorySubDir);

		$dir->ensureExistance();

		$this->assertTrue($dir->exists());
	}

	public function testDirEnsureExistanceExisting()
	{
		$dir = Jadva_File_Abstract::getInstanceFor($this->_path->testDirectory);

		$dir->ensureExistance();

		$this->assertTrue($dir->exists());
	}

	/**
	 * @expectedException  Jadva_File_Exception_CouldNotCreateDirectory
	 */
	public function testDirEnsureExistanceFile()
	{
		$dir = Jadva_File_Abstract::getInstanceFor($this->_path->testDirectoryTestFileOne . '/');

		touch($this->_path->testDirectoryTestFileOne);
	
		$dir->ensureExistance();

		$this->assertTrue($dir->exists());
	}

	public function testGetFileVfs()
	{
		$dir = Jadva_File_Abstract::getInstanceFor($this->_url->FILE_NOT_EMPTY_EXT_DIR);
		$file = $dir->getFile(self::FILE_NOT_EMPTY_EXT_DIR_FILE);

		$this->assertEquals($this->_url->FILE_NOT_EMPTY_EXT_DIR_FILE, $file->getUrl());
		$this->assertTrue($file->exists());
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotaFile
	 */
	public function testGetFileVfsNotaFile()
	{
		$root = Jadva_File_Abstract::getInstanceFor($this->_url->ROOT);
		$dir  = $root->getFile(self::FILE_EMPTY_EXT_DIR);

		$this->assertEquals($this->_url->FILE_EMPTY_EXT_DIR, $dir->getUrl());
		$this->assertTrue($dir->exists());
	}

	public function testGetDirectoryVfs()
	{
		$root = Jadva_File_Abstract::getInstanceFor($this->_url->ROOT);
		$dir = $root->getSubDirectory(self::FILE_EMPTY_EXT_DIR);

		$this->assertEquals($this->_url->FILE_EMPTY_EXT_DIR, $dir->getUrl());
		$this->assertTrue($dir->exists());
	}

	/**
	 * @expectedException  Jadva_File_Exception_NotaDirectory
	 */
	public function testGetDirectoryVfsNotaDirectory()
	{
		$root = Jadva_File_Abstract::getInstanceFor($this->_url->ROOT);
		$file = $root->getSubDirectory(self::FILE_LIST_CSV);

		$this->assertEquals($this->_url->FILE_LIST_CSV, $file->getUrl());
		$this->assertTrue($file->exists());
	}


	public function testCopy()
	{
		$contentsOne = file_get_contents($this->_path->testDirectoryWithFilesFileOne);
		$contentsTwo = file_get_contents($this->_path->testDirectoryWithFilesFileTwo);
		$contentsThree = file_get_contents($this->_path->testDirectoryWithFilesDirOneFileThree);

		$dir = Jadva_File::getInstanceFor($this->_url->testDirectoryWithFiles);
		$dir->copy(Jadva_File::getInstanceFor($this->_url->testDirectory));

		$this->assertTrue(is_dir($this->_path->testDirectoryTestDirectoryWithFiles));
		$this->assertEquals($contentsOne, file_get_contents($this->_path->testDirectoryTestDirectoryWithFilesFileOne));
		$this->assertEquals($contentsTwo, file_get_contents($this->_path->testDirectoryTestDirectoryWithFilesFileTwo));
		$this->assertTrue(is_dir($this->_path->testDirectoryTestDirectoryWithFilesDirOne));
		$this->assertEquals($contentsThree, file_get_contents($this->_path->testDirectoryTestDirectoryWithFilesDirOneFileThree));
	}

	public function testMove()
	{
		$contentsOne = file_get_contents($this->_path->testDirectoryWithFilesFileOne);
		$contentsTwo = file_get_contents($this->_path->testDirectoryWithFilesFileTwo);
		$contentsThree = file_get_contents($this->_path->testDirectoryWithFilesDirOneFileThree);

		$dir = Jadva_File::getInstanceFor($this->_url->testDirectoryWithFiles);
		$dir->move(Jadva_File::getInstanceFor($this->_url->testDirectory));

		$this->assertEquals($this->_path->testDirectoryTestDirectoryWithFiles, $dir->getPath());

		$this->assertTrue(is_dir($this->_path->testDirectoryTestDirectoryWithFiles));
		$this->assertEquals($contentsOne, file_get_contents($this->_path->testDirectoryTestDirectoryWithFilesFileOne));
		$this->assertEquals($contentsTwo, file_get_contents($this->_path->testDirectoryTestDirectoryWithFilesFileTwo));
		$this->assertTrue(is_dir($this->_path->testDirectoryTestDirectoryWithFilesDirOne));
		$this->assertEquals($contentsThree, file_get_contents($this->_path->testDirectoryTestDirectoryWithFilesDirOneFileThree));

		$this->assertFalse(is_dir($this->_path->testDirectoryWithFiles));
		$this->assertFalse(file_exists($this->_path->testDirectoryWithFiles));
	}

	public function testRemove()
	{
		$path = $this->_path->testDirectory . 'toDelete' . DIRECTORY_SEPARATOR;
		mkdir($path);

		$dir = Jadva_File::getInstanceFor($path);
		$dir->remove();
		$this->assertFalse(is_dir($path));
		$this->assertFalse(file_exists($path));
	}

	public function testDeltree()
	{
		$dir = Jadva_File::getInstanceFor($this->_url->testDirectoryWithFiles);
		$dir->deltree();
		$this->assertFalse(is_dir($this->_path->testDirectoryWithFiles));
		$this->assertFalse(file_exists($this->_path->testDirectoryWithFiles));
	}
}
//----------------------------------------------------------------------------------------------------------------------
