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
 * @version    $Id: Question.php 301 2010-01-09 14:50:29Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * Class to represent one FAQ question
 *
 * @category   JAdVA
 * @package    Jadva_FaqList
 * @subpackage Jadva_FaqList
 */
class Jadva_FaqList_Question
{
	//------------------------------------------------
	/**
	 * Prefixed for unique ids
	 */
	const ID_PREFIX = 'jadva_faqqst_';
	//------------------------------------------------
	/**
	 * Contains the question
	 *
	 * @var  string|NULL
	 */
	public $question = NULL;
	//------------------------------------------------
	/**
	 * Contains the answer
	 *
	 * @var  string|NULL
	 */
	public $answer = NULL;
	//------------------------------------------------
	/**
	 * Returns the identity of this question
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
	 * @param  string  $question  The question
	 * @param  string  $answer    The answer
	 * @param  string  $id        (OPTIONAL) The id, will be generated if necessary
	 */
	public function __construct($question, $answer, $id = NULL)
	{
		$this->question = (string) $question;
		$this->answer = (string) $answer;

		if( empty($id) ) {
			$this->_id = self::_generateQuestionId();
		} else {
			$this->_id = (string) $id;
		}
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
	 * Generated a unique question id
	 *
	 * @return  string  The id
	 */
	protected static function _generateQuestionId()
	{
		static $questionId = 0x0FF;

		$questionId++;

		return self::ID_PREFIX . sprintf('%0x', $questionId);
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
