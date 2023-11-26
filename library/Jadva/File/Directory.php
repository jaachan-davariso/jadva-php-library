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
 * @subpackage Jadva_File_Directory
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Directory.php 99 2009-03-16 18:32:15Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File */
require_once 'Jadva/File.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Class to represent a directory on the file system
 *
 * Used for ease of access to the various actions to perform on directories.
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File_Directory
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_File_Directory extends Jadva_File_Abstract implements IteratorAggregate
{
	//------------------------------------------------
	/**
	 * Verifies whether a directory exists, and throws an exception if it doesn't
	 *
	 * @param  string   $in_path   The path to verifiy
	 * @param  integer  $in_flags  Which properties of the directory to test
	 *
	 * @return  Jadva_File_Directory  The directory on the given path
	 */
	public static function verifyExistance($in_path, $in_flags = 5)
	{
		$directory = parent::verifyExistance($in_path, $in_flags);

		if( !$directory->isDir() ) {
			/** @see Jadva_File_Directory_Exception */
			require_once 'Jadva/File/Directory/Exception.php';
			throw new Jadva_File_Directory_Exception('The given directory ("' . $in_path . '") is not a directory.');
		}

		return $directory;
	}
	//------------------------------------------------
	/**
	 * Makes sure this directory exists
	 *
	 * @param  integer  $in_mode  The mode to create it with if it doesn't exist
	 *
	 * @todo Perhaps speed up by using recursive parameter of mkdir?
	 * @return  Jadva_File_Directory  Provides a fluent interface
	 */
	public function ensureExistance($in_mode = 0770)
	{
		if( $this->exists() ) {
			return $this;
		}

		$parent = $this->getParent();
		$parent->ensureExistance();

		$result = @mkdir($this->getPath(), $in_mode);
		if( !$result ) {
			/** @see Jadva_File_Directory_Exception */
			require_once 'Jadva/File/Directory/Exception.php';
			throw new Jadva_File_Directory_Exception('Could not create directory "' . $this->getBasename() . '" in directory "' . $parent->getPath() . '".');
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Filters the entries in this directory with the given filter
	 *
	 * Note that . and .. are never returned
	 *
	 * @param  Jadva_File_Filter_Interface|string|NULL  $in_filter   (OPTIONAL) The filter to apply
	 * @param  array|NULL                               $in_options  (OPTIONAL) Options to pass to the filter
	 *
	 * @return  Jadva_File_Directory_Iterator  The files and/or directories that match the filter
	 */
	public function filter($in_filter = NULL, $in_options = NULL)
	{
		/** @see Jadva_File_Directory_Iterator */
		require_once 'Jadva/File/Directory/Iterator.php';

		return new Jadva_File_Directory_Iterator($this, $in_filter, $in_options);
	}
	//------------------------------------------------
	/**
	 * Returns all entries (except . or ..)
	 *
	 * @return  Jadva_File_Directory_Iterator  The files and/or directories in this directory
	 */
	public function getEntries()
	{
		return $this->filter();
	}
	//------------------------------------------------
	/** Implements Jadva_File_Abstract::isDir */
	public function isDir()
	{
		return TRUE;
	}
	//------------------------------------------------
	/** Implements Jadva_File::exists */
	public function exists()
	{
		return is_dir($this->_path);
	}
	//------------------------------------------------
	/** Implements Jadva_File::isExecutable */
	public function isExecutable()
	{
		//is_executable is not reliable for directories
		return @file_exists($this->_path . '.');
	}
	//------------------------------------------------
	/** Implements IteratorAggregate::getIterator */
	public function getIterator()
	{
		return $this->getEntries();
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
