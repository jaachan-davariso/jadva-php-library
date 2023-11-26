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
 * @version    $Id: FilterTest.php 254 2009-08-21 11:29:03Z jaachan $
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
/** @see Jadva_File_Filter_Extension */
require_once 'Jadva/File/Filter/Extension.php';
/** @see Jadva_File_Filter_Regex */
require_once 'Jadva/File/Filter/Regex.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class tests the Jadva_File_Filter_* classes.
 *
 * @category   JAdVA
 * @package    Jadva
 * @subpackage UnitTests
 */
class Jadva_File_FilterTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->_path = new stdClass;

		$this->_path->FILE__ = __FILE__;
		$this->_path->DIR__  = dirname(__FILE__) . DIRECTORY_SEPARATOR;

		$this->_path->testDirectory = $this->_path->DIR__ . 'testDirectory' . DIRECTORY_SEPARATOR;

		$this->_path->Jadva_File  = $this->_path->DIR__;
		$this->_path->testFile    = $this->_path->Jadva_File . 'testFile.txt';
		$this->_path->testImage   = $this->_path->Jadva_File . 'testImage.gif';
	}

	public function tearDown()
	{
		$i = 0;
		$path = $this->_path->testDirectory . 'existingFile' . $i;
		while(file_exists($path)) {
			unlink($path);

			$i++;
			$path = $this->_path->testDirectory . 'existingFile' . $i;
		}
	}

	public function testDirectories()
	{
		/** @see Jadva_File_Filter_Directories */
		require_once 'Jadva/File/Filter/Directories.php';

		$filter = new Jadva_File_Filter_Directories;

		$this->assertTrue($filter->filter(Jadva_File::getInstanceFor(dirname(__FILE__))));
		$this->assertFalse($filter->filter(Jadva_File::getInstanceFor(__FILE__)));
	}

	public function dataTestExtension()
	{
		return array(
			array(
				array('txt'),
				Jadva_File_Filter_Extension::TYPE_ALLOW,
				array(
					'extension.txt'  => TRUE,
					'extension.ext'  => FALSE,
					'extension.text' => FALSE,
					'extension.txe'  => FALSE,
					'extension'      => FALSE,
					'.txt'           => TRUE,
					'directory' . DIRECTORY_SEPARATOR => FALSE,
				),
			),
			array(
				array('txt'),
				Jadva_File_Filter_Extension::TYPE_DENY,
				array(
					'extension.txt'  => FALSE,
					'extension.ext'  => TRUE,
					'extension.text' => TRUE,
					'extension.txe'  => TRUE,
					'extension'      => TRUE,
					'.txt'           => FALSE,
					'directory' . DIRECTORY_SEPARATOR => FALSE,
				),
			),
			array(
				array('jpg', 'gif', 'png'),
				TRUE,
				array(
					'test.jpg' => TRUE,
					'test.gif' => TRUE,
					'test.png' => TRUE,
					'test.bmp' => FALSE,
				),
			),
		);
	}

	/**
	 * @dataProvider  dataTestExtension
	 */
	public function testExtension(array $extensions, $type, array $list)
	{
		$filter = new Jadva_File_Filter_Extension;

		$filter->setExtensions($extensions);
		$filter->setFilterType($type);

		foreach($list as $filename => $result) {
			$this->assertEquals(
				$result,
				$filter->filter(Jadva_File::getInstanceFor($this->_path->testDirectory . $filename))
			);
		}
	}

	public function testFileSize()
	{
		/** @see Jadva_File_Filter_FileSize */
		require_once 'Jadva/File/Filter/FileSize.php';

		$filter = new Jadva_File_Filter_FileSize(array(
			'min' => 4,
			'max' => 16,
		));

		$path = $this->_getExistingFilePath();
		$instance = Jadva_File::getInstanceFor($path);

		file_put_contents($path, 'xx');
		$this->assertFalse($filter->filter($instance));

		file_put_contents($path, 'xxx');
		$this->assertFalse($filter->filter($instance));
		file_put_contents($path, 'xxxx');
		$this->assertTrue($filter->filter($instance));
		file_put_contents($path, 'xxxxx');
		$this->assertTrue($filter->filter($instance));

		file_put_contents($path, 'xxxxxxxxxx');
		$this->assertTrue($filter->filter($instance));

		file_put_contents($path, 'xxxxxxxxxxxxxxx');
		$this->assertTrue($filter->filter($instance));
		file_put_contents($path, 'xxxxxxxxxxxxxxxx');
		$this->assertTrue($filter->filter($instance));
		file_put_contents($path, 'xxxxxxxxxxxxxxxxx');
		$this->assertFalse($filter->filter($instance));

		file_put_contents($path, 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
		$this->assertFalse($filter->filter($instance));

		$filter->setMin(NULL);
		file_put_contents($path, 'xx');
		$this->assertTrue($filter->filter($instance));

		$filter->setMin(4);
		$filter->setMax(NULL);
		file_put_contents($path, 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
		$this->assertTrue($filter->filter($instance));

		$filter->setMin(NULL);
		file_put_contents($path, 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
		$this->assertTrue($filter->filter($instance));

		$this->assertFalse($filter->filter(Jadva_File::getInstanceFor(dirname(__FILE__))));
	}

	public function testImages()
	{
		/** @see Jadva_File_Filter_Images */
		require_once 'Jadva/File/Filter/Images.php';

		$filter = new Jadva_File_Filter_Images;

		$this->assertFalse($filter->filter(Jadva_File::getInstanceFor($this->_path->testFile)));
		$this->assertTrue($filter->filter(Jadva_File::getInstanceFor($this->_path->testImage)));
		$this->assertFalse($filter->filter(Jadva_File::getInstanceFor(dirname(__FILE__))));
	}

	/**
	 * @expectedException  Jadva_File_Filter_Exception
	 */
	public function testRegexNoPattern()
	{
		$filter = new Jadva_File_Filter_Regex;
		$filter->filter(Jadva_File::getInstanceFor($this->_getExistingFilePath()));
	}

	/**
	 * @expectedException  Jadva_File_Filter_Exception
	 */
	public function testRegexInvalidPattern()
	{
		$filter = new Jadva_File_Filter_Regex;
		$filter->setPattern('test');
		$filter->filter(Jadva_File::getInstanceFor($this->_getExistingFilePath()));
	}

	public function testRegex()
	{
		$filter = new Jadva_File_Filter_Regex;
		$filter->setPattern('/a.*b/');
		$this->assertTrue($filter->filter(Jadva_File::getInstanceFor($this->_path->testDirectory . 'ab')));
		$this->assertTrue($filter->filter(Jadva_File::getInstanceFor($this->_path->testDirectory . 'axb')));
		$this->assertFalse($filter->filter(Jadva_File::getInstanceFor($this->_path->testDirectory . 'ax')));
		$this->assertFalse($filter->filter(Jadva_File::getInstanceFor($this->_path->testDirectory . 'x')));
		$this->assertTrue($filter->filter(Jadva_File::getInstanceFor($this->_path->testDirectory . 'ax.xb')));
		$this->assertTrue($filter->filter(Jadva_File::getInstanceFor($this->_path->testDirectory . 'ax.xbx')));
	}

	public function testFiles()
	{
		/** @see Jadva_File_Filter_Files */
		require_once 'Jadva/File/Filter/Files.php';

		$filter = new Jadva_File_Filter_Files;

		$this->assertFalse($filter->filter(Jadva_File::getInstanceFor(dirname(__FILE__))));
		$this->assertTrue($filter->filter(Jadva_File::getInstanceFor(__FILE__)));
	}

	protected function _getExistingFilePath()
	{
		static $i = NULL;
		if( NULL === $i ) {
			$i = 0;
		} else {
			$i++;
		}

		return $this->_path->testDirectory . 'existingFile' . $i;
	}
}
//----------------------------------------------------------------------------------------------------------------------
