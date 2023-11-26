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
 * @package    Jadva_File
 * @subpackage Jadva_File_Filter
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Extension.php 99 2009-03-16 18:32:15Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File_Filter_Abstract */
require_once 'Jadva/File/Filter/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Class that filters a list so it only returns files with a given extension
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File_Filter
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_File_Filter_Extension extends Jadva_File_Filter_Abstract
{
	//------------------------------------------------
	/** Keep the files with the given extensions */
	const TYPE_ALLOW = 'allow';
	/** Filter out the files with the given extensions */
	const TYPE_DENY = 'deny';
	//------------------------------------------------
	/**
	 * Adds an extension to the list
	 *
	 * Note that you should /not/ add the '.' to the extension.
	 *
	 * @param  string  $in_extension  The extension to add
	 *
	 * @return  Jadva_File_Filter_Extension  Provides a fluent interface
	 */
	public function addExtension($in_extension)
	{
		$extension = (string) $in_extension;
		$this->_extensionList[$extension] = TRUE;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Sets the list of extensions
	 *
	 * @param  array  $extensions  The extensions to set
	 *
	 * @return  Jadva_File_Filter_Extension  Provides a fluent interface
	 */
	public function setExtensions(array $extensions)
	{
		$this->_extensionList = array();
		foreach($extensions as $extension) {
			$this->addExtension($extension);
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Sets the filter type
	 *
	 * Set either using self::TYPE_ALLOW, self::TYPE_DENY, or by a boolean, TRUE allowing files and FALSE denying
	 * them.
	 *
	 * @param  string|boolean  $in_filterType  The filter type
	 */
	public function setFilterType($in_filterType)
	{
		if( $in_filterType === self::TYPE_ALLOW ) {
			$this->_allow = TRUE;
		} elseif( $in_filterType === self::TYPE_DENY ) {
			$this->_allow = FALSE;
		} else {
			$this->_allow = (boolean) $in_filterType;
		}

		return $this;
	}
	//------------------------------------------------
	/** Implements Jadva_File_Filter_Interface::filter */
	public function filter(Jadva_File_Abstract $file)
	{
		if( $file->isDir() ) {
			return FALSE;
		}

		if( !$file->hasExtension() ) {
			return FALSE;
		}

		$extension = $file->getExtension();

		if( array_key_exists($extension, $this->_extensionList) ) {
			return $this->_allow;
		}

		return !$this->_allow;
	}
	//------------------------------------------------
	/**
	 * Contains the list of extensions
	 *
	 * @var  array
	 */
	protected $_extensionList = array();
	//------------------------------------------------
	/**
	 * Contains whether to allow or deny files with the given extensions
	 *
	 * @var  boolean
	 */
	protected $_allow = TRUE;
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
