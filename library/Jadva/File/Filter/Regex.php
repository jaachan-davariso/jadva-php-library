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
 * @version    $Id: Abstract.php 99 2009-03-16 18:32:15Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File_Filter_Abstract */
require_once 'Jadva/File/Filter/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Filters files based on a regular expression matched to their base name
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File_Filter
 */
class Jadva_File_Filter_Regex extends Jadva_File_Filter_Abstract
{
	//------------------------------------------------
	/**
	 * Returns the pattern
	 *
	 * @pre  $this->hasPattern()
	 * @return  string  The regular expression pattern
	 */
	public function getPattern()
	{
		return $this->_pattern;
	}
	//------------------------------------------------
	/**
	 * Returns whether a pattern has been set
	 *
	 * @return  boolean  TRUE if a pattern has been set, FALSE otherwise
	 */
	public function hasPattern()
	{
		return NULL !== $this->_pattern;
	}
	//------------------------------------------------
	/**
	 * Sets the pattern
	 *
	 * @param  string  $in_pattern  The regular expression
	 *
	 * @return  Jadva_File_Filter_Regex  Provides a fluent interface
	 */
	public function setPattern($in_pattern)
	{
		$this->_pattern = (string) $in_pattern;

		return $this;
	}
	//------------------------------------------------
	/** Implements Jadva_File_Filter_Interface::filter */
	public function filter(Jadva_File_Abstract $file)
	{
		if( !$this->hasPattern() ) {
			/** @see Jadva_File_Filter_Exception */
			require_once 'Jadva/File/Filter/Exception.php';
			throw new Jadva_File_Filter_Exception('No pattern has been set');
		}

		$status = @preg_match($this->getPattern(), $file->getBasename());
		if( FALSE === $status ) {
			/** @see Jadva_File_Filter_Exception */
			require_once 'Jadva/File/Filter/Exception.php';
			throw new Jadva_File_Filter_Exception('Internal error matching pattern "' . $this->_pattern . '" against base name "' . $file->getBasename() . '"');
		}

		return (boolean) $status;
	}
	//------------------------------------------------
	/**
	 * Contains the pattern
	 *
	 * @var  string
	 */
	protected $_pattern;
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
