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
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Xhtml.php 43 2008-09-26 10:01:22Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Installer_OutputFormatter_Interface */
require_once 'Jadva/Installer/OutputFormatter/Interface.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Implements {@link Jadva_Installer_OutputFormatter_Interface} with TXT output
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_OutputFormatter
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Installer_OutputFormatter_Txt implements Jadva_Installer_OutputFormatter_Interface
{
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputStart */
	public function outputStart()
	{
		$this->outputText = $this->_title;
		$this->outputText = 'Messages from the installer';

		return $this;
	}
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
	 * Sets the content for the <title> tag
	 *
	 * @param  string  $title  The content for the <title> tag
	 *
	 * @return Jadva_Installer_OutputFormatter_Xhtml  Provides a fluent interface
	 */
	public function setTitle($title)
	{
		$this->_title = (string) $title;
	}
	//------------------------------------------------
	/**
	 * Contains the content for the <title> tag
	 *
	 * @var  string
	 */
	protected $_title        = '';
	//------------------------------------------------
	/**
	 * Allows for easy access to _writeText
	 *
	 * @param  string  $varName   The name of the variable that was tried to set
	 * @param  mixed   $newValue  The value that it was tried to set to
	 *
	 * @return mixed
	 */
	public function __set($varName, $newValue)
	{
		switch($varName) {
		case 'outputText':
			$this->_writeText($newValue);
			return $this;
		}

		trigger_error('Class ' . get_class($this) . ' has no property called "' . $varName .'"', E_USER_WARNING);
	}
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
		$this->outputText = date('[Y-m-d H:i:s]')
			. '[' . str_pad($messageLevel, 7, ' ', STR_PAD_RIGHT) . '] '
			. $message;

		$this->_messageCount++;
	}
	//------------------------------------------------
	/**
	 * Writes text
	 *
	 * @param  string  $text  The text to write
	 *
	 * @return void
	 */
	protected function _writeText($text)
	{
		$this->_write($text . PHP_EOL);
	}
	//------------------------------------------------
	/**
	 * Writes all the arguments to STDOUT
	 *
	 * @param  string ...  The arguments to write out
	 */
	protected function _write()
	{
		foreach(func_get_args() as $argument) {
			echo $argument;
			flush();
		}
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
