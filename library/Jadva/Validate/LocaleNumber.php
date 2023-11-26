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
 * @package    Jadva_Validate
 * @subpackage Jadva_Validate
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: LocaleNumber.php 136 2009-04-04 13:32:31Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class validates an value for a locale number
 *
 * @category   JAdVA
 * @package    Jadva_Validate
 * @subpackage Jadva_Validate
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Validate_LocaleNumber extends Zend_Validate_Abstract
{
	const NOT_A_NUMBER = 'isEmpty';

	protected $_messageTemplates = array(
		self::NOT_A_NUMBER => "'%value%' is not a valid number"
	);
	//------------------------------------------------
	/** Implements Zend_Validate_Interface::isValid */
	public function isValid($value)
	{
		$this->_setValue((string) $value);

		try {
			$number = Zend_Locale_Format::getNumber($value);
		} catch (Zend_Locale_Exception $e ) {
			$this->_error();
			return FALSE;
		}

		return TRUE;
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
