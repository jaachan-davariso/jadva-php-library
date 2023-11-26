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
 * @package    Jadva_Tc
 * @subpackage Jadva_Tc_Object
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Mysqli.php 39 2008-09-25 13:59:21Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class represenst an object
 *
 * A lot of the TC objects link to each other, leading to circular references. This class tries to help, by creating
 * a sort of 'state' for an object. Also adds a few helper functions, like the automatical __get for getVarname
 * functions.
 *
 * @category   JAdVA
 * @package    Jadva_Tc
 * @subpackage Jadva_Tc_Object
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
abstract class Jadva_Tc_Object
{
	//------------------------------------------------
	/**
	 * An object in this state is very much alive
	 */
	const STATE_ALIVE  = 'stateAlive';
	//------------------------------------------------
	/**
	 * An object in this state has been called to be deleted, but hasn't finished the deletion yet
	 */
	const STATE_DIEING = 'stateDieing';
	//------------------------------------------------
	/**
	 * An object in this state has been called to be deleted, and has finished the deletion. Should not be used
	 * anymore, so the garbage collector can come pick it up
	 */
	const STATE_DEAD   = 'stateDead';
	//------------------------------------------------
	/**
	 * General variable to store data in a node
	 *
	 * Not changed by the Tc classes. Also not cleaned on delete.
	 *
	 * @var  mixed
	 */
	public $data;
	//------------------------------------------------
	/**
	 * Default constructor.
	 *
	 * Generates an class-unique identity
	 */
	public function __construct()
	{
		static $identity_list = array();

		$class = get_class($this);
		if( !array_key_exists($class, $identity_list) ) {
			$identity_list[$class] = 0;
		}

		$this->_identity = $identity_list[$class];
		$identity_list[$class]++;

		$this->_aliveState = self::STATE_ALIVE;
	}
	//------------------------------------------------
	/**
	 * Default destructor
	 *
	 * Calls upon {@link delete()} if still alive
	 */
	public function __destruct()
	{
		if( self::STATE_DEAD !== $this->_aliveState ) {
			$this->delete();
		}
	}
	//------------------------------------------------
	/**
	 * Returns the identity of this object
	 *
	 * @return  The identity of this object
	 */
	public function getIdentity()
	{
		return $this->_identity;
	}
	//------------------------------------------------
	/**
	 * Deletes this object, removing any references to other objects
	 */
	public function delete()
	{
		if( self::STATE_DEAD === $this->_aliveState ) {
			return;
		}

		if( self::STATE_DIEING === $this->_aliveState ) {
			return;
		}
		$this->_aliveState = self::STATE_DIEING;

		$this->_innerDelete();

		$this->_aliveState = self::STATE_DEAD;
	}
	//------------------------------------------------
	/**
	 * Calls upon the set{$varName} function if exists, triggers an error otherwise
	 *
	 * @param  string  $varName   The name of the variable that was tried to set
	 * @param  mixed   $newValue  The value that it was tried to set to
	 *
	 * @return mixed
	 */
	public function __set($varName, $newValue) {
		$function = 'set' . ucfirst($varName);
		if( method_exists($this, $function) ) {
			return $this->$function($newValue);
		}

		trigger_error('Class ' . get_class($this) . ' has no property called "' . $varName .'"', E_USER_WARNING);
	}
	//------------------------------------------------
	/**
	 * Calls upon the get{$varName} function if exists, triggers an error otherwise
	 *
	 * @param  string  $varName   The name of the variable that was tried to get
	 *
	 * @return mixed
	 */
	public function __get($varName) {
		$function = 'get' . ucfirst($varName);
		if( method_exists($this, $function) ) {
			return $this->$function();
		}

		trigger_error('Class ' . get_class($this) . ' has no property called "' . $varName .'"', E_USER_WARNING);
	}
	//------------------------------------------------
	/**
	 * Throws an exception if the object is already dead
	 *
	 * @throws  Jadva_Tc_Exception  If the object is already dead
	 */
	protected function _bailIfDead()
	{
		if( self::STATE_DEAD === $this->_aliveState ) {
			require_once 'Jadva/Tc/Exception.php';
			throw new Jadva_Tc_Exception('This object is dead and can therefore not be accessed anymore');
		}
	}
	//------------------------------------------------
	/**
	 * Gets called when the object is called to delete
	 *
	 * @return  void
	 */
	abstract protected function _innerDelete();
	//------------------------------------------------
	/**
	 * Contains the class-unique identity of this object
	 *
	 * @var  integer
	 */
	private $_identity;
	//------------------------------------------------
	/**
	 * Contains the liveness-state of this object
	 *
	 * @var  string
	 */
	private $_aliveState;
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
