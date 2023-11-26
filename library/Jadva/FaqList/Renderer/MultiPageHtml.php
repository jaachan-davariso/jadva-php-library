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
 * @version    $Id: FaqList.php 350 2010-06-19 09:55:01Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_FaqList_Renderer_Html_Abstract */
require_once 'Jadva/FaqList/Renderer/Html/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Class to render a FaqList as multiple HTML pages
 *
 * @category   JAdVA
 * @package    Jadva_FaqList
 * @subpackage Jadva_FaqList_Renderer
 */
class Jadva_FaqList_Renderer_MultiPageHtml extends Jadva_FaqList_Renderer_Html_Abstract
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

		$dp = $this->getOutputDirectory()->getPath();

		if( $out = $this->getSourceDirectory() ) {
			$sp = $out->getPath();
		}

		$iconList = array();
		$fileList = array();

		$this->_readFiles($f->getHomePageText(), $fileList);
		file_put_contents($dp . 'index.html', $this->_generateIndex());

		foreach($f->getGroups() as $group) {
			if( !$group->hasQuestions() ) {
				continue;
			}

			file_put_contents($dp . $group->id() . '.html', $this->_generateFaqPage($group, $iconList, $fileList));
		}
		file_put_contents($dp . 'toc.html', $this->_generateToc());

		//Copy additional files
		$stylesheet = $this->getStylesheet();
		if( !empty($styleSheet) ) {
			$styleSheet = realpath($sp . $styleSheet);
			if( FALSE === $styleSheet ) {
				$this->_warning('Could not find given stylesheet');
			}
		}

		if( empty($styleSheet) ) {
			$styleSheet = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MultiPageHtml.css';
		}
		copy($styleSheet, $dp . 'style.css');


		if( 0 < count($iconList) ) {
			$iconDirectory = Jadva_File_Directory::getInstanceFor($dp . 'icons/');
			$iconDirectory->ensureExistance();
			$idp = $iconDirectory->getPath();

			$this->_copyIcons($iconList, $idp);
		}

		if( 0 < count($fileList) ) {
			$this->_copyFiles($fileList, $sp, $dp);
		}

	}
	//------------------------------------------------
	/**
	 * Generates the HTML header for the pages
	 *
	 * @param  string               $title         The title for the page
	 * @param  Jadva_FaqList_Group  $currentGroup  (OPTIONAL) The group who's page, if any
	 *
	 * @return  string  The HTML
	 */
	protected function _generateHtmlHeader($title, Jadva_FaqList_Group $currentGroup = NULL)
	{
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
		$html .= '<html xmlns="http://www.w3.org/1999/xhtml"><head>';
		$html .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		$html .= '<title>' . $title . '</title>';
		$html .= '<meta name="generator" content="' . __CLASS__ . '" />';
		$html .= '<link href="./style.css" media="screen" rel="stylesheet" type="text/css" />';

		$html .= '<link href="./index.html" rel="top" title="Index" />';
		if( $currentGroup ) {
			$html .= '<link href="./index.html" rel="up" title="Index" />';
		}
		$html .= '<link href="./index.html" rel="index" title="Index" />';

		$groupList = $this->getFaq()->getGroups();
		if( NULL < count($groupList) ) {
			$html .= $this->_generateGroupHeadLink(reset($groupList), 'first');

			if( $currentGroup ) {
				$keys = array_keys($groupList);
				$currentGroupIndex = array_search($currentGroup->id(), $keys);

				if( 0 < $currentGroupIndex ) {
					$html .= $this->_generateGroupHeadLink($groupList[$keys[$currentGroupIndex - 1]], 'prev');
				}

				if( $currentGroupIndex < count($groupList) - 1 ) {
					$html .= $this->_generateGroupHeadLink($groupList[$keys[$currentGroupIndex + 1]], 'next');
				}
			}

			$html .= $this->_generateGroupHeadLink(end($groupList), 'last');
		}


		$html .= '</head><body>';
		$html .= '<h1><a name="top">' . htmlspecialchars($title) . '</a></h1>';

		return $html;
	}
	//------------------------------------------------
	/**
	 * Implements {@link Jadva_FaqList_Renderer_Html_Abstract::_getQuestionUrl}
	 */
	protected function _getQuestionUrl(Jadva_FaqList_Group $group, Jadva_FaqList_Question $question)
	{
		return $group->id() . '.html#q_'. $question->id();
	}
	//------------------------------------------------
	/**
	 * Generates the menu
	 *
	 * @param  Jadva_FaqList_Group  $currentGroup  The current group
	 * @param  string               $pageName      (OPTIONAL) The name of the page, if it's a special page
	 *
	 * @return  string  The HTML
	 */
	protected function _generateMenu(Jadva_FaqList_Group $currentGroup = NULL, $pageName = NULL)
	{
		$faq = $this->getFaq();

		$list = array();

		if( $faq->hasHomePageText() ) {
			$list[] = '<li class="home ' . ('home' === $pageName ? 'active' : '') . '"><a href="./index.html">Home</a></li>';
		}

		foreach($faq->getGroups() as $groupId => $group) {
			if( !$group->hasQuestions() ) {
				continue;
			}

			$class = 'group';
			if( $currentGroup && ($currentGroup->id() == $group->id()) ) {
				$class .= ' active';
			}

			$list[] = '<li class="' . $class . '" group-id="' . $groupId . '"><a href="./' . $groupId . '.html">' . $group->name . '</a></li>';
		}

		$list[] = '<li class="toc ' . ('toc' === $pageName ? 'active' : '') . '"><a href="./toc.html">Table of contents</a></li>';

		return '<ul class="group_menu">' . implode($list) . '</ul>';
	}
	//------------------------------------------------
	/**
	 * Generates the HTML for the index.html file
	 *
	 * @return  string  The HTML
	 */
	protected function _generateIndex()
	{
		$faq = $this->getFaq();

		return $this->_generateHtmlHeader($faq->getName())
			. $this->_generateMenu(NULL, 'home')
			. '<div id="main_content"><h2>' . $faq->getName()
			. '</h2>'
			. ($faq->hasHomePageText() ? $faq->getHomePageText() : '<p>Select a group in the menu</p>')
			. '</div></body></html>';
	}
	//------------------------------------------------
	/**
	 * Generates the HTML for the table of contents file
	 *
	 * @return  string  The HTML
	 */
	protected function _generateToc()
	{
		$faq = $this->getFaq();

		return $this->_generateHtmlHeader($faq->getName())
			. $this->_generateMenu(NULL, 'toc')
			. '<div id="main_content"><h2>Table of contents</h2>'
			. $this->_generateQuestionMenu()
			. '</div></body></html>';
	}
	//------------------------------------------------
	/**
	 * Generates the HTML for a FAQ page
	 *
	 * @param  Jadva_FaqList_Group   $group     The group who's page to generate
	 * @param  array                &$iconList  The icon list to store the found icons in
	 * @param  array                &$fileList  The array to store the file list in
	 *
	 * @return  string  The HTML
	 */
	protected function _generateFaqPage(Jadva_FaqList_Group $group, array &$iconList, array &$fileList)
	{
		$faq = $this->getFaq();

		return $this->_generateHtmlHeader($faq->getName(), $group)
			. $this->_generateMenu($group)
			. '<div id="main_content">'
			. '<h2>' . htmlspecialchars($group->name) . '</h2>'
			. $this->_generateQuestionMenu($group)
			. $this->_generateQuestionList($group, $iconList, $fileList)
			. '</div></body></html>';
	}
	//------------------------------------------------
	/**
	 * Generates the HTML for the menu of questions
	 *
	 * @param  Jadva_FaqList_Group   $group     The group who's page to generate, NULL for table of contents
	 *
	 * @return  string  The HTML
	 */
	protected function _generateQuestionMenu(Jadva_FaqList_Group $group = NULL)
	{
		$faq = $this->getFaq();

		if( NULL === $group ) {
			$questionList = array();
			$questionToGroup = array();
			foreach($faq->getGroups() as $itGroup) {
				foreach($itGroup->getQuestions() as $q) {
					$questionList[$q->question] = $q;
					$questionToGroup[$q->id()] = $itGroup;
				}
			}
			ksort($questionList);
		} else {
			$questionList = $group->getQuestions();
			if( 0 == count($questionList) ) {
				return '';
			}
		}

		$list = array();
		foreach($questionList as $question) {
			$name = 'qln_' . $question->id();

			$href = '';
			if( NULL === $group ) {
				$href = $questionToGroup[$question->id()]->id() . '.html';
			}
			$href .= '#q_' . $question->id();

			$list[] = '<li><a name="' . $name . '" href="' . $href . '">' . 
				htmlspecialchars($question->question)
				. '</a></li>';
		}

		return '<ol class="question_index">' . implode($list) . '</ol>';
	}
	//------------------------------------------------
	/**
	 * Generates the HTML for the question list on a FAQ page
	 *
	 * @param  Jadva_FaqList_Group   $group     The group who's page to generate
	 * @param  array                &$iconList  The icon list to store the found icons in
	 * @param  array                &$fileList  The array to store the file list in
	 *
	 * @return  string  The HTML
	 */
	protected function _generateQuestionList(Jadva_FaqList_Group $group, array &$iconList, array &$fileList)
	{
		$faq = $this->getFaq();

		$questionList = $group->getQuestions();
		if( 0 == count($questionList) ) {
			return 'There are no questions in this group';
		}

		$list = array();
		foreach($questionList as $question) {
			$html = '<dt><a name="q_' . $question->id() . '">';
			$html .= htmlspecialchars($question->question);
			$html .= '</a></dt>';

			$answer = $this->_processAnswerHtml($group, $question, $iconList);

			$this->_readFiles($answer, $fileList);

			$html .= '<dd>' . $answer . '<div class="to_top"><a href="#top">Back to top</a></div></dd>';

			$list[] = $html;
		}

		return '<dl class="question_list">' . implode($list) . '</dl>';
	}
	//------------------------------------------------
	/**
	 * Generates a <link /> element for a given group
	 *
	 * @param  Jadva_FaqList_Group  $group  The group
	 * @param  string               $rel    The relation to the current page
	 *
	 * @return  string  The HTML
	 */
	protected function _generateGroupHeadLink(Jadva_FaqList_Group $group, $rel)
	{
		return '<link href="./' . $group->id() . '.html" rel="' . $rel . '" title="' . $group->name . '" />';
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
