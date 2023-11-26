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
 * @copyright  Copyright (c) 2009-2010 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Mysqli.php 322 2010-01-25 11:46:13Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_Installer_Database_Abstract */
require_once 'Jadva/Installer/Database/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * Implements the class Jadva_Installer_Database_Abstract using the MySQLi class
 *
 * For issues with the parsing of your script, see {@link _parseDatabaseScript}.
 *
 * @todo  See if we need to throw an error if _parseDatabaseScript doesn't finish in a proper state (unclosed strings
 *        or so). So far it seems MySQL throws the error for us.
 *
 * @see        Jadva_Installer_Database_Abstract
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer_Database
 */
class Jadva_Installer_Database_Mysqli extends Jadva_Installer_Database_Abstract
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
		$this->_dbType = 'mysql';

		return parent::__construct($options);
	}
	//------------------------------------------------
	/**
	 * Contains the handle to the MySQLi connection
	 *
	 * @var  MySQLi
	 */
	protected $_mysqliConnection = NULL;
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_connectToDatabase */
	protected function _preInstallSystemCheck()
	{
		$output = array();
		$returnVar = NULL;

		list($commandPrefix, $commandSuffix) = $this->_getCommandFix();

		$commandSuffix .= ' --version';

		exec($commandPrefix . 'mysqldump' . $commandSuffix, $output, $returnVar);

		if( 0 !== $returnVar ) {
			/** @see Jadva_Installer_Database_Exception */
			require_once 'Jadva/Installer/Database/Exception.php';
			throw new Jadva_Installer_Database_Exception('mysqldump command not found, ' . 
				($this->_binDir ? 'binaries directory: ' . $this->_binDir : 'no binaries directory set')
			);
		}

		$output = array();
		$returnVar = NULL;
		exec($commandPrefix . 'mysql' . $commandSuffix, $output, $returnVar);

		if( 0 !== $returnVar ) {
			/** @see Jadva_Installer_Database_Exception */
			require_once 'Jadva/Installer/Database/Exception.php';
			throw new Jadva_Installer_Database_Exception('mysql command not found');
		}

		//Perhaps do a version check? Though that could be dependend on the user's install scripts
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_connectToDatabase */
	protected function _connectToDatabase()
	{
		$mysqli = @new mysqli(
			$this->_credentialsHost,
			$this->_credentialsUser,
			$this->_credentialsPass,
			$this->_databaseName,
			$this->_credentialsPort,
			$this->_credentialsSocket
		);

		if( mysqli_connect_error() ) {
			/** @see Jadva_Installer_Database_Exception */
			require_once 'Jadva/Installer/Database/Exception.php';
			throw new Jadva_Installer_Database_Exception(
				'MySQLi error while connecting to database: (' . mysqli_connect_errno() . ') '
				  . mysqli_connect_error()
			);
		}

		$this->_mysqliConnection = $mysqli;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_lockDatabase */
	protected function _lockDatabase()
	{
		$this->_outputFormatter->outputNotice('MySQL cannot lock databases; proceed with care.');
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_databaseIsLocked */
	protected function _databaseIsLocked()
	{
		return FALSE;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_unlockDatabase */
	protected function _unlockDatabase()
	{
		//MySQL cannot lock databases; no unlocking necessary
		return;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_disconnectFromDatabase */
	protected function _disconnectFromDatabase()
	{
		if( NULL !== $this->_mysqliConnection ) {
			$this->_mysqliConnection->close();
			$this->_mysqliConnection = NULL;
		}
		return;
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_checkVersionTable */
	protected function _checkVersionTable()
	{
		$query = 'SHOW TABLES LIKE "jadva_installer_database_table_versions";';
		$results = $this->_executeQuery($query);

		if( 0 === $results->num_rows ) {
			$query = 'SHOW TABLES LIKE "Jadva_Installer_database_table_versions";';
			$results = $this->_executeQuery($query);

			if( 0 === $results->num_rows ) {

				$query = 'CREATE TABLE jadva_installer_database_table_versions ('
				       . '    scriptName    VARCHAR(255)     NOT NULL,'
				       . '    scriptVersion INTEGER UNSIGNED NOT NULL,'
				       . '    PRIMARY KEY(scriptName)'
				       . ');';
				$this->_executeQuery($query);
			} else {
				$query = 'RENAME TABLE Jadva_Installer_database_table_versions TO jadva_installer_database_table_versions;';
				$this->_executeQuery($query);
			}
		}
	}
	//------------------------------------------------
	/** Implements Jadva_Installer_Database_Abstract::_getVersions */
	protected function _getVersions()
	{
		$return      = array();

		$results = $this->_executeQuery('SELECT * FROM jadva_installer_database_table_versions');

		while( NULL !== ($row = $results->fetch_assoc()) ) {
			$return[ $row['scriptName'] ] = intval($row['scriptVersion']);
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
		foreach($version_list as $scriptName => $scriptVersion) {
			$query = 'UPDATE `jadva_installer_database_table_versions` '
			       . 'SET `scriptVersion`=' . $scriptVersion . ' '
			       . 'WHERE `scriptName`="' . str_replace('"', '\"', $scriptName) . '"';

			$results = $this->_executeQuery($query);

			if( 0 === $this->_mysqliConnection->affected_rows ) {
				$query = 'INSERT INTO `jadva_installer_database_table_versions` '
				       . '(`scriptName`, `scriptVersion`) VALUES '
				       . '("' . str_replace('"', '\"', $scriptName) . '", ' . $scriptVersion . ');';

				$this->_executeQuery($query);
			}
		}

		$this->_outputFormatter->outputInfo('Stored information about the new versions');
	}
	//------------------------------------------------
	// Fallback functions
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

		if( !empty($this->_credentialsPort) ) {
			$parameter_list['port'] = $this->_credentialsPort;
		}

		if( !empty($this->_credentialsSock) ) {
			$parameter_list['socket'] = $this->_credentialsSock;
		}

		list($commandPrefix, $commandSuffix) = $this->_getCommandFix();

		$command = $commandPrefix . 'mysqldump' . $commandSuffix;
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

		if( !Jadva_File_Abstract::fileSchemeIsLinux() ) {
			//Then, when both the directory and the commands are escaped, you need to escape the whole thing
			// again. This might be fixed in later versions, but it wasn't fixed in 5.2.6
			$command = '"' . $command . '"';
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
			'host'           => $this->_credentialsHost,
			$this->_databaseName,
		);

		if( !empty($this->_credentialsPort) ) {
			$parameter_list['port'] = $this->_credentialsPort;
		}

		if( !empty($this->_credentialsSock) ) {
			$parameter_list['socket'] = $this->_credentialsSock;
		}

		list($commandPrefix, $commandSuffix) = $this->_getCommandFix();

		$command = $commandPrefix . 'mysql' . $commandSuffix;
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

		if( !Jadva_File_Abstract::fileSchemeIsLinux() ) {
			//Then, when both the directory and the commands are escaped, you need to escape the whole thing
			// again. This might be fixed in later versions, but it wasn't fixed in 5.2.6
			$command = '"' . $command . '"';
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
			throw new Jadva_Installer_Database_Exception('mysql returned with exit code "' . $returnVar . '".');
		}
		
		$this->_outputFormatter->outputSuccess('Restorepoint restored');
	}
	//------------------------------------------------
	// Helper functions
	//------------------------------------------------
	const PARSER_STATE_OUT                   = 'parserStateOut';

	//Comments
	const PARSER_STATE_DASH                  = 'parserStateDash';
	const PARSER_STATE_COMMENT_ONELINE       = 'parserStateCommentOnline';
	const PARSER_STATE_COMMENT_MULTI_START   = 'parserStateCommentMultiStart';
	const PARSER_STATE_COMMENT_MULTI         = 'parserStateCommentMulti';
	const PARSER_STATE_COMMENT_MULTI_END     = 'parserStateCommentMultiEnd';

	//Strings
	const PARSER_STATE_QUOTE_ONE             = 'parserStateQuoteOne';
	const PARSER_STATE_QUOTE_ONE_ESCAPED     = 'parserStateQuoteOneEscaped';
	const PARSER_STATE_QUOTE_TWO             = 'parserStateQuoteTwo';
	const PARSER_STATE_QUOTE_TWO_ESCAPED     = 'parserStateQuoteTwoEscaped';
	//------------------------------------------------
	/**
	 * Returns the command prefix and suffix
	 *
	 * @return  array  The prefix at key 0, and the suffix at key 1
	 */
	protected function _getCommandFix()
	{
		if( !class_exists('Jadva_File_Abstract') ) {
			/** @see Jadva_File_Abstract */
			require_once 'Jadva/File/Abstract.php';
		}

		$commandPrefix = $this->_binDir;
		$commandSuffix = '';

		if( !Jadva_File_Abstract::fileSchemeIsLinux() ) {
			$commandPrefix = '"' . $commandPrefix;
			$commandSuffix .= '.exe"';
		}

		return array($commandPrefix, $commandSuffix);
	}
	//------------------------------------------------
	/**
	 * Implements Jadva_Installer_Database_Abstract::_parseDatabaseScript
	 *
	 * In general, we try to accept the same behaviour as the mysql command line tool does.
	 *
	 * We need to feed the queries, one at a time. And we want to be able to deal with DELIMITER clauses. Hence, a
	 * rather large parser was hidden away in here
	 *
	 * When you use "#" or "--", the mysql program acts odd. Hence, we assume people don't feed us that kind of 
	 * scripts either
	 *
	 * Assumed is that the ";" is used as a delimiter at the beginning of EACH file
	 *
	 * MySQL uses /*! as a special kind of extension. Therefore, we don't remove /* comments. As far as we can see,
	 * this does not interfere with the "one query at a time", since you cannot hide entire queries with this from
	 * other DBMS systems.
	 */
	protected function _parseDatabaseScript($scriptContents)
	{
		$parserState = self::PARSER_STATE_OUT;

		$delimiter   = ';'; $delimiterLength = strlen($delimiter);

		$queryList           = array();
		$oneLineCommentsList = array();

		$curIndex = 0; $scriptLength = strlen($scriptContents);

		$curText           = '';
		$curOneLineComment = '';

		while( $curIndex < $scriptLength ) {
			$char = $scriptContents[$curIndex];

			switch($parserState) {
			case self::PARSER_STATE_OUT:
				if( substr($scriptContents, $curIndex, $delimiterLength) === $delimiter ) {
					$queryList[] = $curText;
					$curText = '';
					$curIndex += $delimiterLength;
					continue;
				}

				switch($char) {
				case '-':
					$parserState = self::PARSER_STATE_DASH;
					break;
				case "'":
					$parserState = self::PARSER_STATE_QUOTE_ONE;
					$curText .= $char;
					break;
				case '"':
					$parserState = self::PARSER_STATE_QUOTE_TWO;
					$curText .= $char;
					break;
				case '#':
					$parserState = self::PARSER_STATE_COMMENT_ONELINE;
					$curText .= $char;
					break;
				case "/":
					$parserState = self::PARSER_STATE_COMMENT_MULTI_START;
					break;
				default:
					//The proper way of doing this would be to introduce 9 extra states
					// But this is much easier
					if( 'd' === strtolower($char) ) { //Quick test
						//Could be 'delimiter'
						if( 'delimiter' === strtolower(substr($scriptContents, $curIndex, 9)) ) {
							$tmpPos = $curIndex + 9;
							while( ($tmpPos < $scriptLength) && (' '  === $scriptContents[$tmpPos]) ) { $tmpPos++; }

							$newLinePos = strpos($scriptContents, "\n", $tmpPos);

							$delimiter       = trim(substr($scriptContents, $tmpPos, $newLinePos - $tmpPos));
							$delimiterLength = strlen($delimiter);


							$curIndex = $newLinePos; //It gets added one for the \n later on
							$parserState = self::PARSER_STATE_OUT;
						} else {
							$parserState = self::PARSER_STATE_OUT;
							$curText .= $char;
						}
					} else {
						$parserState = self::PARSER_STATE_OUT;
						$curText .= $char;
					}
					break;
				}
				break;

			case self::PARSER_STATE_DASH:
				switch($char) {
				case '-':
					$parserState = self::PARSER_STATE_COMMENT_ONELINE;
					break;
				default:
					$parserState = self::PARSER_STATE_OUT;
					$curText .= '-'; //Add a dash for the one that got us into this state
					$curText .= $char;
					break;
				}
				break;
			case self::PARSER_STATE_COMMENT_ONELINE:
				switch($char) {
				case "\n":
					$parserState = self::PARSER_STATE_OUT;
					$oneLineCommentsList[] = $curOneLineComment;
					$curOneLineComment = '';
					break;
				default:
					$parserState = self::PARSER_STATE_COMMENT_ONELINE;
					$curOneLineComment .= $char;
					break;
				}
				break;
			case self::PARSER_STATE_COMMENT_MULTI_START:
				switch($char) {
				case "*":
					$parserState = self::PARSER_STATE_COMMENT_MULTI;
					$curText .= '/'; //Add a slash for the one that got us into this state
					$curText .= $char;
					break;
				default:
					$parserState = self::PARSER_STATE_OUT;
					$curText .= '/'; //Add a slash for the one that got us into this state
					$curText .= $char;
					break;
				}
				break;
			case self::PARSER_STATE_COMMENT_MULTI:
				switch($char) {
				case "*":
					$parserState = self::PARSER_STATE_COMMENT_MULTI_END;
					break;
				default:
					$parserState = self::PARSER_STATE_COMMENT_MULTI;
					$curText .= $char;
					break;
				}
				break;
			case self::PARSER_STATE_COMMENT_MULTI_END:
				switch($char) {
				case "/":
					$parserState = self::PARSER_STATE_OUT;
					$curText .= '*'; //Add a star for the one that got us into this state
					$curText .= $char;
					break;
				default:
					$parserState = self::PARSER_STATE_COMMENT_MULTI;
					$curText .= '*'; //Add a star for the one that got us into this state
					$curText .= $char;
					break;
				}
				break;

			case self::PARSER_STATE_QUOTE_ONE:
				switch($char) {
				case "'":
					$parserState = self::PARSER_STATE_OUT;
					$curText .= $char;
					break;
				case '\\':
					$parserState = self::PARSER_STATE_QUOTE_ONE_ESCAPED;
					$curText .= $char;
					break;
				default:
					$parserState = self::PARSER_STATE_QUOTE_ONE;
					$curText .= $char;
					break;
				}
				break;
			case self::PARSER_STATE_QUOTE_ONE_ESCAPED:
				switch($char) {
				default:
					$parserState = self::PARSER_STATE_QUOTE_ONE;
					$curText .= $char;
					break;
				}
				break;

			case self::PARSER_STATE_QUOTE_TWO:
				switch($char) {
				case '"':
					$parserState = self::PARSER_STATE_OUT;
					$curText .= $char;
					break;
				case '\\':
					$parserState = self::PARSER_STATE_QUOTE_TWO_ESCAPED;
					$curText .= $char;
					break;
				default:
					$parserState = self::PARSER_STATE_QUOTE_TWO;
					$curText .= $char;
					break;
				}
				break;
			case self::PARSER_STATE_QUOTE_TWO_ESCAPED:
				switch($char) {
				default:
					$parserState = self::PARSER_STATE_QUOTE_TWO;
					$curText .= $char;
					break;
				}
				break;
			}

			$curIndex++;
		}

		//Add the last query
		$queryList[] = $curText;

		//Remove empty queries
		$list = array();
		foreach($queryList as $queryOriginal) {
			$query = $queryOriginal;
			$query = str_replace(array("\n", "\r\n"), ' ', $query);
			$query = preg_replace('/[ \t]+/', ' ', $query);
			$query = trim($query);

			if( empty($query) ) {
				continue;
			}

			$list[] = $queryOriginal;
		}
		$queryList = $list;

		// Parse comments to find requirements
		$requirementList = array();
		foreach($oneLineCommentsList as $oneLineComment) {
			$oneLineComment = trim($oneLineComment);
			if( substr($oneLineComment, 0, 9) === 'REQUIRES:' ) {
				$requirement = substr($oneLineComment, 9);

				if( FALSE === ($commaLocation = strpos($requirement, ',') ) ) {
					$this->_outputFormatter->outputWarning('Unrecognized requirement comment line: "' . $requirement . '"');
				} else {
					list($scriptName, $scriptVersion) = explode(',', $requirement);
					$requirementList[] = array(
						'scriptName'    => trim($scriptName),
						'scriptVersion' => intval($scriptVersion),
					);
				}
			}
		}

		return array(
			'requirement_list' => $requirementList,
			'query_list'       => $queryList,
		);
	}
	//------------------------------------------------
	/**
	 * Executes a query
	 *
	 * @param  string  $query  The query to execute
	 *
	 * @return  SimpleXMLElement  The results in XML
	 */
	protected function _executeQuery($query)
	{
		$results = $this->_mysqliConnection->query($query);

		if( $this->_mysqliConnection->errno ) {
			$this->_outputFormatter->outputErr ('MySQLi error: ' . $this->_mysqliConnection->error);
			$this->_outputFormatter->outputInfo('Query was: ' . PHP_EOL . '<pre>' . $query . '</pre>');

			require_once 'Jadva/Installer/Database/Exception.php';
			throw new Jadva_Installer_Database_Exception($this->_mysqliConnection->error);
		}

		if( $this->_mysqliConnection->warning_count ) {
			if ($result = $this->_mysqliConnection->query("SHOW WARNINGS")) {
				while(NULL !== ($row = $result->fetch_row())) {
					$this->_outputFormatter->outputWarning(sprintf("%s (%d): %s\n", $row[0], $row[1], $row[2]));
				}
				$result->close();
			}
		}

		return $results;
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------

