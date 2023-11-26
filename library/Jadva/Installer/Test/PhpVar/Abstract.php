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
 * @version    $Id: Abstract.php 62 2009-01-17 14:31:54Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class represents a test on a PHP variable
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
abstract class Jadva_Installer_Test_PhpVar_Abstract
{
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @param  string $in_varName  The name of the variable to test
	 * @param  array  $options     The options to set, mapping the name of the variable to the value
	 */
	public function __construct($in_varName, array $options = array())
	{
		$this->_varName = (string) $in_varName;

		foreach($options as $optionName => $optionValue) {
			$methodName = 'set' . ucfirst($optionName);
			if( method_exists($this, $methodName) ) {
				$this->$methodName($optionValue);
			}
		}
	}
	//------------------------------------------------
	/**
	 * Returns the name of the variable to test
	 *
	 * @return  string  The name of the variable to test
	 */
	public function getVarName()
	{
		return $this->_varName;
	}
	//------------------------------------------------
	/**
	 * Sets whether this test is required to pass, in order to install the application
	 *
	 * @param  boolean  $in_required  Whether this test is required to pass
	 *
	 * @return  Jadva_Installer_Test_PhpVar_Abstract  Provides a fluent interface
	 */
	public function setRequired($in_required)
	{
		$this->_required = (bool) $in_required;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns whether this test is required to pass, in order to install the application
	 *
	 * @return  boolean  Whether this test is required to pass
	 */
	public function getRequired()
	{
		return $this->_required;
	}
	//------------------------------------------------
	/**
	 * Gets the value of the variable
	 *
	 * @return  mixed  The value of the variable
	 */
	public function getVarValue()
	{
		return ini_get($this->getVarName());
	}
	//------------------------------------------------
	/**
	 * Tests whether the variable has an acceptable value
	 *
	 * @return  boolean  TRUE if the variable has an acceptable value, FALSE otherwise
	 */
	abstract public function test();
	//------------------------------------------------
	/**
	 * Renders a string to show what the value is expected to be
	 *
	 * @return  string  A string to show what the value is expected to be
	 */
	abstract public function renderExpectedValue();
	//------------------------------------------------
	/**
	 * Renders a string to show what the value actually is
	 *
	 * @return  string  A string to show what the value actually is
	 */
	abstract public function renderActualValue();
	//------------------------------------------------
	/**
	 * Contains the name of the variable to test
	 *
	 * @var  string
	 */
	protected $_varName;
	//------------------------------------------------
	/**
	 * Contains whether this test is required to pass
	 *
	 * @var  boolean
	 */
	protected $_required = TRUE;
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
