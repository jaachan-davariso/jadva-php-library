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
 * @version    $Id: Interface.php 43 2008-09-26 09:04:34Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * Interface for the output of the installer objects
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_OutputFormatter
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
interface Jadva_Installer_OutputFormatter_Interface
{
	//------------------------------------------------
	/**
	 * This message is an emergency
	 */
	const LOG_EMERG    = 1;
	//------------------------------------------------
	/**
	 * This message is an alert
	 */
	const LOG_ALERT    = 2;
	//------------------------------------------------
	/**
	 * This message is critical
	 */
	const LOG_CRIT     = 3;
	//------------------------------------------------
	/**
	 * This message is an error
	 */
	const LOG_ERR      = 4;
	//------------------------------------------------
	/**
	 * This message is a warning
	 */
	const LOG_WARNING  = 5;
	//------------------------------------------------
	/**
	 * This message is a notice
	 */
	const LOG_NOTICE   = 6;
	//------------------------------------------------
	/**
	 * This message is purely informational
	 */
	const LOG_INFO     = 7;
	//------------------------------------------------
	/**
	 * This message is for debug purposes only
	 */
	const LOG_DEBUG    = 8;
	//------------------------------------------------
	/**
	 * Starts the output
	 *
	 * @return  Jadva_Installer_OutputFormatter_Interface  Provides a fluent interface
	 */
	public function outputStart();
	//------------------------------------------------
	/**
	 * Output an emergency message
	 *
	 * @param  string  $message  The message to output
	 *
	 * @return  Jadva_Installer_OutputFormatter_Interface  Provides a fluent interface
	 */
	public function outputEmerg($message);
	//------------------------------------------------
	/**
	 * Output an alert message
	 *
	 * @param  string  $message  The message to output
	 *
	 * @return  Jadva_Installer_OutputFormatter_Interface  Provides a fluent interface
	 */
	public function outputAlert($message);
	//------------------------------------------------
	/**
	 * Output a critical message
	 *
	 * @param  string  $message  The message to output
	 *
	 * @return  Jadva_Installer_OutputFormatter_Interface  Provides a fluent interface
	 */
	public function outputCrit($message);
	//------------------------------------------------
	/**
	 * Output an error message
	 *
	 * @return  Jadva_Installer_OutputFormatter_Interface  Provides a fluent interface
	 */
	public function outputErr($message);
	//------------------------------------------------
	/**
	 * Output a warning message
	 *
	 * @param  string  $message  The message to output
	 *
	 * @return  Jadva_Installer_OutputFormatter_Interface  Provides a fluent interface
	 */
	public function outputWarning($message);
	//------------------------------------------------
	/**
	 * Output a notice message
	 *
	 * @param  string  $message  The message to output
	 *
	 * @return  Jadva_Installer_OutputFormatter_Interface  Provides a fluent interface
	 */
	public function outputNotice($message);
	//------------------------------------------------
	/**
	 * Output an informational message
	 *
	 * @param  string  $message  The message to output
	 *
	 * @return  Jadva_Installer_OutputFormatter_Interface  Provides a fluent interface
	 */
	public function outputInfo($message);
	//------------------------------------------------
	/**
	 * Output an debug message
	 *
	 * @param  string  $message  The message to output
	 *
	 * @return  Jadva_Installer_OutputFormatter_Interface  Provides a fluent interface
	 */
	public function outputDebug($message);
	//------------------------------------------------
	/**
	 * Report a success
	 *
	 * @param  string  $message  The message to output
	 *
	 * @return  Jadva_Installer_OutputFormatter_Interface  Provides a fluent interface
	 */
	public function outputSuccess($message);
	//------------------------------------------------
	/**
	 * Ends the output
	 *
	 * @return  void
	 */
	public function outputEnd();
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
