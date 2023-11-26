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
 * @package    Jadva_File
 * @subpackage Jadva_File_Exception
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: PathAbstract.php 227 2009-07-24 10:45:30Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File_Exception */
require_once 'Jadva/File/Exception.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Abstract exception class for exceptions related to a path
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File_Exception
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
abstract class Jadva_File_Exception_PathAbstract extends Jadva_File_Exception
{
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @param  string  $in_path  The path related to this error
	 * @param  string  $message  The message
	 * @param  mixed   $code     The error code
	 */
	public function __construct($in_path, $message = null, $code = 0)
	{
		if( $in_path instanceof Jadva_File_Abstract ) {
			if( $in_path->isSchemeFile() ) {
				$in_path = $in_path->getPath();
			} else {
				$in_path = $in_path->getUrl();
			}
		}
		$this->_path = (string) $in_path;

		if( NULL !== $message ) {
			$message = str_replace('%path%', $this->getPath(), $message);
		}

		parent::__construct($message, $code);

	}
	//------------------------------------------------
	/**
	 * Returns the path related to this error
	 *
	 * @return  string  The path
	 */
	public function getPath()
	{
		return $this->_path;
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
