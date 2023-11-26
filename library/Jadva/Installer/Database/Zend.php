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
 * @subpackage Jadva_Installer_Database
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Zend.php 113 2009-03-21 10:40:10Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Installer_Database_Abstract */
require_once 'Jadva/Installer/Database/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Implements the class using the Zend connector
 *
 *
 * @todo       At the moment, this only works for MySQL, and not completely either
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Database
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Installer_Database_Zend extends Jadva_Installer_Database_Abstract
{
	//------------------------------------------------
	/**
	 * Sets the database adapter type
	 *
	 * @param  string  String name of base adapter class
	 *
	 * return  Jadva_Installer_Database_Zend  Provides a fluent interface
	 */
	public function setAdapterType($type)
	{
		$this->_dbAdapterType = trim($type);
		$this->_dbType        = strtolower($this->_dbAdapterType);
		if( substr($this->_dbType, 0, 4) === 'pdo_' ) {
			$this->_dbType    = substr($this->_dbType, 4);
		}

		if( 'mysqli' === $this->_dbType ) {
			$this->_dbType = 'mysql';
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Installs the database scripts
	 *
	 * Verifies the database type was set and returns control to the parent class
	 *
	 * @return void
	 */
	public function install()
	{
		if( NULL === $this->_dbType ) {
			throw new Exception('Database type unset');
		}

		return parent::install();
	}
	//------------------------------------------------
	/**
	 * Contains the database adapter
	 * @var  string
	 */
	protected $_dbAdapter            = NULL;
	//------------------------------------------------
	/**
	 * Contains the name of the database adapter type
	 * @var  string
	 */
	protected $_dbAdapterType        = NULL;
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_connectToDatabase */
	protected function _connectToDatabase()
	{
		try {
			$this->_dbAdapter = Zend_Db::factory($this->_dbAdapterType, array(
				'host'     => $this->_credentialsHost,
				'username' => $this->_credentialsUser,
				'password' => $this->_credentialsPass,
				'dbname'   => $this->_databaseName,
			));
			$this->_dbAdapter->getConnection();
		} catch(Zend_Exception $e) {
			require_once 'Jadva/Installer/Database/Exception.php';
			throw new Jadva_Installer_Database_Exception($e->getMessage());
		}

		$this->_outputFormatter->outputSuccess(
			'Connected to database "' . $this->_databaseName . '"'
			  . ' on host "' . $this->_credentialsHost . '"'
			  . ' using username "' . $this->_credentialsUser . '".'
		);
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_lockDatabase */
	protected function _lockDatabase()
	{
		//Can't lock database
		$this->_outputFormatter->outputNotice('Cannot lock databases; proceed with care.');
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_databaseIsLocked */
	protected function _databaseIsLocked()
	{
		//Couldn't lock database
		return FALSE;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_unlockDatabase */
	protected function _unlockDatabase()
	{
		//Couldn't lock database
		return;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_disconnectFromDatabase */
	protected function _disconnectFromDatabase()
	{
		$this->_dbAdapter->closeConnection();
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_checkVersionTable */
	protected function _checkVersionTable()
	{
		$tables = $this->_dbAdapter->listTables();

		if( !in_array('Jadva_Installer_database_table_versions', $tables) ) {
			$query = 'CREATE TABLE Jadva_Installer_database_table_versions ('
			       . '    scriptName    VARCHAR(255)     NOT NULL,'
			       . '    scriptVersion INTEGER UNSIGNED NOT NULL,'
			       . '    PRIMARY KEY(scriptName)'
			       . ');';
			$this->_executeQuery($query);
		}
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_getVersions */
	protected function _getVersions()
	{
		$return      = array();

		$results = $this->_executeQuery('SELECT * FROM jadva_installer_database_table_versions');

		if( isset($results->row) ) {
			foreach($results->row as $row) {
				$return[ $row['scriptName'] ] = $row['scriptVersion'];
			}
		}

		$this->_outputFormatter->outputSuccess('Retrieved information about the current versions.');

		return $return;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_executeDatabaseScript */
	protected function _executeDatabaseScript($script)
	{
		if( !is_array($script) ) {
			$script = array($script);
		}

		foreach($script as $query) {
			$this->_executeQuery($query);
		}
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_storeVersions */
	protected function _storeVersions(array $version_list)
	{
		try { 
			foreach($version_list as $scriptName => $scriptVersion) {
				$updatedRowCount = $this->_dbAdapter->update(
					'jadva_installer_database_table_versions',
					array(
						'scriptVersion' => $scriptVersion,
					),
					$this->_dbAdapter->quoteIdentifier('scriptName') . ' = ' . $this->_dbAdapter->quote($scriptName)
				);

				if( 0 === $updatedRowCount ) {
					$this->_dbAdapter->insert(
						'jadva_installer_database_table_versions',
						array(
							'scriptName'    => $scriptName,
							'scriptVersion' => $scriptVersion,
						)
					);
				}
			}
		} catch(Zend_Db_Statement_Exception $e) {
			require_once 'Jadva/Installer/Database/Exception.php';
			throw new Jadva_Installer_Database_Exception($e->getMessage());
		}

		$this->_outputFormatter->outputInfo('Store information about the new versions');
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_createRestorePoint */
	protected function _createRestorePoint()
	{
		$parameter_list = array(
			'add-drop-table' => NULL,
			'add-locks'      => NULL,
			'user'           => $this->_credentialsUser,
			'password'       => $this->_credentialsPass,
			'host'           => $this->_credentialsHost,
			$this->_databaseName,
		);

		$command = 'mysqldump';
		foreach($parameter_list as $key => $value ) {
			if( is_int($key) ) {
				$command .= ' ' . $value;
			} else {
				if( NULL === $value ) {
					$command .= ' --' . $key;
				} else {
					$command .= ' --' . $key . '="' . $value . '"';
				}
			}
		}

		$filename = $this->_restoreDirectory . $this->_databaseName . '_' . date('YmdHis') . '.mysql';

		$pipes     = array();
		$process   = proc_open(
			$command,
			array(
				1 => array('file', $filename, 'w'),
				2 => array('pipe', 'w'),
			),
			$pipes,
			$this->_restoreDirectory
		);

		if( !is_resource($process) ) {
			require_once 'Jadva/Installer/Database/Exception.php';
			throw new Jadva_Installer_Database_Exception('Could not create mysqldump process');
		}

		$errors = stream_get_contents($pipes[2]);
		// It is important that you close any pipes before calling
		// proc_close in order to avoid a deadlock
		fclose($pipes[2]);

		$returnVar = proc_close($process);

		if( 0 !== $returnVar ) {
			$this->_outputFormatter->outputErr($errors);
			require_once 'Jadva/Installer/Database/Exception.php';
			throw new Jadva_Installer_Database_Exception('mysqldump returned with exit code "' . $returnVar . '".');
		}

		$this->_restorePointLocation = $filename;
		$this->_listTablesBefore     = $this->_dbAdapter->listTables();

		$this->_outputFormatter->outputSuccess('Restore point created, and stored in "' . $filename . '".');
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_restoreRestorePoint */
	protected function _restoreRestorePoint()
	{
		//There's no way we can determine all the things that went wrong
		// So we wipe the database and recreate it from the restore point
		$this->_executeQuery('DROP DATABASE ' . $this->_databaseName);
		$this->_executeQuery('CREATE DATABASE ' . $this->_databaseName);

		$parameter_list = array(
			'user'           => $this->_credentialsUser,
			'password'       => $this->_credentialsPass,
			$this->_databaseName,
		);

		$command = 'mysql';
		foreach($parameter_list as $key => $value ) {
			if( is_int($key) ) {
				$command .= ' ' . $value;
			} else {
				if( NULL === $value ) {
					$command .= ' --' . $key;
				} else {
					$command .= ' --' . $key . '="' . $value . '"';
				}
			}
		}

		$pipes     = array();
		$process   = proc_open(
			$command,
			array(
				0 => array('file', $this->_restorePointLocation, 'r'),
				2 => array('pipe', 'w'),
			),
			$pipes,
			$this->_restoreDirectory
		);

		if( !is_resource($process) ) {
			require_once 'Jadva/Installer/Database/Exception.php';
			throw new Jadva_Installer_Database_Exception('Could not create mysqldump process');
		}

		$errors = stream_get_contents($pipes[2]);
		// It is important that you close any pipes before calling
		// proc_close in order to avoid a deadlock
		fclose($pipes[2]);

		$returnVar = proc_close($process);

		if( 0 !== $returnVar ) {
			$this->_outputFormatter->outputErr($errors);
			require_once 'Jadva/Installer/Database/Exception.php';
			throw new Jadva_Installer_Database_Exception('Could not restore restorepoint');
		}
		
		$this->_outputFormatter->outputSuccess('Restorepoint restored');
	}
	//------------------------------------------------
	// Helper functions
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_parseDatabaseScript */
	protected function _parseDatabaseScript($scriptContents)
	{
		$return = array();
		$return['requirement_list'] = array();
		$return['query_list']       = array();

		$line_list = explode("\n", $scriptContents);
		$delimeter = ';';

		$current_query = '';

		foreach($line_list as $line) {
			$line = trim($line);

			if( empty($line) ) {
				continue;
			}

			$comment = '';

			if( ('-' === $line[0]) && ('-' === $line[1]) ) {
				//Line in singleline comment
				$comment = trim(substr($line, 2));
			}

			if( '#' === $line[0] ) {
				//Line in singleline comment
				$comment = trim(substr($line, 2));
			}

			if( !empty($comment) ) {
				if( substr($comment, 0, 9) === 'REQUIRES:' ) {
					$requirement = substr($comment, 9);
					if( FALSE === strpos($requirement, ',') ) {
						throw new Exception('Error at line ' . $lineNumber . ': Invalid requirement');
					}

					list($scriptName, $scriptVersion) = explode(',', $requirement);
					$return['requirement_list'][] = array(
						'scriptName'    => $scriptName,
						'scriptVersion' => $scriptVersion,
					);
				}
				continue;
			}

			if( substr($line, 0, 9) === 'DELIMITER' ) {
				$delimeter = trim(substr($line, 9));
				continue;
			}

			if( FALSE !== ($delPos = strpos($line, $delimeter)) ) {
				$current_query .= substr($line, 0, $delPos);
				$return['query_list'][] = $current_query;

				$current_query = trim(substr($line, $delPos + 1)) . PHP_EOL;;
				continue;
			}

			$current_query .= $line . PHP_EOL;
		}

		return $return;
	}
	//------------------------------------------------
	/**
	 * Executes a query
	 *
	 * @param  string  $query  The query to execute
	 *
	 * @return  ClassInterface Zend_Db_Statement_Interface  The results of the query
	 */
	protected function _executeQuery($query)
	{
		$this->_dbAdapter->query($query);
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------

