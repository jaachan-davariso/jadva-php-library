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
 * @copyright  Copyright (c) 2009 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Directory.php 253 2009-08-21 11:03:50Z jaachan $
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
	public static function verifyExistance($in_path, $in_flags = self::FLAG_RX)
	{
		$directory = parent::verifyExistance($in_path, $in_flags);

		if( !$directory->isDir() ) {
			/** @see Jadva_File_Exception_NotaDirectory */
			require_once 'Jadva/File/Exception/NotaDirectory.php';
			throw new Jadva_File_Exception_NotaDirectory($directory);
		}

		return $directory;
	}
	//------------------------------------------------
	/**
	 * Makes sure this directory exists
	 *
	 * @param  integer  $in_mode  The mode to create it with if it doesn't exist
	 *
	 * @return  Jadva_File_Directory  Provides a fluent interface
	 */
	public function ensureExistance($in_mode = 0770)
	{
		if( $this->exists() ) {
			return $this;
		}

		$result = @mkdir($this->getPath(), $in_mode, TRUE);
		if( !$result ) {
			/** @see Jadva_File_Exception_CouldNotCreateDirectory */
			require_once 'Jadva/File/Exception/CouldNotCreateDirectory.php';
			throw new Jadva_File_Exception_CouldNotCreateDirectory($this->getPath(), TRUE);
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
	/**
	 * Returns a file from this directory
	 *
	 * @param  string  $in_name  The name of the file
	 *
	 * @throws  Jadva_File_Directory_Exception when the given name belongs to a directory
	 * @return  Jadva_File  The file
	 */
	public function getFile($in_name)
	{
		$name = (string) $in_name;
		$file = Jadva_File_Abstract::getInstanceFor($this->getUrl() . $name);

		if( $file->isDir() ) {
			/** @see Jadva_File_Exception_NotaFile */
			require_once 'Jadva/File/Exception/NotaFile.php';
			throw new Jadva_File_Exception_NotaFile($file);
		}

		return $file;
	}
	//------------------------------------------------
	/**
	 * Returns a sub directory from this directory
	 *
	 * @param  string  $in_name  The name of the sub directory
	 *
	 * @throws  Jadva_File_Directory_Exception when the given name belongs to a file
	 * @return  Jadva_File_Directory  The sub directory
	 */
	public function getSubDirectory($in_name)
	{
		$name = (string) $in_name;
		$dir = Jadva_File_Abstract::getInstanceFor($this->getUrl() . $name . '/');

		if( !$dir->isDir() ) {
			/** @see Jadva_File_Exception_NotaDirectory */
			require_once 'Jadva/File/Exception/NotaDirectory.php';
			throw new Jadva_File_Exception_NotaDirectory($dir);
		}

		return $dir;
	}
	//------------------------------------------------
	/**
	 * Recursively deletes this directory
	 *
	 * @return  boolean  TRUE if the removal was successfull, FALSE otherwise
	 */
	public function deltree()
	{
		foreach($this->getEntries() as $entry) {
			if( $entry->isDir() ) {
				$result = $entry->deltree();
			} else {
				$result = $entry->remove();
			}

			if( !$result ) {
				return FALSE;
			}
		}

		return $this->remove();
	}
	//------------------------------------------------
	/** Implements Jadva_File_Abstract::isDir */
	public function isDir()
	{
		return TRUE;
	}
	//------------------------------------------------
	/** Implements Jadva_File_Abstract::exists */
	public function exists()
	{
		return is_dir($this->_url);
	}
	//------------------------------------------------
	/** Implements Jadva_File_Abstract::isExecutable */
	public function isExecutable()
	{
		if( $this->isSchemeFile() ) {
			//is_executable is not reliable for directories
			return @file_exists($this->_url . '.');
		}

		return parent::isExecutable();
	}
	//------------------------------------------------
	/** Implements Jadva_File_Abstract::copy */
	public function copy($in_directory)
	{
		$directory = Jadva_File_Directory::verifyExistance($in_directory, self::FLAG_WX);

		$targetDirectory = Jadva_File_Directory::getInstanceFor($directory->getPath() . $this->getBaseName() . '/');
		$targetDirectory->ensureExistance(fileperms($this->_path));

		foreach($this->getEntries() as $entry) {
			$result = $entry->copy($targetDirectory);

			if( !$result ) {
				return FALSE;
			}
		}

		return TRUE;
	}
	//------------------------------------------------
	/** Implements Jadva_File_Abstract::move */
	public function move($in_directory)
	{
		$directory = Jadva_File_Directory::verifyExistance($in_directory, self::FLAG_WX);

		$targetDirectory = Jadva_File_Directory::getInstanceFor($directory->getPath() . $this->getBaseName() . '/');
		$targetDirectory->ensureExistance(fileperms($this->_path));

		foreach($this->getEntries() as $entry) {
			$result = $entry->move($targetDirectory);

			if( !$result ) {
				return FALSE;
			}
		}

		$this->remove();

		$this->_path = $targetDirectory->getPath();

		return TRUE;
	}
	//------------------------------------------------
	/**
	 * Implements Jadva_File_Abstract::remove
	 *
	 * @see deltree
	 */
	public function remove()
	{
		return rmdir($this->getPath());
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
