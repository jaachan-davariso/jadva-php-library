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
 * @version    $Id: NotaFile.php 227 2009-07-24 10:45:30Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File_Exception_PathAbstract */
require_once 'Jadva/File/Exception/PathAbstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Exceptions thrown when a requested path is not a file when it should be.
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File_Exception
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_File_Exception_NotaFile extends Jadva_File_Exception_PathAbstract
{
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @param  string  $in_path  The requested path which is not a file when it should be
	 */
	public function __construct($in_path)
	{
		parent::__construct($in_path, 'The given file or directory ("%path%") is not a file.');
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
