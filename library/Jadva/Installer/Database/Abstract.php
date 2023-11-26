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
 * @copyright  Copyright (c) 2009 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Abstract.php 245 2009-08-21 09:02:58Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Installer_Database_TableNode_List */
require_once 'Jadva/Installer/Database/TableNode/List.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class can be used to install database scripts.
 *
 * The advantage of this class is that you can build up your scripts in purely SQL fashion, and then execute them. You
 * can state for each script the names and versions of the other scripts that are used. The filenames must be
 *
 * <scriptName>.<scriptVersion>.<databaseType>
 *
 * So for the Jadva_Installer_Database_Mysqli, the filenames could be as follows:
 * <ul>
 *   <li>users.1.mysql</li>
 *   <li>users.2.mysql</li>
 *   <li>user_logs.1.mysql</li>
 * </ul>
 *
 * Note that each version will automatically depend on the versions before them, allowing you to update the tables etc
 * you created in the first version. The version numbers will be stored in the database, allowing you to update your
 * database at a later point in time. Version numbers are required to start at 1, and increase with 1 at each version.
 *
 * Example:
 * <code>
 * $outputFormatter = new Jadva_Installer_OutputFormatter_Xhtml;
 * $outputFormatter->setTitle('Database installer example');
 * 
 * $installer = new Jadva_Installer_Database_Mysqli;
 * $installer->setOutputFormatter($outputFormatter)
 *           ->setRestoreDirectory('./restore/')
 *           ->setCredentials('db_user_name', 'db_user_pass', 'localhost')
 *           ->setDatabaseName('db_name')
 *           ->addDirectory('./forum/installationscripts/')
 *           ->addDirectory('./blog/installationscripts/')
 *           ->install();
 * </code>
 *
 * This example will load the database scripts from './forum/installationscripts/' and './blog/installationscripts/', 
 * make a backup of the database in './restore/', and execute the scripts against the database with 'db_name', after
 * logging in to 'localhost' with 'db_user_name' and 'db_user_pass'. If the installation goes wrong, the backup will be
 * restored.
 *
 * Requirements for the Jadva_Installer_Database_Mysqli are annotated in the .mysql files as follows:
 * <ul>
 *   <li>Start a line with a single-line comment symbol (-- or #)</li>
 *   <li>Then state 'REQUIRES:' (with no space between "REQUIRES" and the ":")</li>
 *   <li>Then state the name the script depends on</li>
 *   <li>Then state a comma (",")</li>
 *   <li>Then state the version number the script depends on</li>
 * </ul>
 *
 * Support for other DBMSs is not written yet; there is a {@link Jadva_Installer_Database_Zend} class which connects
 * using the Zend_Db_Adapter you specifiy, however, this has been untested, and creating a backup might not even be
 * possible while using Zend. Therefore, the above stated requirements for MySQLi should be generalised to other DBMSs,
 * but this is not written yet.
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Database
 */
//----------------------------------------------------------------------------------------------------------------------
abstract class Jadva_Installer_Database_Abstract
{
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * Allows for setting the options on construction time
	 *
	 * @param  array  $options  The options to set
	 */
	public function __construct(array $options = array())
	{
		foreach($options as $optionName => $optionValue) {
			$methodName = 'set' . ucfirst($optionName);
			if( method_exists($this, $methodName) ) {
				$this->$methodName($optionValue);
			}
		}
	}
	//------------------------------------------------
	/**
	 * Sets the output formatter instance
	 *
	 * @param  Jadva_Installer_OutputFormatter_Interface  $outputFormatter  The output formatter instance
	 *
	 * @return  Jadva_Installer_Database  Provides a fluent interface
	 */
	public function setOutputFormatter(Jadva_Installer_OutputFormatter_Interface $outputFormatter)
	{
		$this->_outputFormatter = $outputFormatter;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Sets the directory to store the restore point in
	 *
	 * Note that this function ATM only takes full paths
	 *
	 * @param  string  $directory  The directory to store the restore point in
	 *
	 * @todo  Fix bug with relative paths.
	 * @return  Jadva_Installer_Database  Provides a fluent interface
	 */
	public function setRestoreDirectory($directory)
	{
		$directory = $this->_checkDirectory($directory, TRUE);

		$this->_restoreDirectory = $directory;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Sets the database connection credentials
	 *
	 * @param  string  $username  The username to use for connecting
	 * @param  string  $password  The password to use for connecting
	 * @param  string  $host      The host to connect to. Defaults to 'localhost'
	 * @param  string  $port      (OPTIONAL) The port to connect to
	 * @param  string  $socket    (OPTIONAL) The socket to connect to
	 *
	 * @return  Jadva_Installer_Database  Provides a fluent interface
	 */
	public function setCredentials($username, $password, $host = 'localhost', $port = NULL, $socket = NULL)
	{
		$this->_credentialsUser = (string) $username;
		$this->_credentialsPass = (string) $password;
		$this->_credentialsHost = (string) $host;
		$this->_credentialsPort = NULL === $port ? NULL : (int) $port;
		$this->_credentialsSock = NULL === $socket ? NULL : (int) $socket;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Sets the database name to install into
	 *
	 * @param  string  $databaseName  The database name to install into
	 *
	 * @return  Jadva_Installer_Database  Provides a fluent interface
	 */
	public function setDatabaseName($databaseName)
	{
		$this->_databaseName = (string) $databaseName;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Adds a directory with installation scripts
	 *
	 * @param  string  $directory  The directory with installation scripts to add
	 *
	 * @return  Jadva_Installer_Database  Provides a fluent interface
	 */
	public function addDirectory($directory)
	{
		$this->_directoryList[] = $this->_checkDirectory($directory);

		return $this;
	}
	//------------------------------------------------
	/**
	 * Installs the database scripts
	 *
	 * This function loads the installation script from the added directories, connects to the database and tries
	 * to lock it. Then it creates a restore point, orders the scripts, checks which versions are installed and
	 * updates the database. Finally, the database will be unlocked.
	 *
	 * If something goes wrong, it will try to restore the restore point. Should that fail, it will output the
	 * location of the restore point, so the user can restore the database themselves
	 *
	 * @return void
	 */
	public function install()
	{
		if( NULL === $this->_dbType ) {
			throw new Exception('Database type unset');
		}

		if( NULL === $this->_outputFormatter ) {
			throw new Exception('Output formatter unset');
		}

		if( NULL === $this->_restoreDirectory ) {
			throw new Exception('Restore directory unset');
		}

		if( NULL === $this->_credentialsUser ) {
			throw new Exception('Credentials unset');
		}

		if( NULL === $this->_databaseName ) {
			throw new Exception('Database name unset');
		}

		if( count($this->_directoryList) === 0 ) {
			throw new Exception('No directories added');
		}

		$this->_outputFormatter->outputStart();

		try{
			$this->_outputFormatter->outputInfo('Reading the directories');
			foreach($this->_directoryList as $directory) {
				$this->_loadFilesFromDirectory($directory);
			}

			$this->_outputFormatter->outputInfo('Retrieving the list of files');
			try {
				$list = $this->_nodeLists[$this->_dbType]->getNodesAsTopologicalSort();
			} catch(Jadva_Tc_Graph_Exception $e) {
				/** @see Jadva_Installer_Database_Exception */
				require_once 'Jadva/Installer/Database/Exception.php';
				throw new Jadva_Installer_Database_Exception('Error while sorting query files: ' . $e->getMessage());
			}

			$this->_outputFormatter->outputInfo('Checking for content');
			foreach($list as $node) {
				if( NULL === $node->content ) {
					/** @see Jadva_Installer_Database_Exception */
					require_once 'Jadva/Installer/Database/Exception.php';
					throw new Jadva_Installer_Database_Exception('Missing content for file "' . $node->filename . '"');
				}
			}
			$this->_outputFormatter->outputSuccess('File list updated and sorted');

			$this->_outputFormatter->outputInfo('Connecting to the database');
			$this->_connectToDatabase();

			$this->_outputFormatter->outputInfo('Locking the database');
			$this->_lockDatabase();

			$this->_outputFormatter->outputInfo('Creating a restore point');
			$this->_createRestorePoint();

			$this->_outputFormatter->outputInfo('Checking version table');
			$this->_checkVersionTable();

			$this->_outputFormatter->outputInfo('Retrieving current versions');
			$oldVersionList     = $this->_getVersions();
			$versionListUpdates = array();

			$this->_outputFormatter->outputInfo('Updating the database');
			$updateCount = 0;
			foreach($list as $node) {
				$currentVersion = intval(@$oldVersionList[$node->scriptName]);
				if( $currentVersion < $node->scriptVersion ) {
					$this->_outputFormatter->outputInfo('Updating script "' . $node->scriptName . '" to version ' . $node->scriptVersion);

					$this->_executeDatabaseScript($node->content);

					$updateCount++;
					$oldVersionList[$node->scriptName] = $node->scriptVersion;
					$versionListUpdates[$node->scriptName] = $node->scriptVersion;
					$this->_outputFormatter->outputSuccess('Update successful');
				}
			}

			if( $updateCount > 0 ) {
				$this->_outputFormatter->outputSuccess('Updated ' . $updateCount . ' scripts');
				$this->_storeVersions($versionListUpdates);
			} else {
				$this->_outputFormatter->outputSuccess('Database is up to date');
			}

			$this->_outputFormatter->outputSuccess('Done.');
		} catch(Jadva_Installer_Database_Exception $e) {
			if( NULL === $this->_restorePointLocation ) {
				$this->_outputFormatter->outputCrit('An error has occurred before or during the creating of the restore point');
				$this->_outputFormatter->outputErr($e->getMessage());
			} else {
				$this->_outputFormatter->outputErr ('An error has occurred: ' . $e->getMessage());
				$this->_outputFormatter->outputInfo('Restoring restore point');
				try {
					$this->_restoreRestorePoint();
				} catch(Jadva_Installer_Database_Exception $e2) {
					$this->_outputFormatter->outputErr ('An error has occurred while restoring the restore point: ' . $e2->getMessage());
				}
			}
		}

		if( $this->_databaseIsLocked() ) {
			$this->_outputFormatter->outputInfo('Unlocking the database');
			$this->_unlockDatabase();
		}

		$this->_disconnectFromDatabase();

		$this->_outputFormatter->outputEnd();
	}
	//------------------------------------------------
	/**
	 * Contains the output formatter instance
	 * @var  Jadva_Installer_Database_Abstract
	 */
	protected $_outputFormatter      = NULL;
	//------------------------------------------------
	/**
	 * Contains the directory to store the restore point in
	 * @var  string
	 */
	protected $_restoreDirectory     = NULL;
	//------------------------------------------------
	/**
	 * Contains the database connection credentials (The username to use for connecting)
	 * @var  string
	 */
	protected $_credentialsUser      = NULL;
	//------------------------------------------------
	/**
	 * Contains the database connection credentials (The password to use for connecting)
	 * @var  string
	 */
	protected $_credentialsPass      = NULL;
	//------------------------------------------------
	/**
	 * Contains the database connection credentials (The host to connect to)
	 * @var  string
	 */
	protected $_credentialsHost      = NULL;
	//------------------------------------------------
	/**
	 * Contains the database connection credentials (The port to connect to)
	 * @var  string
	 */
	protected $_credentialsPort      = NULL;
	//------------------------------------------------
	/**
	 * Contains the database connection credentials (The socket to connect to)
	 * @var  string
	 */
	protected $_credentialsSock      = NULL;
	//------------------------------------------------
	/**
	 * Contains the database name to install into
	 * @var  string
	 */
	protected $_databaseName         = NULL;
	//------------------------------------------------
	/**
	 * Contains the list of directories to load database scripts from
	 * @var  string
	 */
	protected $_directoryList        = array();
	//------------------------------------------------
	/**
	 * Contains the location of the restore point
	 * @var  string
	 */
	protected $_restorePointLocation = NULL;
	//------------------------------------------------
	/**
	 * Contains the list of tables that existed before the database was altered
	 * @var  string
	 */
	protected $_listTablesBefore     = NULL;
	//------------------------------------------------
	/**
	 * Contains the list of node lists, where each node is a database script
	 * @var  array
	 */
	protected $_nodeLists            = array();
	//------------------------------------------------
	/**
	 * Contains the name of the database type
	 * @var  string
	 */
	protected $_dbType               = NULL;
	//------------------------------------------------
	/**
	 * Checks a directory for existance
	 *
	 * @param  string   $directory  The directory to check
	 * @param  boolean  $writable   If TRUE, this also checks whether the directory is writable
	 *
	 * @return  The cleaned up directory URI
	 */
	protected function _checkDirectory($directory, $writable = FALSE)
	{
		$directory = (string) $directory;
		$directory = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $directory);
		if( substr($directory, -1) !== DIRECTORY_SEPARATOR ) {
			$directory .= DIRECTORY_SEPARATOR;
		}

		if(!is_dir($directory) ) {
			throw new Exception('No such directory: "' . $directory . '"');
		}

		if( $writable && !is_writeable($directory) ) {
			throw new Exception('Cannot write to directory: "' . $directory . '"');
		}

		return $directory;
	}
	//------------------------------------------------
	/**
	 * Loads the database scripts from the given directory and its subdirectories
	 *
	 * @param  string  $directory  The directory from which to load the scripts
	 *
	 * @return  void
	 */
	protected function _loadFilesFromDirectory($directory)
	{
		$iterator = new DirectoryIterator($directory);
		foreach($iterator as $file) {
			if( $file->isDot() ) {
				continue;
			}

			$filename = $file->getFileName();
			if( '.' === $filename[0] ) {
				continue;
			}

			$fullUri = $file->getPath() . DIRECTORY_SEPARATOR . $filename;
			if( $file->isDir() ) {
				$this->_loadFilesFromDirectory($fullUri . DIRECTORY_SEPARATOR);
				return;
			}

			$list = explode('.', $filename);

			if( count($list) < 3 ) {
				/** @see Jadva_Installer_Database_Exception */
				require_once 'Jadva/Installer/Database/Exception.php';
				throw new Jadva_Installer_Database_Exception('Could not determine information about file "' . $file . '"');
			}

			list($scriptName, $scriptVersion, $dbType) = $list;

			if( !is_numeric($scriptVersion) ) {
				/** @see Jadva_Installer_Database_Exception */
				require_once 'Jadva/Installer/Database/Exception.php';
				throw new Jadva_Installer_Database_Exception('Not a number: "' . $scriptVersion .'"');
			}

			if( !array_key_exists($dbType, $this->_nodeLists) ) {
				$this->_nodeLists[$dbType] = new Jadva_Installer_Database_TableNode_List($dbType);
			}

			$node = $this->_nodeLists[$dbType]->getNode($scriptName, $scriptVersion);
			$this->_setContent($fullUri, $node);
		}
	}
	//------------------------------------------------
	/**
	 * Connects to the database using the stored credentials
	 *
	 * @throws  Jadva_Installer_Database_Exception  When the connection cannot be established
	 * @return  void
	 */
	abstract protected function _connectToDatabase();
	//------------------------------------------------
	/**
	 * Locks the database, if possible. Issues a notice if locking is not possible.
	 *
	 * @return  void
	 */
	abstract protected function _lockDatabase();
	//------------------------------------------------
	/**
	 * Checks whether the database is locked
	 *
	 * @return  boolean  Whether the database is locked
	 */
	abstract protected function _databaseIsLocked();
	//------------------------------------------------
	/**
	 * Unlocks the database, if it was locked.
	 *
	 * @return  void
	 */
	abstract protected function _unlockDatabase();
	//------------------------------------------------
	/**
	 * Disconnects from the database
	 *
	 * @return  void
	 */
	abstract protected function _disconnectFromDatabase();
	//------------------------------------------------
	/**
	 * Checks whether the version table exists, creates it if it doesn't
	 *
	 * @return void
	 */
	abstract protected function _checkVersionTable();
	//------------------------------------------------
	/**
	 * Returns all the current versions of the installed scripts
	 *
	 * @return  array  The current versions
	 */
	abstract protected function _getVersions();
	//------------------------------------------------
	/**
	 * Executes a database script on the server
	 *
	 * @param  string  $script  The database script to execute
	 *
	 * @return  void
	 */
	abstract protected function _executeDatabaseScript($script);
	//------------------------------------------------
	/**
	 * Updates the versions table
	 *
	 * @param  array  $version_list  The version to update, mapped from script name to version number
	 *
	 * @return  void
	 */
	abstract protected function _storeVersions(array $version_list);
	//------------------------------------------------
	// Fallback functions
	//------------------------------------------------
	/**
	 * Creates a restore point from which the database can be restored.
	 *
	 * Stores the location in $this->_restorePointLocation.
	 *
	 * @return void;
	 */
	abstract protected function _createRestorePoint();
	//------------------------------------------------
	/**
	 * Restores the restore point
	 *
	 * @return  void
	 */
	abstract protected function _restoreRestorePoint();
	//------------------------------------------------
	// Helper functions
	//------------------------------------------------
	/**
	 * Parses a database script and returns the requirements and queries in it
	 *
	 * Returned is an array as follows:
	 * <code>
	 * return array(
	 *     "requirement_list" => array(
	 *         array("scriptName" => $scriptName1, "scriptVersion" => $scriptVersion1),
	 *         array("scriptName" => $scriptName2, "scriptVersion" => $scriptVersion2),
	 * //...
	 *     ),
	 *     "query_list" => array(
	 *         "query one",
	 *         "query two",
	 * //...
	 *     ),
	 * );
	 * </code>
	 *
	 * @param  string  $scriptContents  The contenst of the database script file
	 *
	 * @return  array  The list of requirements and queries
	 */
	abstract protected function _parseDatabaseScript($scriptContents);
	//------------------------------------------------
	/**
	 * Sets the content for a database script node
	 *
	 * @param  string  $filename  The name of the database script file
	 * @param  stirng  $curNode   The node of the database script
	 *
	 * @return  void
	 */
	protected function _setContent($filename, $curNode)
	{
		$nodeList       = $curNode->getParent();

		$scriptContents = file_get_contents($filename);
		$scriptContents = str_replace("\r\n", "\n", $scriptContents);
		$parsed         = $this->_parseDatabaseScript($scriptContents);

		foreach($parsed['requirement_list'] as $requirement) {
			$scriptName    = trim($requirement['scriptName']);
			$scriptVersion = intval(trim($requirement['scriptVersion']));

			$depNode = $nodeList->getNode($scriptName, $scriptVersion);
			$nodeList->addEdge($curNode, $depNode);
		}

		$curNode->content = $parsed['query_list'];
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
