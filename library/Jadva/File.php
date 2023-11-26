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
 * @subpackage Jadva_File
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: File.php 99 2009-03-16 18:32:15Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File_Abstract */
require_once 'Jadva/File/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Class to represent a file on the file system
 *
 * Used for ease of access to the various actions to perform on directories.
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_File extends Jadva_File_Abstract
{
	//------------------------------------------------
	/**
	 * Implements Jadva_File_Abstract::verifyExistance
	 *
	 * @return  Jadva_File  The file on the given path
	 */
	public static function verifyExistance($in_path, $in_flags = 5)
	{
		$file = parent::verifyExistance($in_path, $in_flags);

		if( $file->isDir() ) {
			/** @see Jadva_File_Exception */
			require_once 'Jadva/File/Exception.php';
			throw new Jadva_File_Directory_Exception('The given file ("' . $in_path . '") is a directory.');
		}

		return $file;
	}
	//------------------------------------------------
	/**
	 * Does a basic check to see if this file is an image
	 *
	 * @return  boolean  TRUE if this file is an image, FALSE otherwise
	 */
	public function isImage()
	{
		return FALSE !== @getimagesize($this->_path);
	}
	//------------------------------------------------
	/**
	 * Returns the size of this file
	 *
	 * @return  integer  The size of this file
	 */
	public function getSize()
	{
		return filesize($this->_path);
	}
	//------------------------------------------------
	/**
	 * Returns the extension of this file
	 *
	 * What's returned is the portion of the basename that starts after the last '.'
	 *
	 * @pre  $this->hasExtension()
	 * @return  string  The extension of this file
	 */
	public function getExtension()
	{
		return substr(strrchr($this->getBasename(), '.'), 1);
	}
	//------------------------------------------------
	/**
	 * Returns whether this file has an extension
	 *
	 * @return  boolean  TRUE if this file has an extension, FALSE otherwise
	 */
	public function hasExtension()
	{
		return FALSE !== strpos($this->getBasename(), '.');
	}
	//------------------------------------------------
	/** Implements Jadva_File_Abstract::isDir */
	public function isDir()
	{
		return FALSE;
	}
	//------------------------------------------------
	/** Implements Jadva_File::exists */
	public function exists()
	{
		return file_exists($this->_path);
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
