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
 * @version    $Id: Abstract.php 377 2011-10-01 09:16:43Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_FaqList_Renderer_Abstract */
require_once 'Jadva/FaqList/Renderer/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Adds functions for HTML output
 *
 * @category   JAdVA
 * @package    Jadva_FaqList
 * @subpackage Jadva_FaqList_Renderer
 */
abstract class Jadva_FaqList_Renderer_Html_Abstract extends Jadva_FaqList_Renderer_Abstract
{
	//------------------------------------------------
	/**
	 * Returns the URL for the given question
	 *
	 * @param  Jadva_FaqList_Group     $group     The group the question belongs in
	 * @param  Jadva_FaqList_Question  $question  The question
	 *
	 * return  string  The link
	 */
	abstract protected function _getQuestionUrl(Jadva_FaqList_Group $group, Jadva_FaqList_Question $question);
	//------------------------------------------------
	/**
	 * Processes the answer of the given question into HTML, replacing elements and matching icons and such
	 *
	 * @param  Jadva_FaqList_Group     $group     The group the question belongs in
	 * @param  Jadva_FaqList_Question  $question  The question
	 * @param  array                  &$iconList  The array to add found icons to
	 *
	 * @return  string  The HTML
	 */
	protected function _processAnswerHtml(Jadva_FaqList_Group $group, Jadva_FaqList_Question $question, array &$iconList)
	{
		$faq = $this->getFaq();

		$answer = $question->answer;

		$matchesList = array(); $replaceList = array(); $replaceMatchList = array();
		$matchCount = preg_match_all('/<qlink[^>]*>/', $answer, $matchesList);
		foreach($matchesList[0] as $match) {
			if( empty($match) ) {
				continue;
			}

			if( FALSE === strpos($match, 'href="') ) {
				$this->_warning('Invalid qlink: ' . $match);
				continue;
			}

			list($pre, $rest) = explode('href="', $match);
			list($name, $post) = explode('"', $rest, 2);

			$url = NULL;
			if( !$group->hasQuestion($name) ) {
				foreach($faq->getGroups() as $otherGroup) {
					if( $otherGroup->hasQuestion($name) ) {
						$url = $this->_getQuestionUrl($otherGroup, $otherGroup->getQuestion($name));
						break;
					}
				}
			} else {
				$url = $this->_getQuestionUrl($group, $group->getQuestion($name));
			}

			$replaceList[]      = '/<qlink([^>*]href=")' . $name . '"/';
			$replaceMatchList[] = '<a\1' . $url . '"';
		}

		if( count($replaceList) ) {
			$replaceList      = array_unique($replaceList);
			$replaceMatchList = array_unique($replaceMatchList);
			$answer = preg_replace($replaceList, $replaceMatchList, $answer);
		}


		$answer = str_replace('</qlink', '</a', $answer);

		$answer = str_replace('<url>', '<span class="in-text-url">', $answer);
		$answer = str_replace('</url>', '</span>', $answer);

		$matchesList = array();
		$matchCount = preg_match_all('/<icon[^>]*>/', $answer, $matchesList);
		foreach($matchesList[0] as $match) {
			if( empty($match) ) {
				continue;
			}

			if( FALSE === strpos($match, 'name="') ) {
				$this->_warning('Invalid icon: ' . $match);
				continue;
			}

			list($pre, $rest) = explode('name="', $match);
			list($name, $post) = explode('"', $rest, 2);

			$iconList[$name] = $name;
		}

		$answer = str_replace('<icon name="', '<img class="icon" src="./icons/', $answer);

		return $answer;
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
