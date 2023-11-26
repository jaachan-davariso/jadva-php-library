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
 * @subpackage Jadva_FaqList
 * @copyright  Copyright (c) 2010 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: FaqList.php 317 2010-01-23 12:25:58Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_FaqList_Group */
require_once 'Jadva/FaqList/Group.php';
/** @see Jadva_FaqList_Question */
require_once 'Jadva/FaqList/Question.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Class to deal with Frequently Asked Questions
 *
 * @category   JAdVA
 * @package    Jadva_FaqList
 * @subpackage Jadva_FaqList
 */
class Jadva_FaqList
{
	//------------------------------------------------
	/**
	 * Contains the directory where icons are located
	 *
	 * @var  string
	 */
	public static $iconDirectory = NULL;
	//------------------------------------------------
	/**
	 * Returns the name of this FAQ list
	 *
	 * @return  string  The name
	 */
	public function getName()
	{
		return $this->_name;
	}
	//------------------------------------------------
	/**
	 * Returns whether this FAQ list has a name yet
	 *
	 * @return  boolean  TRUE if this FAQ list has a name yet, FALSE otherwise
	 */
	public function hasName()
	{
		return NULL !== $this->_name;
	}
	//------------------------------------------------
	/**
	 * Sets the name for this FAQ list
	 *
	 * @param  string  $in_name  The name
	 *
	 * @return  Jadva_FaqList  Provides a fluent interface
	 */
	public function setName($in_name)
	{
		$this->_name = (string) $in_name;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the text on the home page of this FAQ list
	 *
	 * @return  string  The text on the home page
	 */
	public function getHomePageText()
	{
		return $this->_homePageText;
	}
	//------------------------------------------------
	/**
	 * Returns whether this FAQ list has a text on the home page yet
	 *
	 * @return  boolean  TRUE if this FAQ list has a text on the home page yet, FALSE otherwise
	 */
	public function hasHomePageText()
	{
		return NULL !== $this->_homePageText;
	}
	//------------------------------------------------
	/**
	 * Sets the text on the home page for this FAQ list
	 *
	 * @param  string  $in_homePageText  The text on the home page
	 *
	 * @return  Jadva_FaqList  Provides a fluent interface
	 */
	public function setHomePageText($in_homePageText)
	{
		$this->_homePageText = (string) $in_homePageText;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Loads the XML from the given string
	 *
	 * @param  string  $xml  The XML
	 *
	 * @return  Jadva_FaqList  Provides a fluent interface
	 */
	public function loadXml($xml)
	{
		$document = new DOMDocument;
		$result = $document->loadXML($xml, LIBXML_NOBLANKS);

		if( !$result ) {
			$this->_error('Invalid XML');
			return;
		}

		$docName = $document->documentElement->getAttribute('name');
		if( !$this->hasName() ) {
			$this->setName($docName);
		} else {
			if( $this->getName() !== $docName ) {
				$this->_warning('FAQ name mismatch; already have %1$s, now trying to add %2$s', $this->getName(), $docName);
			}
		}

		$xpath = new DOMXpath($document);
		$homePageNodeList = $xpath->query('/faq/home-page');
		if( 0 < $homePageNodeList->length ) {
			$this->setHomePageText($this->_domNodeToText($homePageNodeList->item(0)));
		}

		$xpath = new DOMXpath($document);
		$groupNodeList = $xpath->query('/faq/group');
		for($itEl = 0; $itEl < $groupNodeList->length; $itEl++) {
			$groupNode = $groupNodeList->item($itEl);

			$group = new Jadva_FaqList_Group(
				$groupNode->getAttribute('name'),
				$groupNode->getAttribute('id')
			);

			$this->addGroup($group);

			$xpath = new DOMXpath($document);
			$questionNodeList = $xpath->query('./question', $groupNode);
			for($itQuestEl = 0; $itQuestEl < $questionNodeList->length; $itQuestEl++) {
				$questionNode = $questionNodeList->item($itQuestEl);

				$group->addQuestion(new Jadva_FaqList_Question(
					$questionNode->getAttribute('text'),
					$this->_domNodeToText($questionNode),
					$questionNode->getAttribute('id')
				));
			}
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Converts this FAQ list to a website
	 *
	 * @param  Jadva_File_Directory|string  $targetDirectory  The directory to store the files in
	 * @param  Jadva_File_Directory|string  $sourceDirectory  (OPTIONAL) The directory to copy files from, if any
	 *
	 * @return  Jadva_FaqList  Provides a fluent interface
	 */
	public function toHtml($targetDirectory, $sourceDirectory = NULL)
	{
		/** @see Jadva_File_Directory */
		require_once 'Jadva/File/Directory.php';

		$directory = Jadva_File_Directory::getInstanceFor($targetDirectory);
		$directory->ensureExistance();
		$dp = $directory->getPath();

		$iconDirectory = Jadva_File_Directory::getInstanceFor($dp . 'icons/');
		$iconDirectory->ensureExistance();
		$idp = $iconDirectory->getPath();

		$iconList = array();
		$fileList = array();

		$this->_readFiles($this->_homePageText, $fileList);
		file_put_contents($dp . 'index.html', $this->_generateIndex());

		foreach($this->getGroups() as $group) {
			if( !$group->hasQuestions() ) {
				continue;
			}

			file_put_contents($dp . $group->id() . '.html', $this->_generateFaqPage($group, $iconList, $fileList));
		}
		file_put_contents($dp . 'toc.html', $this->_generateToc());

		//Copy additional files
		copy(
			dirname(__FILE__) . DIRECTORY_SEPARATOR . 'FaqList' . DIRECTORY_SEPARATOR . 'style.css',
			$dp . 'style.css'
		);

		$this->_copyIcons($iconList, $idp);

		if( 0 < count($fileList) ) {
			$directory = Jadva_File_Directory::verifyExistance($sourceDirectory);
			$sp = $directory->getPath();

			$this->_copyFiles($fileList, $sp, $dp);
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the errors generated during loading or storing
	 *
	 * @return  array  The errors
	 */
	public function getErrors()
	{
		return $this->_errorList;
	}
	//------------------------------------------------
	/**
	 * Returns the warnings generated during loading or storing
	 *
	 * @return  array  The warnings
	 */
	public function getWarnings()
	{
		return $this->_warningList;
	}
	//------------------------------------------------
	/**
	 * Adds a group
	 *
	 * @param  Jadva_FaqList_Group|string  $in_group  The (name of the) group
	 *
	 * @return  Jadva_FaqList  Provides a fluent interface
	 */
	public function addGroup($in_group)
	{
		if( $in_group instanceof Jadva_FaqList_Group ) {
			$group = $in_group;
		} else {
			$group = new Jadva_FaqList_Group($in_group);
		}

		$this->_groupList[$group->id()] = $group;

		return $this;

	}
	//------------------------------------------------
	/**
	 * Returns the group with the given id
	 *
	 * @param  Jadva_FaqList_Group|string  The (id of the) group
	 *
	 * @pre  $this->hasGroup($group)
	 * @return  Jadva_FaqList_Group  The group
	 */
	public function getGroup($group)
	{
		if( $group instanceof Jadva_FaqList_Group ) {
			$groupId = $group->id();
		} else {
			$groupId = (string) $group;
		}

		if( !$this->hasGroup($groupId) ) {
			/** @see Jadva_FaqList_Exception */
			require_once 'Jadva/FaqList/Exception.php';
			throw new Jadva_FaqList_Exception(sprintf('Group with identity "%1$s" was not added to this list', $groupId));
		}

		return $this->_groupList[$group->id()];
	}
	//------------------------------------------------
	/**
	 * Returns the list of groups
	 *
	 * @return  array  The list of groups
	 */
	public function getGroups()
	{
		return $this->_groupList;
	}
	//------------------------------------------------
	/**
	 * Returns whether a given group has been added to this list yet
	 *
	 * @param  Jadva_FaqList_Group|string  $in_group  The (id of the) group
	 *
	 * @return  boolean  TRUE if the given group has been added to this list yet, FALSE otherwise
	 */
	public function hasGroup($in_group)
	{
		if( $in_group instanceof Jadva_FaqList_Group ) {
			$groupId = $in_group->id();
		} else {
			$groupId = (string) $in_group;
		}

		return array_key_exists($groupId, $this->_groupList);
	}
	//------------------------------------------------
	/**
	 * Adds a question to the given group
	 *
	 * @param  Jadva_FaqList_Question  $question  The question to add
	 * @param  Jadva_FaqList_Group     $group     The group to add the question to
	 *
	 * @return  boolean  TRUE if the given group has been added to this list yet, FALSE otherwise
	 */
	public function addQuestion(Jadva_FaqList_Question $question, Jadva_FaqList_Group $group)
	{
		$this->getGroup($group)->addQuestion($question);

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the questions for the given group
	 *
	 * @param  Jadva_FaqList_Group|string  $in_group  The (id of the) group
	 *
	 * @return  array  The questions
	 */
	public function getQuestions($in_group)
	{
		return $this->getGroup($in_group)->getQuestions();
	}
	//------------------------------------------------
	/**
	 * Contains the name of this FAQ list, when set
	 *
	 * @var  string|NULL
	 */
	protected $_name = NULL;
	//------------------------------------------------
	/**
	 * Contains the text on the home page of this FAQ list, when set
	 *
	 * @var  string|NULL
	 */
	protected $_homePageText = NULL;
	//------------------------------------------------
	/**
	 * Contains the list of groups
	 *
	 * @var  array
	 */
	protected $_groupList = array();
	//------------------------------------------------
	/**
	 * Contains the list of errors
	 *
	 * @var  array
	 */
	protected $_errorList = array();
	//------------------------------------------------
	/**
	 * Contains the list of warnings
	 *
	 * @var  array
	 */
	protected $_warningList = array();
	//------------------------------------------------
	/**
	 * Adds a error to the list
	 *
	 * @param  string  $message  The error
	 *
	 * @return  void
	 */
	protected function _error($message)
	{
		$this->_errorList[] = $message;
	}
	//------------------------------------------------
	/**
	 * Adds a warning to the list
	 *
	 * @param  string  $message  The warning
	 *
	 * @return  void
	 */
	protected function _warning($message)
	{
		$options = func_get_args();
		array_shift($options);

		$this->_warningList[] = vsprintf($message, $options);
	}
	//------------------------------------------------
	/**
	 * Converts the contents of a DOM Node to text
	 *
	 * @param  DOMNode $node  The node
	 *
	 * @return  string  The text
	 */
	protected function _domNodeToText(DOMNode $node)
	{
		if( 0 == $node->childNodes->length ) {
			return '';
		}

		if( 1 == $node->childNodes->length ) {
			$childNode = $node->childNodes->item(0);

			if( $childNode instanceof DOMCdataSection ) {
				return $node->textContent;
			}
			
			if( $childNode instanceof DOMText ) {
				return nl2br(trim($node->textContent));
			}

			//Dunno what node this is
		}

		return $node->ownerDocument->saveXML($node);
	}
	//------------------------------------------------
	/**
	 * Generates the HTML for the index.html file
	 *
	 * @return  string  The HTML
	 */
	protected function _generateIndex()
	{
		return $this->_generateHtmlHeader($this->getName())
			. $this->_generateMenu(NULL, 'home')
			. '<div id="main_content"><h2>' . $this->getName()
			. '</h2>'
			. ($this->hasHomePageText() ? $this->getHomePageText() : '<p>Select a group in the menu</p>')
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
		return $this->_generateHtmlHeader($this->getName())
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
		return $this->_generateHtmlHeader($this->getName(), $group)
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
		if( NULL === $group ) {
			$questionList = array();
			$questionToGroup = array();
			foreach($this->getGroups() as $itGroup) {
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
		$questionList = $group->getQuestions();
		if( 0 == count($questionList) ) {
			return 'There are no questions in this group';
		}

		$list = array();
		foreach($questionList as $question) {
			$html = '<dt><a name="q_' . $question->id() . '">';
			$html .= htmlspecialchars($question->question);
			$html .= '</a></dt>';

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

				$questionPrefix = NULL;
				if( !$group->hasQuestion($name) ) {
					foreach($this->getGroups() as $otherGroup) {
						if( $otherGroup->hasQuestion($name) ) {
							$questionPrefix = $otherGroup->id() . '.html';
							break;
						}
					}
				}

				$replaceList[]      = '/<qlink([^>*]href=")' . $name . '"/';
				$replaceMatchList[] = '<a\1' . $questionPrefix . '#q_' . $name . '"';
			}

			if( count($replaceList) ) {
				$replaceList      = array_unique($replaceList);
				$replaceMatchList = array_unique($replaceMatchList);
				$answer = preg_replace($replaceList, $replaceMatchList, $answer);
			}


			$answer = str_replace('</qlink', '</a', $answer);

			$matchesList = array(); $replaceList = array(); $replaceMatchList = array();
			$matchCount = preg_match_all('/<code[^>]*>/', $answer, $matchesList);
			foreach($matchesList[0] as $match) {
				if( empty($match) ) {
					continue;
				}

				if( FALSE !== strpos($match, 'lang="') ) {
					list($pre, $rest) = explode('lang="', $match);
					list($lang, $post) = explode('"', $rest, 2);

					$replaceList[]      = '/<code([^>*])lang="' . $lang . '"/';
					$replaceMatchList[] = '<div class="code ' . $lang . '-code"\1';
				}
			}

			if( count($replaceList) ) {
				$replaceList      = array_unique($replaceList);
				$replaceMatchList = array_unique($replaceMatchList);
				$answer = preg_replace($replaceList, $replaceMatchList, $answer);
			}

			//Simple <code> tags.
			$answer = str_replace('<code>', '<div class="code">', $answer);
			$answer = str_replace('</code>', '</div>', $answer);

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

			$answer = str_replace('<icon name="', '<img src="./icons/', $answer);

			$this->_readFiles($answer, $fileList);

			$html .= '<dd>' . $answer . '<div class="to_top"><a href="#top">Back to top</a></div></dd>';

			$list[] = $html;
		}

		return '<dl class="question_list">' . implode($list) . '</dl>';
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

		if( NULL < count($this->_groupList) ) {
			$html .= $this->_generateGroupHeadLink(reset($this->_groupList), 'first');

			if( $currentGroup ) {
				$keys = array_keys($this->_groupList);
				$currentGroupIndex = array_search($currentGroup->id(), $keys);

				if( 0 < $currentGroupIndex ) {
					$html .= $this->_generateGroupHeadLink($this->_groupList[$keys[$currentGroupIndex - 1]], 'prev');
				}

				if( $currentGroupIndex < count($this->_groupList) - 1 ) {
					$html .= $this->_generateGroupHeadLink($this->_groupList[$keys[$currentGroupIndex + 1]], 'next');
				}
			}

			$html .= $this->_generateGroupHeadLink(end($this->_groupList), 'last');
		}


		$html .= '</head><body>';
		$html .= '<h1><a name="top">' . htmlspecialchars($title) . '</a></h1>';

		return $html;
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
		$list = array();

		if( $this->hasHomePageText() ) {
			$list[] = '<li class="home ' . ('home' === $pageName ? 'active' : '') . '"><a href="./index.html">Home</a></li>';
		}

		foreach($this->getGroups() as $groupId => $group) {
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
	 * Copies the needed icons
	 *
	 * @param  array   $iconList        The list of icon names
	 * @param  string  $iconTargetPath  The path to copy the icons from
	 *
	 * @return  void
	 */
	protected function _copyIcons(array $iconList, $iconTargetPath)
	{
		if( 0 == count($iconList) ) {
			return;
		}

		if( NULL === self::$iconDirectory ) {
			$this->_warning('No icon input directory specified, cannot copy icons');
			return;
		}

		$directory = Jadva_File_Directory::getInstanceFor(self::$iconDirectory);

		if( !$directory->exists() ) {
			$this->_warning('Invalid icon input directory ' . $directory->getUrl());
			return;
		}

		foreach($iconList as $icon) {
			$iconFile = $directory->getFile($icon);

			if( !$iconFile->exists() ) {
				$this->_warning('Invalid icon file: ' . $iconFile->getUrl() . PHP_EOL);
				continue;
			}
			copy($iconFile->getUrl(), $iconTargetPath . $icon);
		}
	}
	//------------------------------------------------
	/**
	 * Reads the required files to copy
	 *
	 * @param  string   $html      The HTML to read from
	 * @param  array   &$fileList  The array to store the file list in
	 *
	 * @return  void
	 */
	protected function _readFiles($html, array &$fileList)
	{
		// Images used
		$matchesList = array();
		$matchCount = preg_match_all('/<img[^>]*>/', $html, $matchesList);
		foreach($matchesList[0] as $match) {
			if( empty($match) ) {
				continue;
			}

			if( FALSE === strpos($match, 'src="') ) {
				continue;
			}

			list(, $match) = explode('src="', $match);
			list($match) = explode('"', $match);

			if( FALSE !== strpos($match, '://') ) {
				continue;
			}

			$fileList[] = $match;
		}
	}
	//------------------------------------------------
	/**
	 * Copies the given files
	 *
	 * @param  array   $fileList  The list of files to copy
	 * @param  string  $sp        The source directory
	 * @param  string  $tp        The target directory
	 *
	 * @return  void
	 */
	protected function _copyFiles(array $fileList, $sp, $tp)
	{
		$fileList = array_unique($fileList);
		foreach($fileList as $file) {
			$filePath = realpath($sp . $file);
			if( FALSE === $filePath ) {
				$this->_warning('Files "%1$s" does not exist', $sp . $file);
				continue;
			}

			$targetPath = $tp . substr($filePath, strlen($sp));

			$dir = dirname($targetPath);
			$directory = Jadva_File_Directory::getInstanceFor($dir);
			$directory->ensureExistance();

			copy($filePath, $targetPath);
		}
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
