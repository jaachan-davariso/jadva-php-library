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
 * @subpackage Jadva_Installer_OutputFormatter
 * @copyright  Copyright (c) 2010 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Array.php 357 2010-09-24 11:50:34Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Installer_OutputFormatter_Interface */
require_once 'Jadva/Installer/OutputFormatter/Interface.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Implements {@link Jadva_Installer_OutputFormatter_Interface} with no output, the text is stored in an array
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_OutputFormatter
 */
class Jadva_Installer_OutputFormatter_Array implements Jadva_Installer_OutputFormatter_Interface
{
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputStart */
	public function outputStart()
	{}
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputEmerg */
	public function outputEmerg($message)
	{
		$this->_outputMessage($message, 'emerg');

		return $this;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputAlert */
	public function outputAlert($message)
	{
		$this->_outputMessage($message, 'alert');

		return $this;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputCrit */
	public function outputCrit($message)
	{
		$this->_outputMessage($message, 'crit');

		return $this;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputErr */
	public function outputErr($message)
	{
		$this->_outputMessage($message, 'err');

		return $this;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputWarning */
	public function outputWarning($message)
	{
		$this->_outputMessage($message, 'warning');

		return $this;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputNotice */
	public function outputNotice($message)
	{
		$this->_outputMessage($message, 'notice');

		return $this;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputInfo */
	public function outputInfo($message)
	{
		$this->_outputMessage($message, 'info');

		return $this;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputDebug */
	public function outputDebug($message)
	{
		$this->_outputMessage($message, 'debug');

		return $this;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputSuccess */
	public function outputSuccess($message)
	{
		$this->_outputMessage($message, 'success');

		return $this;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputEnd */
	public function outputEnd()
	{}
	//------------------------------------------------
	/**
	 * Outputs a message at the given message level
	 *
	 * @param  string   $message       The message to output
	 * @param  integer  $messageLevel  The level of the message to output
	 *
	 * @return void
	 */
	protected function _outputMessage($message, $messageLevel)
	{
		$this->_messageList[] = array(time(), $message, $messageLevel);

		$this->_levelCounts[$messageLevel]++;
	}
	//------------------------------------------------
	/**
	 * Returns all messages
	 *
	 * @return  array  The messages
	 */
	public function getMessages()
	{
		return $this->_messageList;
	}
	//------------------------------------------------
	/**
	 * Returns the number of messages on each level, or on all levels if no level is passed
	 *
	 * @param  string  $level  (OPTIONAL) The level to count; set to NULL for all levels
	 *
	 * @return  array|integer  The level counts
	 */
	public function getLevelCounts($level = NULL)
	{
		if( NULL === $level ) {
			return $this->_levelCounts;
		}

		return $this->_levelCounts[$level];
	}
	//------------------------------------------------
	/**
	 * Contains all messages
	 *
	 * @var  array
	 */
	protected $_messageList = array();
	//------------------------------------------------
	/**
	 * Contains the number of messages on each level
	 *
	 * @var  array
	 */
	protected $_levelCounts = array(
		'emerg'   => 0,
		'alert'   => 0,
		'crit'    => 0,
		'err'     => 0,
		'warning' => 0,
		'notice'  => 0,
		'info'    => 0,
		'debug'   => 0,
		'success' => 0,
	);
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
