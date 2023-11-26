<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * JAdVA library
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
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: ShorthandByteValue.php 62 2009-01-17 14:31:54Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Installer_Test_PhpVar_Abstract */
require_once 'Jadva/Installer/Test/PhpVar/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class represents a test on a PHP variable that is a byte value
 *
 * Examples would be: post_max_size, memory_limit
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Installer_Test_PhpVar_ShorthandByteValue extends Jadva_Installer_Test_PhpVar_Abstract
{
	//------------------------------------------------
	/**
	 * Sets the maxium value
	 *
	 * @param  string|integer  $in_maximum  The maximum value
	 *
	 * @return  Jadva_Installer_Test_PhpVar_ShorthandByteValue  Provides a fluent interface
	 */
	public function setMaximum($in_maximum)
	{
		$this->_maximum = $this->convertToInteger($in_maximum);

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the maximum value
	 *
	 * @return  integer  The maximum value
	 */
	public function getMaximum()
	{
		return $this->_maximum;
	}
	//------------------------------------------------
	/**
	 * Sets the minimum value
	 *
	 * @param  string|integer  $in_minimum  The minimum value
	 *
	 * @return  Jadva_Installer_Test_PhpVar_ShorthandByteValue  Provides a fluent interface
	 */
	public function setMinimum($in_minimum)
	{
		$this->_minimum = $this->convertToInteger($in_minimum);
	}
	//------------------------------------------------
	/**
	 * Returns the minimum value
	 *
	 * @return  integer  The minimum value
	 */
	public function getMinimum()
	{
		return $this->_minimum;
	}
	//------------------------------------------------
	/**
	 * Converts a shorthand notation value to its integer value in bytes
	 *
	 * Seems to be like PHP accepts file up to around 2KiB larger than the specified upload_max_filesize.
	 *
	 * @param  string  $in_shorthand  The shorthand notation
	 *
	 * @return  integer  The value in bytes
	 */
	public function convertToInteger($in_shorthand)
	{
		$shorthand = (string) $in_shorthand;
		$shorthand = strtolower(trim($shorthand));

		$last  = substr($shorthand, -1);
		$value = (float) $shorthand;

		switch($last) {
		case 'g': // The 'G' modifier is available since PHP 5.1.0
			$value *= 1024;
		case 'm':
			$value *= 1024;
		case 'k':
			$value *= 1024;
		}

		return $value;
	}
	//------------------------------------------------
	/**
	 * Converts a value in bytes to its shorthand notation
	 *
	 * @param  integer  $in_bytes  The value in bytes
	 *
	 * @return  string  The shorthand notation
	 */
	public function convertToShorthand($in_bytes)
	{
		$bytes = (int) $in_bytes;

		$last = '';
		if( 1024 <= $bytes ) {
			$bytes /= 1024;
			$last = 'K';
		}

		if( 1024 <= $bytes ) {
			$bytes /= 1024;
			$last = 'M';
		}

		if( 1024 <= $bytes ) {
			$bytes /= 1024;
			$last = 'G';
		}

		return sprintf('%.2f', $bytes) . $last;

	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Test_PhpVar_Abstract::test */
	public function test()
	{
		$value   = $this->getVarValue();
		$value   = $this->convertToInteger($value);

		$minimum = $this->getMinimum();
		if( (NULL !== $minimum) && ($value < $minimum) ) {
			return FALSE;
		}

		$maximum = $this->getMaximum();
		if( (NULL !== $maximum) && ($maximum < $value) ) {
			return FALSE;
		}

		return TRUE;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Test_PhpVar_Abstract::renderExpectedValue */
	public function renderExpectedValue()
	{
		if( NULL === $this->_minimum ) {
			return 'At most ' . $this->convertToShorthand($this->_maximum);
		}

		if( NULL === $this->_maximum ) {
			return 'At least ' . $this->convertToShorthand($this->_minimum);
		}

		return 'Between ' . $this->convertToShorthand($this->_minimum) . ' and ' . $this->convertToShorthand($this->_maximum);
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Test_PhpVar_Abstract::renderActualValue */
	public function renderActualValue()
	{
		$value   = $this->getVarValue();
		$value   = $this->convertToInteger($value);
		$value   = $this->convertToShorthand($value);

		return $value;
	}
	//------------------------------------------------
	/**
	 * Contains the minimum value, if any
	 *
	 * @var  integer|NULL
	 */
	protected $_minimum = NULL;
	//------------------------------------------------
	/**
	 * Contains the maximum value, if any
	 *
	 * @var  integer|NULL
	 */
	protected $_maximum = NULL;
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
