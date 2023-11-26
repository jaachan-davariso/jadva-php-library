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
 * @package    Jadva_Translate
 * @subpackage Jadva_Translate_Adapter
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Jtxml.php 106 2009-03-19 16:43:04Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Zend_Translate_Adapter */
require_once 'Zend/Translate/Adapter.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Translator for JAdVA Translation files
 *
 * @see http://jaachan.com/standards/jtxml-0.1.dtd
 *
 * @category   JAdVA
 * @package    Jadva_Translate
 * @subpackage Jadva_Translate_Adapter
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Translate_Adapter_Jtxml extends Zend_Translate_Adapter
{
	//------------------------------------------------
	/** Implements Zend_Translate_Adapter::_loadTranslationData */
	protected function _loadTranslationData($filename, $locale, array $options = array())
	{
		$options = $this->_options + $options;

		if( $options['clear'] ) {
			$this->_translate = array();
		}

		if( !is_readable($filename) ) {
			require_once 'Zend/Translate/Exception.php';
			throw new Zend_Translate_Exception('Translation file \'' . $filename . '\' is not readable.');
		}

		$document = new DOMDocument;
		$loadSuccess = @$document->load($filename, LIBXML_COMPACT | LIBXML_NOBLANKS);
		if( !$loadSuccess ) {
			require_once 'Zend/Translate/Exception.php';
			throw new Zend_Translate_Exception('Translation file \'' . $filename . '\' is not valid XML.');
		}

		$language = $document->documentElement->getAttribute('target-language');

		if( !array_key_exists($language, $this->_translate) ) {
			$this->_translate[$language] = array();
		}

		for($i = 0; $i < $document->documentElement->childNodes->length; $i++) {
			$node = $document->documentElement->childNodes->item($i);
			if( !($node instanceof DOMElement) ) {
				continue;
			}

			$this->_translate[$language][$node->getAttribute('source')] = $node->textContent;
		}
	}
	//------------------------------------------------
	/** Implements Zend_Translate_Adapter::toString */
	public function toString()
	{
		return "Jtxml";
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
