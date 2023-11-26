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
 * @version    $Id: FaqList.php 379 2011-10-01 09:20:43Z jaachan $
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
	 * @param  string  $xmlFileName  (OPTIONAL) The location of the XML file, if any
	 *
	 * @return  Jadva_FaqList  Provides a fluent interface
	 */
	public function loadXml($xml, $xmlFileName = NULL)
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
			$groupName = $groupNode->getAttribute('name');
			$groupId   = $groupNode->getAttribute('id');
			$grooup    = NULL;

			if( $groupId && $this->hasGroup($groupId) ) {
				$group = $this->getGroup($groupId);
			} elseif( $groupName ) {
				$group = $this->lookupGroup($groupName);
			}

			if( !$group ) {
				$group = new Jadva_FaqList_Group($groupName, $groupId);
				$this->addGroup($group);
			}

			$xpath = new DOMXpath($document);
			$questionNodeList = $xpath->query('./question', $groupNode);
			for($itQuestEl = 0; $itQuestEl < $questionNodeList->length; $itQuestEl++) {
				$questionNode = $questionNodeList->item($itQuestEl);
				$question = new Jadva_FaqList_Question(
					$questionNode->getAttribute('text'),
					$this->_domNodeToText($questionNode),
					$questionNode->getAttribute('id')
				);

				$this->addQuestion($question, $group, $xmlFileName);
			}
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Converts this FAQ list to a website
	 *
	 * Note that for copying files or the style sheet, you need a source directory.
	 *
	 * @param  Jadva_File_Directory|string  $targetDirectory  The directory to store the files in
	 * @param  Jadva_File_Directory|string  $sourceDirectory  (OPTIONAL) The directory to copy files from, if any
	 * @param  string                       $styleSheet       (OPTIONAL) The name of the stylesheet file, if any
	 *
	 * @return  Jadva_FaqList  Provides a fluent interface
	 */
	public function toHtml($targetDirectory, $sourceDirectory = NULL, $styleSheet = NULL)
	{
		$this->render('MultiPageHtml', $targetDirectory, $sourceDirectory, $styleSheet);
	}
	//------------------------------------------------
	/**
	 * Renders the FaqList with the given renderer
	 *
	 * @param  Jadva_FaqList_Renderer_Abstract|string  $renderer
	 *         The (name of the) renderer.
	 * @param  Jadva_File_Directory|string  $targetDirectory
	 *         (OPTIONAL) The directory to store the files in. Require when invoking a renderer by name
	 * @param  Jadva_File_Directory|string  $sourceDirectory
	 *         (OPTIONAL) The directory to copy files from, if any
	 * @param  string  $styleSheet
	 *         (OPTIONAL) The name of the stylesheet file, if any
	 *
	 * @return  Jadva_FaqList  Provides a fluent interface
	 */
	public function render($renderer, $targetDirectory = NULL, $sourceDirectory = NULL, $styleSheet = NULL)
	{
		if( !($renderer instanceof Jadva_FaqList_Renderer_Abstract) ) {
			$className = 'Jadva_FaqList_Renderer_' . $renderer;

			require_once str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

			$renderer = new $className($this, $targetDirectory);
		}

		if( $targetDirectory ) {
			$renderer->setOutputDirectory($targetDirectory);
		}

		if( $sourceDirectory ) {
			$renderer->setSourceDirectory($sourceDirectory);
			if( $styleSheet ) {
				$renderer->setStyleSheet($styleSheet);
			}
		}
		$renderer->render();

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
	 * Adds a warning to the list
	 *
	 * @param  string  $message  The message
	 * @parma  mixed   ...       The message params
	 *
	 * @return  Jadva_FaqList  Provides a fluent interface
	 */
	public function addWarning($message)
	{
		$args = func_get_args();

		call_user_func_array(array($this, '_warning'), $args);

		return $this;
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

		return $this->_groupList[$groupId];
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
	 * Find a group by its name
	 *
	 * @param  string  $in_groupName  The name of the group to find
	 *
	 * @return  Jadva_FaqList_Group|NULL  The group, or NULL if no group with that name was added
	 */
	public function lookupGroup($in_groupName)
	{
		$groupName = (string) $in_groupName;

		foreach($this->_groupList as $group) {
			if( $group->name == $groupName ) {
				return $group;
			}
		}

		return NULL;
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
	public function addQuestion(Jadva_FaqList_Question $question, Jadva_FaqList_Group $group, $fileFound = NULL)
	{
		if( array_key_exists($question->id(), $this->_questionMap) ) {
			$firstInfo = $this->_questionMap[$question->id()];
			$first = $firstInfo['group']->name . ' (' . $firstInfo['group']->id();
			if( $firstInfo['file'] ) {
				$first .= ', ' . $firstInfo['file'];
			}
			$first .= ')';

			$later = $group->name . ' (' . $group->id();
			if( $fileFound ) {
				$later .= ', ' . $fileFound;
			}
			$later .= ')';

			$this->_error(sprintf(
				'Double question id found: %1$s' . "\n"
				. ' First defined in %2$s' . "\n"
				. ' Later also defined in %3$s',
				$question->id(), $first, $later
			));

			return $this;
		}

		$this->_questionMap[$question->id()] = array(
			'group' => $group,
			'file'  => $fileFound,
		);

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
	 * Contains a mapping from question to (id of the) group they belong in and file they were found in
	 *
	 * @var  array
	 */
	protected $_questionMap = array();
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

		$document = $node->ownerDocument;

		$return = array();
		for($itEl = 0; $itEl < $node->childNodes->length; $itEl++) {
			$child = $node->childNodes->item($itEl);

			$return[] = trim($document->saveXML($child));
		}

		return implode(" ", $return);
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
