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
 * @package    Jadva_FaqList
 * @subpackage Jadva_FaqList_Renderer
 * @copyright  Copyright (c) 2011 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: SinglePageHtml.php 378 2011-10-01 09:18:27Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_FaqList_Renderer_Html_Abstract */
require_once 'Jadva/FaqList/Renderer/Html/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Class to render a FaqList as a single HTML page
 *
 * @category   JAdVA
 * @package    Jadva_FaqList
 * @subpackage Jadva_FaqList_Renderer
 */
class Jadva_FaqList_Renderer_SinglePageHtml extends Jadva_FaqList_Renderer_Html_Abstract
{
	//------------------------------------------------
	/**
	 * Implements {@link Jadva_FaqList_Renderer_Abstract::render()}
	 *
	 * @return  void
	 */
	public function render()
	{
		$f = $this->getFaq();

		$iconList = array();

		$out = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
		$out .= '<html xmlns="http://www.w3.org/1999/xhtml"><head>';
		$out .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		$out .= '<title>' . $this->_escape($f->getName()) . '</title>';
		$out .= '<meta name="generator" content="' . __CLASS__ . '" />';
		$out .= '<link href="./style.css" media="screen" rel="stylesheet" type="text/css" />';
		$out .= '</head><body>';

		$out .= '<h1>' . $this->_escape($f->getName()) . '</h1>';
		$out .= $f->getHomePageText();

		foreach($f->getGroups() as $group) {
			if( !$group->hasQuestions() ) {
				continue;
			}

			$out .= '<h2>' . $this->_escape($group->name) . '</h2>';

			foreach($group->getQuestions() as $question) {
				$out .= '<h3><a name="q_' . $group->id() . '__' . $question->id() . '"></a>' . $this->_escape($question->question) . '</h3>';
				$out .= $this->_processAnswerHtml($group, $question, $iconList);
			}
		}
		$out .= '</body></html>';

		$outDir = $this->getOutputDirectory();
		file_put_contents($outDir->getFile('index.html')->getPath(), $out);

		if( 0 < count($iconList) ) {
			$iconDirectory = $outDir->getSubdirectory('icons');
			$iconDirectory->ensureExistance();
			$idp = $iconDirectory->getPath();

			$this->_copyIcons($iconList, $idp);
		}
	}
	//------------------------------------------------
	/**
	 * Implements {@link Jadva_FaqList_Renderer_Html_Abstract::_getQuestionUrl}
	 */
	protected function _getQuestionUrl(Jadva_FaqList_Group $group, Jadva_FaqList_Question $question)
	{
		return '#q_' . $group->id() . '__' . $question->id();
	}
	//------------------------------------------------
	/**
	 * Escapes the given text
	 *
	 * @param  string  $text  The text to escape
	 *
	 * @return  string  The escaped text
	 */
	protected function _escape($text)
	{
		return htmlspecialchars($text, ENT_COMPAT, 'utf-8');
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
