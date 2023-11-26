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
 * @version    $Id: Xhtml.php 44 2008-09-26 10:01:22Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Installer_OutputFormatter_Interface */
require_once 'Jadva/Installer/OutputFormatter/Interface.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Implements {@link Jadva_Installer_OutputFormatter_Interface} with XHTML output
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_OutputFormatter
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Installer_OutputFormatter_Xhtml implements Jadva_Installer_OutputFormatter_Interface
{
	//------------------------------------------------
	/** Implements Jadva_Installer_OutputFormatter_Interface::outputStart */
	public function outputStart()
	{
		$this->outputTag  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
		$this->outputTag  = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$this->outputTag  = '<head>';
		$this->outputTag  = '<title>';
		$this->outputText = $this->_escape($this->_title);
		$this->outputTag  = '</title>';
		$this->outputTag  = '<meta name="generator" content="Jadva_Installer_Database_OutputFormatter_Xhtml" />';
		$this->outputTag  = '<link rel="Stylesheet" href="./installer.css" type="text/css" />';
		$this->outputTag  = '</head>';
		$this->outputTag  = '<body>';
		$this->outputTag  = '<table cellspacing="0" class="message_list">';
		$this->outputTag  = '<tr class="header">';
		$this->outputTag  = '<th colspan="3">';
		$this->outputText = 'Message from the installer';
		$this->outputTag  = '</th>';
		$this->outputTag  = '</tr>';

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
	{
		$this->outputTag  = '</table>';
		$this->outputTag  = '</body>';
		$this->outputTag  = '</html>';
	}
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
	 * Contains the current level of indentation
	 *
	 * @var  integer
	 */
	protected $_indent       = 0;
	//------------------------------------------------
	/**
	 * Contains the number of messages outputted
	 *
	 * @var  integer
	 */
	protected $_messageCount = 0;
	//------------------------------------------------
	/**
	 * Allows for easy access to _writeTag and _writeText
	 *
	 * @param  string  $varName   The name of the variable that was tried to set
	 * @param  mixed   $newValue  The value that it was tried to set to
	 *
	 * @return mixed
	 */
	public function __set($varName, $newValue)
	{
		switch($varName) {
		case 'outputTag':
			$this->_writeTag($newValue);
			return $this;
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
		$this->outputTag  = '<tr class="' . $messageLevel . ' ' . (1===($this->_messageCount%2)?'odd':'even') . '">';
		$this->outputTag  = '<td class="timestamp">';
		$this->outputText = date('[Y-m-d H:i:s]');
		$this->outputTag  = '</td>';
		$this->outputTag  = '<td class="message">';
		$this->outputText = $message;
		$this->outputTag  = '</td>';
		$this->outputTag  = '<td class="level">';
		$this->outputText = '[' . str_replace(' ', '&nbsp;', str_pad($messageLevel, 7, ' ', STR_PAD_RIGHT)) . ']';
		$this->outputTag  = '</td>';
		$this->outputTag  = '</tr>';

		$this->_messageCount++;
	}
	//------------------------------------------------
	/**
	 * Writes a tag
	 *
	 * @param  string  $tag  The tag to write
	 *
	 * @return void
	 */
	protected function _writeTag($tag)
	{
		$tag = trim($tag);

		if( '!' === @$tag[1] ) {
			$this->_write($tag . PHP_EOL);
		} elseif( '/' === @$tag[1] ) {
			$this->_indent--;
			$this->_writeText($tag);
		} elseif( '/' === substr($tag, -2, 1) ) {
			$this->_writeText($tag);
		} else {
			$this->_writeText($tag);
			$this->_indent++;
		}
	}
	//------------------------------------------------
	/**
	 * Writes text at the current indent level
	 *
	 * @param  string  $text  The text to write
	 *
	 * @return void
	 */
	protected function _writeText($text)
	{
		$this->_write(str_repeat("\t", $this->_indent) . $text . PHP_EOL);
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
	/**
	 * Escapes text for the XHTML output
	 *
	 * @param  string  $text  The text to escape
	 *
	 * @return  The escaped text
	 */
	protected function _escape($text)
	{
		return htmlentities($text);
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
