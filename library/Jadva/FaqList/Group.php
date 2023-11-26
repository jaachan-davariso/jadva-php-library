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
 * @version    $Id: Group.php 301 2010-01-09 14:50:29Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * Class to represent one FAQ question group
 *
 * @category   JAdVA
 * @package    Jadva_FaqList
 * @subpackage Jadva_FaqList
 */
class Jadva_FaqList_Group
{
	//------------------------------------------------
	/**
	 * Prefixed for unique ids
	 */
	const ID_PREFIX = 'jadva_faqgrp_';
	//------------------------------------------------
	/**
	 * The name of the group
	 *
	 * @var  string|NULL
	 */
	public $name = NULL;
	//------------------------------------------------
	/**
	 * Returns the identity of this group
	 *
	 * If all instances are created without specifying an id, this is guaranteed to be unique
	 *
	 * @return  string  The identity
	 */
	public function id()
	{
		return $this->_id;
	}
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @param  string  $name  The name of the group
	 * @param  string  $id        (OPTIONAL) The id, will be generated if necessary
	 */
	public function __construct($name, $id = NULL)
	{
		$this->name = (string) $name;

		if( empty($id) ) {
			$this->_id = self::_generateGroupId();
		} else {
			$this->_id = $id;
		}
	}
	//------------------------------------------------
	/**
	 * Adds a question to this group
	 *
	 * @param  Jadva_FaqList_Question  $question  The question to add
	 *
	 * @return  Jadva_FaqList_Question  Provides a fluent interface
	 */
	public function addQuestion(Jadva_FaqList_Question $question)
	{
		$this->_questionList[$question->id()] = $question;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the question with the given identity
	 *
	 * @param  string  $questionId  The identity of the question
	 *
	 * @pre  $this->hasQuestion($questionId)
	 * @return  Jadva_FaqList_Question  The question
	 */
	public function getQuestion($questionId)
	{
		if( !$this->hasQuestion($questionId) ) {
			/** @see Jadva_FaqList_Exception */
			require_once 'Jadva/FaqList/Exception.php';
			throw new Jadva_FaqList_Exception(sprintf('There is no question with identity "%1$s" in this group', $questionId));
		}

		return $this->_questionList[$questionId];
	}
	//------------------------------------------------
	/**
	 * Returns the list of questions for this group
	 *
	 * @return  array  The questions
	 */
	public function getQuestions()
	{
		return $this->_questionList;
	}
	//------------------------------------------------
	/**
	 * Returns whether the question with the given identity has been added to this group
	 *
	 * @param  string  $questionId  The identity of the question
	 *
	 * @return  boolean  TRUE if the question with the given identity has been added to this group, FALSE otherwise
	 */
	public function hasQuestion($questionId)
	{
		return array_key_exists($questionId, $this->_questionList);
	}
	//------------------------------------------------
	/**
	 * Returns whether this group has questions
	 *
	 * @return  boolean  TRUE if questions have been added to this group, FALSE otherwise
	 */
	public function hasQuestions()
	{
		return 0 < count($this->_questionList);
	}
	//------------------------------------------------
	/**
	 * Contains the identity
	 *
	 * @var  string|NULL
	 */
	protected $_id = NULL;
	//------------------------------------------------
	/**
	 * Contains the list of questions
	 *
	 * @var  array
	 */
	protected $_questionList = array();
	//------------------------------------------------
	/**
	 * Generated a unique question id
	 *
	 * @return  string  The id
	 */
	protected static function _generateGroupId()
	{
		static $groupId = 0x0FF;

		$groupId++;

		return self::ID_PREFIX . sprintf('%0x', $groupId);
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
