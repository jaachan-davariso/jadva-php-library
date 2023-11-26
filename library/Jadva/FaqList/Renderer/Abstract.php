<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * JAdVA application
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
 * @package    Jadva_FaqList
 * @subpackage Jadva_FaqList_Renderer
 * @copyright  Copyright (c) 2011 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: FaqList.php 350 2010-06-19 09:55:01Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * Abstract class for rendering FaqLists
 *
 * @category   JAdVA
 * @package    Jadva_FaqList
 * @subpackage Jadva_FaqList_Renderer
 */
abstract class Jadva_FaqList_Renderer_Abstract
{
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @param  Jadva_FaqList                $faq     The FaqList to render
	 * @param  Jadva_File_Directory|string  $outDir  The directory where the rendered files should be put
	 */
	public function __construct(Jadva_FaqList $faq, $outDir)
	{
		$this->_faq = $faq;

		$this->setOutputDirectory($outDir);
	}
	//------------------------------------------------
	/**
	 * Renders the FaqList
	 *
	 * @return  void
	 */
	abstract public function render();
	//------------------------------------------------
	/**
	 * Returns the FaqList we're rendering
	 *
	 * @return  Jadva_FaqList  The FaqList
	 */
	public function getFaq()
	{
		return $this->_faq;
	}
	//------------------------------------------------
	/**
	 * Returns the directory where the rendered files should be put
	 *
	 * @return  Jadva_File_Directory  The directory
	 */
	public function getOutputDirectory()
	{
		return $this->_outputDirectory;
	}
	//------------------------------------------------
	/**
	 * Sets the directory where the rendered files should be put
	 *
	 * @param  Jadva_File_Directory|string  $outDir  The directory
	 *
	 * @return  Jadva_FaqList_Renderer_Abstract  Provides a fluent interface
	 */
	public function setOutputDirectory($outDir)
	{
		$directory = Jadva_File_Directory::getInstanceFor($outDir);
		$directory->ensureExistance();

		$this->_outputDirectory = $directory;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the directory where the resource files should be loaded from, if any
	 *
	 * @return  Jadva_File_Directory  The directory
	 */
	public function getSourceDirectory()
	{
		return $this->_sourceDirectory;
	}
	//------------------------------------------------
	/**
	 * Sets the directory where the resource files should be loaded from
	 *
	 * @param  Jadva_File_Directory|string  $inDir  The directory
	 *
	 * @return  Jadva_FaqList_Renderer_Abstract  Provides a fluent interface
	 */
	public function setSourceDirectory($inDir)
	{
		$this->_sourceDirectory = Jadva_File_Directory::verifyExistance($inDir);

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the name of the stylesheet, if any
	 *
	 * @return  string  The filename within the source directory
	 */
	public function getStylesheet()
	{
		return $this->_stylesheet;
	}
	//------------------------------------------------
	/**
	 * Sets the name of the stylesheet
	 *
	 * @param  string  $stylesheet  The filename within the source directory
	 *
	 * @return  Jadva_FaqList_Renderer_Abstract  Provides a fluent interface
	 */
	public function setStylesheet($stylesheet)
	{
		$this->_stylesheet = (string) $stylesheet;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Contains the FaqList to render
	 *
	 * @var  Jadva_FaqList
	 */
	protected $_fag;
	//------------------------------------------------
	/**
	 * Contains the directory where the rendered files should be put
	 *
	 * @var  Jadva_FaqList
	 */
	protected $_outputDirectory;
	//------------------------------------------------
	/**
	 * Contains the directory where the resource files should be loaded from
	 *
	 * @var  Jadva_FaqList|NULL
	 */
	protected $_sourceDirectory;
	//------------------------------------------------
	/**
	 * Contains the filename of the stylesheet within the source directory
	 *
	 * @var  string|NULL
	 */
	protected $_stylesheet;
	//------------------------------------------------
	/**
	 * Reads the required files to copy from the source directory to the output directory
	 *
	 * @param  string   $html      The HTML to read from
	 * @param  array   &$fileList  The array to store the file list in
	 *
	 * @return  void
	 */
	protected function _readFiles($html, array &$fileList)
	{
		// Images used
		$matchesList = array();
		$matchCount = preg_match_all('/<img[^>]*>/', $html, $matchesList);
		foreach($matchesList[0] as $match) {
			if( empty($match) ) {
				continue;
			}

			if( FALSE === strpos($match, 'src="') ) {
				continue;
			}

			if( preg_match('/class="[^"]*icon/', $match) ) {
				continue;
			}

			list(, $match) = explode('src="', $match);
			list($match) = explode('"', $match);

			if( FALSE !== strpos($match, '://') ) {
				continue;
			}

			$fileList[] = $match;
		}
	}
	//------------------------------------------------
	/**
	 * Copies the needed icons
	 *
	 * @param  array   $iconList        The list of icon names
	 * @param  string  $iconTargetPath  The path to copy the icons from
	 *
	 * @return  void
	 */
	protected function _copyIcons(array $iconList, $iconTargetPath)
	{
		if( 0 == count($iconList) ) {
			return;
		}

		if( NULL === Jadva_FaqList::$iconDirectory ) {
			$this->getFaq()->addWarning('No icon input directory specified, cannot copy icons');
			return;
		}

		$directory = Jadva_File_Directory::getInstanceFor(Jadva_FaqList::$iconDirectory);

		if( !$directory->exists() ) {
			$this->getFaq()->addWarning('Invalid icon input directory ' . $directory->getUrl());
			return;
		}

		foreach($iconList as $icon) {
			$iconFile = $directory->getFile($icon);

			if( !$iconFile->exists() ) {
				$this->getFaq()->addWarning('Invalid icon file: ' . $iconFile->getUrl() . PHP_EOL);
				continue;
			}
			copy($iconFile->getUrl(), $iconTargetPath . $icon);
		}
	}
	//------------------------------------------------
	/**
	 * Copies the given files
	 *
	 * @param  array   $fileList  The list of files to copy
	 * @param  string  $sp        The source directory
	 * @param  string  $tp        The target directory
	 *
	 * @return  void
	 */
	protected function _copyFiles(array $fileList, $sp, $tp)
	{
		$fileList = array_unique($fileList);
		foreach($fileList as $file) {
			$filePath = realpath($sp . $file);
			if( FALSE === $filePath ) {
				$this->getFaq()->addWarning('File "%1$s" does not exist', $sp . $file);
				continue;
			}

			$targetPath = $tp . substr($filePath, strlen($sp));

			$dir = dirname($targetPath);
			$directory = Jadva_File_Directory::getInstanceFor($dir);
			$directory->ensureExistance();

			copy($filePath, $targetPath);
		}
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
