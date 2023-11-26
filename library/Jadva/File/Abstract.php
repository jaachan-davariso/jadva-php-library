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
 * @subpackage Jadva_File_Abstract
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Abstract.php 99 2009-03-16 18:32:15Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * Abstract class to represent a file or directory on the file system
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File_Abstract
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
abstract class Jadva_File_Abstract
{
	//------------------------------------------------
	/** The file/directory is/must be readable */
	const FLAG_IS_READABLE   = 1;
	/** The file/directory is/must be writable */
	const FLAG_IS_WRITABLE   = 2;
	/** The file/directory is/must be executable */
	const FLAG_IS_EXECUTABLE = 4;
	//------------------------------------------------
	//
	// Static helper functions
	//
	//------------------------------------------------
	/**
	 * Returns an instance for the given path, existing or not
	 *
	 * @param  string   $in_path   The path to get an instance for
	 *
	 * @return  Jadva_File_Abstract  The file or directory on the given path
	 */
	public static function getInstanceFor($in_path)
	{
		$path = self::cleanPath($in_path);

		if( is_dir($path) ) {
			$className = 'Jadva_File_Directory';
			$path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		} elseif( file_exists($path) ) {
			$className = 'Jadva_File';
			$path = rtrim($path, DIRECTORY_SEPARATOR);
		} else {
			//No existing file or directory, guess which one it should be base on the convention
			// that directories are always passed with a DIRECTORY_SEPARATOR at the end
			if( DIRECTORY_SEPARATOR == substr($path, -1) ) {
				$className = 'Jadva_File_Directory';
			} else {
				$className = 'Jadva_File';
			}
		}

		return new $className($path);
	}
	//------------------------------------------------
	/**
	 * Verifies whether a file exists, and throws an exception if it doesn't
	 *
	 * @param  string   $in_path   The path to verifiy
	 * @param  integer  $in_flags  Which properties of the file to test
	 *
	 * @return  Jadva_File  The file on the given path
	 */
	public static function verifyExistance($in_path, $in_flags = 5)
	{
		$file = self::getInstanceFor($in_path);

		if( !$file->exists() ) {
			/** @see Jadva_File_Directory_Exception */
			require_once 'Jadva/File/Directory/Exception.php';
			throw new Jadva_File_Directory_Exception('The given directory ("' . $in_path . '") does not exist.');
		}

		$flags = (int) $in_flags;

		if( ($flags & self::FLAG_IS_READABLE) && !$file->isReadable() ) {
			/** @see Jadva_File_Directory_Exception */
			require_once 'Jadva/File/Directory/Exception.php';
			throw new Jadva_File_Directory_Exception('The given directory ("' . $in_path . '") is not readable.');
		}

		if( ($flags & self::FLAG_IS_WRITABLE) && !$file->isWritable() ) {
			/** @see Jadva_File_Directory_Exception */
			require_once 'Jadva/File/Directory/Exception.php';
			throw new Jadva_File_Directory_Exception('The given directory ("' . $in_path . '") is not writable.');
		}

		if( ($flags & self::FLAG_IS_EXECUTABLE) && !$file->isExecutable() ) {
			/** @see Jadva_File_Directory_Exception */
			require_once 'Jadva/File/Directory/Exception.php';
			throw new Jadva_File_Directory_Exception('The given directory ("' . $in_path . '") is not executable.');
		}

		return $file;
	}
	//------------------------------------------------
	/**
	 * Cleans up a path
	 *
	 * @param  string   $in_path   The path to clean up
	 *
	 * @return  string  The cleaned up path
	 */
	public static function cleanPath($in_path)
	{
		$path = (string) $in_path;

		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);

		return $path;
	}
	//------------------------------------------------
	//
	// Querying functions
	//
	//------------------------------------------------
	/**
	 * Returns the path
	 *
	 * @param  string  The path
	 */
	public function getPath()
	{
		return $this->_path;
	}
	//------------------------------------------------
	/**
	 * Returns the basename
	 *
	 * @param  string  The basename
	 */
	public function getBasename()
	{
		return basename($this->_path);
	}
	//------------------------------------------------
	/**
	 * Returns whether this class represents a directory
	 *
	 * Note that directories have a class of their own, see {@link Jadva_File_Directory}.
	 *
	 * @return  boolean  TRUE if this class represents a directory, or FALSE otherwise
	 */
	abstract public function isDir();
	//------------------------------------------------
	/**
	 * Returns whether this file exists
	 *
	 * @return  boolean  TRUE if this file exists
	 */
	abstract public function exists();
	//------------------------------------------------
	/**
	 * Returns whether this file is readable
	 *
	 * @return  boolean  TRUE if this file is readable
	 */
	public function isReadable()
	{
		return is_readable($this->_path);
	}
	//------------------------------------------------
	/**
	 * Returns whether this file is writable
	 *
	 * @return  boolean  TRUE if this file is writable
	 */
	public function isWritable()
	{
		return is_writable($this->_path);
	}
	//------------------------------------------------
	/**
	 * Returns whether this file is executable
	 *
	 * @return  boolean  TRUE if this file is executable
	 */
	public function isExecutable()
	{
		return is_executable($this->_path);
	}
	//------------------------------------------------
	/**
	 * Returns the parent directory
	 *
	 * @return  Jadva_File_Directory  The parent directory
	 */
	public function getParent()
	{
		return self::getInstanceFor($this->getParentPath());
	}
	//------------------------------------------------
	/**
	 * Returns the path of the parent directory
	 *
	 * @return  string  The path of the parent directory
	 */
	public function getParentPath()
	{
		return dirname($this->_path) . DIRECTORY_SEPARATOR;
	}
	//------------------------------------------------
	/**
	 * Contains the path
	 *
	 * @var  string
	 */
	protected $_path;
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @param  string  $path  The path of the file
	 *
	 * @see getInstanceFor
	 */
	protected function __construct($path)
	{
		$this->_path = $path;
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
