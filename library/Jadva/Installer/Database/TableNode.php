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
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Database_ScriptLists
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: TableNode.php 357 2010-09-24 11:50:34Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class represents a single Database Script version
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Database_ScriptLists
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Installer_Database_TableNode extends Jadva_Tc_Node
{
	//------------------------------------------------
	/**
	 * Contains the script name and version of the script that causes this one to be skipped, or FALSE when it
	 * shouldn't be skipped
	 *
	 * @var  array|false
	 */
	public $skipReason = FALSE;
	//------------------------------------------------
	/**
	 * Returns the database type this script is for
	 *
	 * @return  string  The database type this script is for
	 */
	public function getDbType()
	{
		return $this->_dbType;
	}
	//------------------------------------------------
	/**
	 * Sets the database type this script is for
	 *
	 * @param  string  $dbType  The database type this script is for
	 *
	 * @return  Jadva_Installer_Database_TableNode  Provides a fluent interface
	 */
	public function setDbType($dbType)
	{
		$this->_dbType = strtolower(trim((string) $dbType));

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the name of the database script
	 *
	 * @return  string  The name of the database script
	 */
	public function getScriptName()
	{
		return $this->_scriptName;
	}
	//------------------------------------------------
	/**
	 * Sets the name of the database script
	 *
	 * @param  string  $scriptName  The name of the script
	 *
	 * @return  Jadva_Installer_Database_TableNode  Provides a fluent interface
	 */
	public function setScriptName($scriptName)
	{
		$this->_scriptName = trim((string) $scriptName);

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the version of the database script
	 *
	 * @return  integer  The version of the database script
	 */
	public function getScriptVersion()
	{
		return $this->_scriptVersion;
	}
	//------------------------------------------------
	/**
	 * Sets the version of the database script
	 *
	 * @param  integer  $scriptVersion  The version of the script
	 *
	 * @return  Jadva_Installer_Database_TableNode  Provides a fluent interface
	 */
	public function setScriptVersion($scriptVersion)
	{
		if( $scriptVersion < 1 ) {
			require_once 'Jadva/Installer/Database/Exception.php';
			throw new Jadva_Installer_Database_Exception('Script version must be 1 or larger');
		}
		$this->_scriptVersion = (int) trim($scriptVersion);


		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the filename of the database script
	 *
	 * @return  string  The filename of the database script
	 */
	public function getFilename()
	{
		return $this->_scriptName . '.' . $this->_scriptVersion . '.' . $this->_dbType;
	}
	//------------------------------------------------
	/**
	 * Returns the content of the database script
	 *
	 * @return  array  The list of queries for this database script
	 */
	public function getContent()
	{
		return $this->_content;
	}
	//------------------------------------------------
	/**
	 * Returns TRUE if this node has the content of the database script set, FALSE otherwise
	 *
	 * @return  boolean  TRUE if this node has the content of the database script set, FALSE otherwise
	 */
	public function hasContent()
	{
		return NULL !== $this->_content;
	}
	//------------------------------------------------
	/**
	 * Sets the content of the database script
	 *
	 * @param  array  $content  The list of queries for this database script
	 *
	 * @return  Jadva_Installer_Database_TableNode  Provides a fluent interface
	 */
	public function setContent($content)
	{
		$this->_content = (array) $content;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns whether this node is essential for a complete installation
	 *
	 * @return  boolean  TRUE when this node is essential for a complete installation, FALSE otherwise
	 */
	public function getIsEssential()
	{
		return $this->_isEssential;
	}
	//------------------------------------------------
	/**
	 * Sets whether this node is essential
	 *
	 * @param  boolean  $isEssential  Whether this node is essential
	 *
	 * @return  Jadva_Installer_Database_TableNode  Provides a fluent interface
	 */
	public function setIsEssential($isEssential)
	{
		$this->_isEssential = (boolean) $isEssential;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Contains the database type this script is for
	 * @var  string
	 */
	protected $_dbType        = NULL;
	//------------------------------------------------
	/**
	 * Contains the name of the database script
	 * @var  string
	 */
	protected $_scriptName    = NULL;
	//------------------------------------------------
	/**
	 * Contains the version of the database script
	 * @var  integer
	 */
	protected $_scriptVersion = NULL;
	//------------------------------------------------
	/**
	 * Contains the content of the database script
	 * @var  array
	 */
	protected $_content       = NULL;
	//------------------------------------------------
	/**
	 * Contains whether this node is essential
	 * @var  boolean
	 */
	protected $_isEssential   = TRUE;
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
