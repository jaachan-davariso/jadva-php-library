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
 * @version    $Id: Flag.php 62 2009-01-17 14:31:54Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Installer_Test_PhpVar_Abstract */
require_once 'Jadva/Installer/Test/PhpVar/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class represents a test on a PHP variable that is a flag (either 'on' or 'off')
 *
 * Example variables would be: magic_quotes_gpc, register_globals, display_errors (though not really)
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Installer_Test_PhpVar_Flag extends Jadva_Installer_Test_PhpVar_Abstract
{
	//------------------------------------------------
	/**
	 * Sets the expected state
	 *
	 * @param  string|boolean  $in_state  The expected state. Either 'on', 'off', or a boolean value.
	 *
	 * @return  Jadva_Installer_Test_PhpVar_Flag  Provides a fluent interface
	 */
	public function setExpectedState($in_state)
	{
		if( is_string($in_state) ) {
			$strState = strtolower(trim($in_state));

			switch($strState) {
			case 'off': $state = FALSE; break;
			case 'on' : $state = TRUE;  break;
			default: 
				/** @see Jadva_Installer_Exception */
				require_once 'Jadva/Installer/Exception.php';
				throw new Jadva_Installer_Exception('Invalid expected state: ' . $in_state);
			}
		} else {
			$state = (bool) $in_state;
		}

		$this->_expectedState = $state;
	}
	//------------------------------------------------
	/**
	 * Returns the expected state
	 *
	 * @throws  Jadva_Installer_Exception when the expected state hasn't been set yet
	 * @return  boolean  The expected state
	 */
	public function getExpectedState()
	{
		if( NULL === $this->_expectedState ) {
			/** @see Jadva_Installer_Exception */
			require_once 'Jadva/Installer/Exception.php';
			throw new Jadva_Installer_Exception('No expected state set');
		}

		return $this->_expectedState;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Test_PhpVar_Abstract::test */
	public function test()
	{
		$actualState = (bool) $this->getVarValue();

		return $actualState === $this->getExpectedState();
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Test_PhpVar_Abstract::test */
	public function renderExpectedValue()
	{
		return $this->getExpectedState() ? 'On' : 'Off';
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Test_PhpVar_Abstract::test */
	public function renderActualValue()
	{
		$actualState = (bool) $this->getVarValue();

		return $actualState ? 'On' : 'Off';
	}
	//------------------------------------------------
	/**
	 * Contains the expected state, if set
	 *
	 * @var  boolean|NULL
	 */
	protected $_expectedState = NULL;
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
