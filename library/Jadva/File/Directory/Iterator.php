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
 * @subpackage Jadva_File_Directory_Iterator
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Iterator.php 99 2009-03-16 18:32:15Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File_Directory */
require_once 'Jadva/File/Directory.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Class to iterate over the files in a directory
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File_Directory_Iterator
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_File_Directory_Iterator implements SeekableIterator, Countable
{
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * Note that the options are only passed when the filter is an instance of Jadva_File_Filter_Abstract. If
	 * necessary, this could be increased to all classes which have a setOptions(array) function, but that'd probably
	 * be slow to verify.
	 *
	 * @param  Jadva_File_Directory                     $directory   The directory to iterate over
	 * @param  Jadva_File_Filter_Interface|string|NULL  $in_filter   (OPTIONAL) The filter to apply
	 * @param  array|NULL                               $in_options  (OPTIONAL) Options to pass to the filter
	 */
	public function __construct(Jadva_File_Directory $directory, $in_filter = NULL, $in_options = NULL)
	{
		$this->_directory = $directory;

		if( NULL === $in_filter ) {
			$this->_filter = NULL;
		} elseif( $in_filter instanceof Jadva_File_Filter_Interface ) {
			$this->_filter = $in_filter;
		} else {
			/** @see Zend_Loader */
			require_once 'Zend/Loader.php';

			$filterName = (string) $in_filter;

			$className = 'Jadva_File_Filter_' . $filterName;

			Zend_Loader::loadClass($className);
			$filter = new $className;

			if( !($filter instanceof Jadva_File_Filter_Interface) ) {
				/** @see Jadva_File_Directory_Iterator_Exception */
				require_once 'Jadva/File/Directory/Iterator/Exception.php';
				throw new Jadva_File_Directory_Iterator_Exception('Filter must implement Jadva_File_Filter_Interface');
			}

			if( ($filter instanceof Jadva_File_Filter_Abstract) && is_array($in_options) ) {
				$filter->setOptions($in_options);
			}

			$this->_filter = $filter;
		}

		$this->_initialiseList();
		$this->rewind();
	}
	//------------------------------------------------
	/**
	 * Returns the directory we're iterating over
	 *
	 * @return  Jadva_File_Directory  The directory we're iterating over
	 */
	public function getDirectory()
	{
		return $this->_directory;
	}
	//------------------------------------------------
	/**
	 * Returns the filter we're applying
	 *
	 * @pre  $this->hasFilter()
	 * @return  Jadva_File_Filter_Interface  The filter
	 */
	public function getFilter()
	{
		return $this->_filter;
	}
	//------------------------------------------------
	/**
	 * Returns whether we're applying a filter
	 *
	 * @return  TRUE if we're applying a filter, FALSE otherwise
	 */
	public function hasFilter()
	{
		return NULL !== $this->_filter;
	}
	//------------------------------------------------
	/** Implements Iterator::current */
	public function current()
	{
		return $this->_list[$this->key()];
	}
	//------------------------------------------------
	/** Implements Iterator::key */
	public function key()
	{
		return $this->_listKey;
	}
	//------------------------------------------------
	/** Implements Iterator::next */
	public function next()
	{
		$this->_listKey++;
	}
	//------------------------------------------------
	/** Implements Iterator::rewind */
	public function rewind()
	{
		$this->_listKey = 0;
	}
	//------------------------------------------------
	/** Implements Iterator::valid */
	public function valid()
	{
		return $this->key() < $this->count();
	}
	//------------------------------------------------
	/** Implements SeekableIterator::seek */
	public function seek($in_key)
	{
		$key = (int) $key;

		if( ($key < 0) || ($this->count() <= $key) ) {
			throw new OutOfBoundsException('Invalid seek position "' . $in_key . '"');
		}

		$this->_listKey = (int) $in_key;
	}
	//------------------------------------------------
	/** Implements Countable::count */
	public function count()
	{
		return $this->_listCount;
	}
	//------------------------------------------------
	/**
	 * Contains the directory we're iterating over
	 *
	 * @var  Jadva_File_Directory
	 */
	protected $_directory = NULL;
	//------------------------------------------------
	/**
	 * Contains the filter we're applying, if any
	 *
	 * @var  Jadva_File_Filter_Interface|NULL
	 */
	protected $_filter    = NULL;
	//------------------------------------------------
	/**
	 * Contains the index of the current file
	 *
	 * @var  integer
	 */
	protected $_listKey   = 0;
	//------------------------------------------------
	/**
	 * Contains the amount of files
	 *
	 * @var  integer
	 */
	protected $_listCount = 0;
	//------------------------------------------------
	/**
	 * Contains the list of files
	 *
	 * @var  array
	 */
	protected $_list      = array();
	//------------------------------------------------
	/**
	 * Loads the list of files from the directory
	 *
	 * @return  void
	 */
	protected function _initialiseList()
	{
		if( $this->hasFilter() ) {
			$filter = $this->getFilter();
		} else {
			$filter = NULL;
		}

		$iterator = new DirectoryIterator($this->_directory->getPath());
		foreach($iterator as $iteratorEntry) {
			if( $iteratorEntry->isDot() ) {
				continue;
			}

			$path = $iteratorEntry->getPath() . DIRECTORY_SEPARATOR . $iteratorEntry->getFileName();
			if( $iteratorEntry->isDir() ) {
				$path .= DIRECTORY_SEPARATOR;
			}

			$listEntry = Jadva_File::getInstanceFor($path);

			if( (NULL !== $filter) && !$filter->filter($listEntry) ) {
				continue;
			}

			$this->_list[] = $listEntry;
			$this->_listCount++;
		}
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
