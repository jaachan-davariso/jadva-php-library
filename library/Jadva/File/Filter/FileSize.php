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
 * @copyright  Copyright (c) 2009 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: FileSize.php 254 2009-08-21 11:29:03Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File_Filter_Abstract */
require_once 'Jadva/File/Filter/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Class that filters a list so it only returns files within a certain file size
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File_Filter
 */
class Jadva_File_Filter_FileSize extends Jadva_File_Filter_Abstract
{
	//------------------------------------------------
	/**
	 * Implements Jadva_File_Filter_Interface::filter
	 *
	 * Filters files with min <= size <= max
	 */
	public function filter(Jadva_File_Abstract $file)
	{
		if( $file->isDir() ) {
			return FALSE;
		}

		$filesize = $file->getSize();

		if( $this->hasMin() && ($filesize < $this->getMin()) ) {
			return FALSE;
		}

		if( $this->hasMax() && ($this->getMax() < $filesize) ) {
			return FALSE;
		}

		return TRUE;
	}
	//------------------------------------------------
	/**
	 * Returns the minimum file size
	 *
	 * @pre  $this->hasMin();
	 * @return  integer  The minimum file size
	 */
	public function getMin()
	{
		return $this->_min;
	}
	//------------------------------------------------
	/**
	 * Returns whether files will be filtered for a minum file size
	 *
	 * @return  boolean  TRUE if files will be filtered for a minum file size, FALSE otherwise
	 */
	public function hasMin()
	{
		return NULL !== $this->_min;
	}
	//------------------------------------------------
	/**
	 * Sets the mimimum file size (inclusive)
	 *
	 * @param  integer|NULL  $in_min  The minium file size, NULL for no minium.
	 *
	 * @return  Jadva_File_Filter_FileSize  Provides a fluent interface
	 */
	public function setMin($in_min)
	{
		if( NULL === $in_min ) {
			$this->_min = NULL;
		} else {
			$this->_min = (int) $in_min;
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the maximum file size
	 *
	 * @pre  $this->hasMax();
	 * @return  integer  The maximum file size
	 */
	public function getMax()
	{
		return $this->_max;
	}
	//------------------------------------------------
	/**
	 * Returns whether files will be filtered for a maxum file size
	 *
	 * @return  boolean  TRUE if files will be filtered for a maxum file size, FALSE otherwise
	 */
	public function hasMax()
	{
		return NULL !== $this->_max;
	}
	//------------------------------------------------
	/**
	 * Sets the mimimum file size (exclusive)
	 *
	 * @param  integer|NULL  $in_max  The maxium file size, NULL for no maxium.
	 *
	 * @return  Jadva_File_Filter_FileSize  Provides a fluent interface
	 */
	public function setMax($in_max)
	{
		if( NULL === $in_max ) {
			$this->_max = NULL;
		} else {
			$this->_max = (int) $in_max;
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Contains the minimum file size, if any
	 *
	 * @var  integer|NULL
	 */
	protected $_min = NULL;
	//------------------------------------------------
	/**
	 * Contains the maximum file size, if any
	 *
	 * @var  integer|NULL
	 */
	protected $_max = NULL;
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
