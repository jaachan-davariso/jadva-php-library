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
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Interface.php 212 2009-07-14 17:09:21Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File */
require_once 'Jadva/File.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Interface for classes that filter files
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File_Filter
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
interface Jadva_File_Filter_Interface
{
	//------------------------------------------------
	/**
	 * Filters a file
	 *
	 * @param  Jadva_File  $file  The file to filter
	 *
	 * @throws  Jadva_File_Filter_Exception in case of irrepairable error during filtering
	 * @return  boolean  TRUE if this file passes the filter (i.e. stays in the list),
	 *                   FALSE when it's filtered out
	 */
	public function filter(Jadva_File_Abstract $file);
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
