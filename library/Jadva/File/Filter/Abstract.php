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
 * @version    $Id: Abstract.php 99 2009-03-16 18:32:15Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_File_Filter_Interface */
require_once 'Jadva/File/Filter/Interface.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Abstract class for file filter
 *
 * @category   JAdVA
 * @package    Jadva_File
 * @subpackage Jadva_File_Filter
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
abstract class Jadva_File_Filter_Abstract implements Jadva_File_Filter_Interface
{
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @param  array  $options  Options to set
	 */
	public function __construct(array $options = array())
	{
		$this->setOptions($options);
	}
	//------------------------------------------------
	/**
	 * Sets the options
	 *
	 * @param  array  $options  Options to set
	 *
	 * @return  Jadva_File_Filter_Abstract  Provides a fluent interface
	 */
	public function setOptions(array $options)
	{
		unset($options['options']);
		foreach($options as $optionName => $optionValue) {
			$methodName = 'set' . ucfirst($optionName);
			if( method_exists($this, $methodName) ) {
				$this->$methodName($optionValue);
			}
		}

		return $this;
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
