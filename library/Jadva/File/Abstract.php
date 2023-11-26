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
 * @copyright  Copyright (c) 2009 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Abstract.php 312 2010-01-13 11:31:10Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * Abstract class to represent a file or directory on the file system
 *
 * Functionality in this class has only been tested on the file system and on vfsStream. Working with relative path is
 * not supported/tested, safe for the realpath function. Also not tested are SMB server or the sort.
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File_Abstract
 */
abstract class Jadva_File_Abstract
{
	//------------------------------------------------
	/** The file/directory is/must be readable */
	const FLAG_IS_READABLE   = 4;
	/** The file/directory is/must be writable */
	const FLAG_IS_WRITABLE   = 2;
	/** The file/directory is/must be executable */
	const FLAG_IS_EXECUTABLE = 1;
	//------------------------------------------------
	/** The file/directory is/must be readable */
	const FLAG_R = 4;
	/** The file/directory is/must be writable */
	const FLAG_W = 2;
	/** The file/directory is/must be executable */
	const FLAG_X = 1;
	/** The file/directory is/must be readable and writable*/
	const FLAG_RW = 6;
	/** The file/directory is/must be readable and executable*/
	const FLAG_RX = 5;
	/** The file/directory is/must be writable and executable*/
	const FLAG_WX = 3;
	/** The file/directory is/must be readable, writable and executable*/
	const FLAG_RWX = 7;
	//------------------------------------------------
	/** The scheme for files on the local file system */
	const SCHEME_FILE = 'file';
	//------------------------------------------------
	/** The scheme for files over the Hypertext Transfer Protocol (HTTP) */
	const SCHEME_HTTP = 'http';
	//------------------------------------------------
	//
	// Static helper functions
	//
	//------------------------------------------------
	/**
	 * Returns whether the current file system is a linux style file system
	 *
	 * @return  boolean  TRUE if the current file system is a linux style file system, FALSE otherwise
	 */
	public static function fileSchemeIsLinux()
	{
		static $result = NULL;

		if( NULL === $result ) {
			$result = '/' == substr(__FILE__, 0, 1);
		}

		return $result;
	}
	//------------------------------------------------
	/**
	 * Returns an instance for the given path, existing or not
	 *
	 * @param  Jadva_File_Abstract|string   $in_path   The path to get an instance for
	 *
	 * @return  Jadva_File_Abstract  The file or directory on the given path
	 */
	public static function getInstanceFor($in_path)
	{
		if( $in_path instanceof self ) {
			return $in_path;
		}

		if( empty($in_path) ) {
			/** @see Jadva_File_Exception */
			require_once 'Jadva/File/Exception.php';
			throw new Jadva_File_Exception('Empty path passed');
		}

		$path = self::cleanPath($in_path);

		if( is_dir($path) ) {
			$className = 'Jadva_File_Directory';
			if( ':///' !== substr($path, -4) ) {
				$path = rtrim($path, '/') . '/';
			}
		} elseif( file_exists($path) ) {
			$className = 'Jadva_File';
			$path = rtrim($path, '/');
		} else {
			//No existing file or directory, guess which one it should be base on the convention
			// that directories are always passed with a / at the end
			if( '/' == substr($path, -1) ) {
				$className = 'Jadva_File_Directory';
			} else {
				$className = 'Jadva_File';
			}
		}

		if( !class_exists($className, FALSE) ) {
			require_once str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		}

		return new $className($path);
	}
	//------------------------------------------------
	/**
	 * Returns an instance for the real path
	 *
	 * @param  string  The path
	 *
	 * @return  Jadva_File_Abstract  The file or directory on the given path
	 */
	public static function realpath($in_path)
	{
		return self::getInstanceFor(realpath($in_path));
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
	public static function verifyExistance($in_path, $in_flags = self::FLAG_R)
	{
		$file = self::getInstanceFor($in_path);

		if( !$file->exists() ) {
			/** @see Jadva_File_Exception_NotExists */
			require_once 'Jadva/File/Exception/NotExists.php';
			throw new Jadva_File_Exception_NotExists($in_path);
		}

		$flags = (int) $in_flags;

		if( ($flags & self::FLAG_IS_READABLE) && !$file->isReadable() ) {
			/** @see Jadva_File_Exception_NotReadable */
			require_once 'Jadva/File/Exception/NotReadable.php';
			throw new Jadva_File_Exception_NotReadable($in_path);
		}

		if( ($flags & self::FLAG_IS_WRITABLE) && !$file->isWritable() ) {
			/** @see Jadva_File_Exception_NotWritable */
			require_once 'Jadva/File/Exception/NotWritable.php';
			throw new Jadva_File_Exception_NotWritable($in_path);
		}

		if( ($flags & self::FLAG_IS_EXECUTABLE) && !$file->isExecutable() ) {
			/** @see Jadva_File_Exception_NotExecutable */
			require_once 'Jadva/File/Exception/NotExecutable.php';
			throw new Jadva_File_Exception_NotExecutable($in_path);
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

		//Find a scheme, if it exists. Assume file by default.
		if( FALSE !== strpos($path, '://') ) {
			list($scheme, $path) = explode('://', $path, 2);
		} else {
			$scheme = self::SCHEME_FILE;
		}

		//Clean up the directory separators
		$path = str_replace('\\', '/', $path);

		//Find out whether the path is supposed to appoint a directory
		$pathIsDir = '/' == substr($path, -1);

		//Find out whether the path is supposed to begin with a slash
		$startIsSlash = (self::SCHEME_FILE == $scheme) && self::fileSchemeIsLinux();

		//Clean up on all the slashes
		$path = trim($path, '/');
		$path = preg_replace('#/+#', '/', $path);

		//Restore whether the path is supposed to appoint a directory
		if( $pathIsDir ) {
			$path .= '/';
		}

		//Restore whether the path is supposed to begin with a slash
		if( $startIsSlash ) {
			$path = '/' . $path;
		}

		//Restore the scheme
		$path = $scheme . '://' . $path;

		//Remove directories that don't count
		$path = str_replace('/./', '/', $path);
		$path = preg_replace('#/[^/]*/\.\./#', '/', $path);

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
	 * Will return a localised path for file system files (i.e. D:\text.txt instead of file://D:/test.txt). Does not
	 * include the scheme.
	 *
	 * @param  string  The path
	 */
	public function getPath()
	{
		$path = $this->_path;

		if( $this->isSchemeFile() && ('/' != DIRECTORY_SEPARATOR) ) {
			$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		}

		return $path;
	}
	//------------------------------------------------
	/**
	 * Returns the scheme
	 *
	 * @param  string  The scheme
	 */
	public function getScheme()
	{
		return $this->_scheme;
	}
	//------------------------------------------------
	/**
	 * Returns whehther the scheme is file
	 *
	 * i.e. whether it's a file or directory on the local file system
	 *
	 * @param  boolean  TRUE if the scheme is file, FALSE otherwise
	 */
	public function isSchemeFile()
	{
		return self::SCHEME_FILE === $this->_scheme;
	}
	//------------------------------------------------
	/**
	 * Returns the URL
	 *
	 * The URL being basically just the path, but including the scheme and using the URL separators
	 *
	 * @param  string  The url
	 */
	public function getUrl()
	{
		return $this->_url;
	}
	//------------------------------------------------
	/**
	 * Returns the basename
	 *
	 * @param  string  The basename
	 */
	public function getBasename()
	{
		return basename($this->_url);
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
	 * Copies this file or directory into the given directory
	 *
	 * The target will be overwritten.
	 *
	 * @param  Jadva_File_Directory|string  The directory to move this file to
	 *
	 * @return  boolean  TRUE if the copy was successfull, FALSE otherwise
	 */
	abstract public function copy($in_directory);
	//------------------------------------------------
	/**
	 * Moves this file or directory into the given directory
	 *
	 * The target will be overwritten.
	 *
	 * Afterwards, the path of this file or directory is updated to reflect the new location
	 *
	 * @param  Jadva_File_Directory|string  The directory to move this file to
	 *
	 * @return  boolean  TRUE if the move was successfull, FALSE otherwise
	 */
	abstract public function move($in_directory);
	//------------------------------------------------
	/**
	 * Removes this file or directory
	 *
	 * @return  boolean  TRUE if the removal was successfull, FALSE otherwise
	 */
	abstract public function remove();
	//------------------------------------------------
	/**
	 * Returns whether this file is readable
	 *
	 * @return  boolean  TRUE if this file is readable
	 */
	public function isReadable()
	{
		return is_readable($this->_url);
	}
	//------------------------------------------------
	/**
	 * Returns whether this file is writable
	 *
	 * @return  boolean  TRUE if this file is writable
	 */
	public function isWritable()
	{
		return is_writable($this->_url);
	}
	//------------------------------------------------
	/**
	 * Returns whether this file is executable
	 *
	 * @return  boolean  TRUE if this file is executable
	 */
	public function isExecutable()
	{
		return is_executable($this->_url);
	}
	//------------------------------------------------
	/**
	 * Returns the parent directory
	 *
	 * @return  Jadva_File_Directory  The parent directory
	 */
	public function getParent()
	{
		return self::getInstanceFor(dirname($this->_url) . '/');
	}
	//------------------------------------------------
	/**
	 * Returns the URL of the parent directory
	 *
	 * @return  string  The url of the parent directory
	 */
	public function getParentUrl()
	{
		return $this->_scheme . '://' . dirname($this->_path) . '/';
	}
	//------------------------------------------------
	/**
	 * Returns the path of the parent directory
	 *
	 * Will return a localized path for file system files (i.e. D:\text.txt instead of file://D:/test.txt).
	 *
	 * @return  string  The path of the parent directory
	 */
	public function getParentPath()
	{
		$path = dirname($this->_path) . '/';

		if( $this->isSchemeFile() ) {
			if( '/' != DIRECTORY_SEPARATOR ) {
				$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
			}
		}

		return $path;
	}
	//------------------------------------------------
	/**
	 * Contains the full URL
	 *
	 * @var  string
	 */
	protected $_url;
	//------------------------------------------------
	/**
	 * Contains the URL scheme
	 *
	 * @var  string
	 */
	protected $_scheme;
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
		$this->_url = $path;

		if( FALSE === strpos($path, '://') ) {
			/** @see Jadva_File_Exception */
			require_once 'Jadva/File/Exception.php';
			throw new Jadva_File_Exception(sprintf('Internal error: Invalid path passed ("%1$s")', $path));
		}

		list($this->_scheme, $this->_path) = explode('://', $path);
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
