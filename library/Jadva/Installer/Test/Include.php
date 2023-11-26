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
 * @subpackage Jadva_Installer_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Include.php 63 2009-01-17 15:53:57Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * Represents an include test
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Test
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Installer_Test_Include
{
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @param  string  $in_include  The include to test for
	 * @param  string  $in_name     The name of the class or function that should exist after the inclusion
	 * @param  string  $in_type     Whether to test for a class or a function. Defaults to class.
	 *
	 */
	public function __construct($in_include, $in_name, $in_type = 'class')
	{
		if( NULL === $in_type ) {
			$in_type = 'class';
		}

		$this->_include = (string) $in_include;
		$this->_name    = (string) $in_name;

		if( ('class' !== $in_type) && ('function' !== $in_type) ) {
			/** @see Jadva_Installer_Exception */
			require_once 'Jadva/Installer/Exception.php';
			throw new Jadva_Installer_Exception('Include type must be either "class" or "function", "' . $in_type . '" given.');
		}

		$this->_type    = (string) $in_type;
	}
	//------------------------------------------------
	/**
	 * Returns the include to test for
	 *
	 * @return  string  The include to test for
	 */
	public function getInclude()
	{
		return $this->_include;
	}
	//------------------------------------------------
	/**
	 * Returns the name of the class or function that should exist after the inclusion
	 *
	 * @return  string  The name of the class or function that should exist after the inclusion
	 */
	public function getName()
	{
		return $this->_name;
	}
	//------------------------------------------------
	/**
	 * Returns the type of the item to test for
	 *
	 * @return  string  The type of the item to test for
	 */
	public function getType()
	{
		return $this->_type;
	}
	//------------------------------------------------
	/**
	 * Executes the test
	 *
	 * @return  boolean  TRUE if the include was possible for the current include path, FALSE otherwise
	 */
	public function test()
	{
		$fileRealPath = realpath($this->_include); //Perhaps it's a full path. It better not be though.
		if( FALSE === $fileRealPath ) {
			$include = $this->_include;
			$include = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $include);

			$includePathList = explode(PATH_SEPARATOR, get_include_path());
			foreach($includePathList as $includePath) {
				$path = $includePath;
				$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
				$path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

				if( file_exists($path . $include) ) {
					$this->_pathList[] = $path . $include;
					require_once $path . $include;
				}
			}
		} else {
			require_once $fileRealPath;
		}

		switch( $this->_type ):
		case 'class':
			if( !class_exists($this->_name, FALSE) ) {
				return FALSE;
			}
			break;
		case 'function':
			if( !function_exists($this->_name) ) {
				return FALSE;
			}
			break;
		endswitch;

		return TRUE;
	}
	//------------------------------------------------
	/**
	 * After executing the {@link test}(), this function returns the list of files included
	 *
	 * @return  array  The list of files included
	 */
	public function getPathList()
	{
		return $this->_pathList;
	}
	//------------------------------------------------
	/**
	 * Contains the include to test for
	 *
	 * @var  string
	 */
	protected $_include;
	//------------------------------------------------
	/**
	 * Contains the name of the class or function that should exist after the inclusion
	 *
	 * @var  string
	 */
	protected $_name;
	//------------------------------------------------
	/**
	 * Contains the type of the item to test for
	 *
	 * @var  string
	 */
	protected $_type;
	//------------------------------------------------
	/**
	 * Contains the 
	 *
	 * @var  array
	 */
	protected $_pathList = array();
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
