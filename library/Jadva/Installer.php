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
 * @subpackage Jadva_Installer
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Installer.php 64 2009-01-17 16:20:45Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class helps you install PHP applications
 *
 * @category   JAdVA
 * @package    Jadva_Installer
 * @subpackage Jadva_Installer
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_Installer
{
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @param  array  $options  Options to set, as passed to {@link config}.
	 */
	public function __construct(array $options = array())
	{
		$this->config($options);

		$this->_page = NULL;
	}
	//------------------------------------------------
	/**
	 * Shorthand access to the various setXxx options
	 *
	 * @param  array  $options  The options to set, mapping the name of the variable to the value
	 *
	 * @return  Jadva_Installer  Provides a fluent interface
	 */
	public function config(array $options)
	{
		foreach($options as $optionName => $optionValue) {
			$methodName = 'set' . ucfirst($optionName);
			if( method_exists($this, $methodName) ) {
				$this->$methodName($optionValue);
			}
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Sets the name of the application to install
	 *
	 * @param  string  $in_name  The name of the application to install
	 *
	 * @return  Jadva_Installer  Provides a fluent interface
	 */
	public function setApplicationName($in_name)
	{
		$this->_applicationName = (string) $in_name;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the name of the application to install
	 *
	 * @return  string  The name of the application
	 */
	public function getApplicationName()
	{
		return $this->_applicationName;
	}
	//------------------------------------------------
	/**
	 * Sets whether to allow the user to add include directories to the path
	 *
	 * If your configuration file is a PHP file, the user could probably change the include path there. If your
	 * configuration file doesn't allow this, the user should update their php.ini or another server configuration.
	 *
	 * @param  boolean  $in_allow  Whether to allow the user to add include directories to the path
	 *
	 * @return  Jadva_Installer  Provides a fluent interface
	 */
	public function setAllowUserIncludes($in_allow)
	{
		$this->_allowUserIncludes = (bool) $in_allow;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns whether to allow the user to add include directories to the path
	 *
	 * @return  boolean  The name of the application
	 */
	public function getAllowUserIncludes()
	{
		return $this->_allowUserIncludes;
	}
	//------------------------------------------------
	/**
	 * Sets the minimum PHP version required to install the application
	 *
	 * @param  string  $in_version  The minium PHP version
	 *
	 * @return  Jadva_Installer  Provides a fluent interface
	 */
	public function setMiniumPhpVersion($in_version)
	{
		$this->_miniumPhpVersion = $in_version;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the mimimum PHP version required to install the application, if any
	 *
	 * @return  string|NULL  The minimum PHP version required to install the application, if any
	 */
	public function getMiniumPhpVersion()
	{
		return $this->_miniumPhpVersion;
	}
	//------------------------------------------------
	/**
	 * Adds a PHP variable test
	 *
	 * @see  Jadva_Installer_Test_PhpVar_Abstract
	 *
	 * @param  Jadva_Installer_Test_PhpVar_Abstract|string  The test. Can be used as a factory with the last part of the class name.
	 * @param  string                                       (OPTIONAL) The name of the variable to test. Only used when creating a new test.
	 * @param  array                                        (OPTIONAL) The options to pass when creating the test. Only used when creating a new test.
	 *
	 * @return  Jadva_Installer  Provides a fluent interface
	 */
	public function addTestPhpVar($in_test, $in_varName = NULL, $in_testOptions = NULL)
	{
		if( $in_test instanceof Jadva_Installer_Test_PhpVar_Abstract ) {
			$test = $in_test;
		} else {
			$className = 'Jadva_Installer_Test_PhpVar_' . $in_test;
			$this->_loadClass($className);
			$test = new $className($in_varName, (array) $in_testOptions);
		}

		$this->_testPhpVarList[] = $test;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Adds a include test
	 *
	 * @see  Jadva_Installer_Test_Include
	 *
	 * @param  Jadva_Installer_Test_Include|string  $in_test  The test to execute, or the actual include to try
	 * @param  string                               $in_name  (OPTIONAL) The name of the class or function that should exist after the include
	 * @param  string                               $in_type  (OPTIONAL) Whether the check is for a 'class' or a 'function'.
	 *
	 * @return  Jadva_Installer  Provides a fluent interface
	 */
	public function addTestInclude($in_test, $in_name = NULL, $in_type = NULL)
	{
		if( $in_test instanceof Jadva_Installer_Test_Include ) {
			$test = $in_test;
		} else {
			/** @see Jadva_Installer_Test_Include */
			require_once 'Jadva/Installer/Test/Include.php';

			$test = new Jadva_Installer_Test_Include($in_test, $in_name, $in_type);
		}

		$this->_testIncludes[] = $test;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Runs the installer
	 *
	 * @return  void
	 */
	public function run()
	{
		if( empty($_COOKIE['jadva_library_page_results']) ) {
			$this->_resultsCookie = array();
			$this->_resultsCookie['latestFinishedPage'] = 0;

			$this->_page          = 1;
		} else {
			$this->_resultsCookie = unserialize(base64_decode($_COOKIE['jadva_library_page_results']));
			$this->_page          = $this->_resultsCookie['latestFinishedPage'] + 1;
		}

		$this->_runPage();

		setcookie('jadva_library_page_results', base64_encode(serialize($this->_resultsCookie)));

		$this->_echoOutput();
	}
	//------------------------------------------------
	/**
	 * Contains the number of the current page
	 *
	 * @var  integer|NULL
	 */
	protected $_page;
	//------------------------------------------------
	/**
	 * Conains the contents of the results cookie
	 *
	 * @var  array
	 */
	protected $_resultsCookie;
	//------------------------------------------------
	/**
	 * Contains the output
	 *
	 * @var  string
	 */
	protected $_output = '';
	//------------------------------------------------
	/**
	 * Contains the name of the application to install
	 *
	 * @var  string
	 */
	protected $_applicationName  = 'Unknown application';
	//------------------------------------------------
	/**
	 * Contains whether to allow the user to add include directories to the path
	 *
	 * @var  boolean
	 */
	protected $_allowUserIncludes = FALSE;
	//------------------------------------------------
	/**
	 * Contains the minimum PHP version required to install the application
	 *
	 * @var  string|NULL
	 */
	protected $_miniumPhpVersion = NULL;
	//------------------------------------------------
	/**
	 * Contains the list of PHP variable tests
	 *
	 * @var  array
	 */
	protected $_testPhpVarList   = array();
	//------------------------------------------------
	/**
	 * Contains the list of Include tests
	 *
	 * @var  array
	 */
	protected $_testIncludes     = array();
	//------------------------------------------------
	/**
	 * Runs the current page
	 *
	 * @return  void
	 */
	protected function _runPage()
	{
		$success = FALSE;
		switch( $this->_page ) {
		case 1: $success = $this->_runPageTestPhpVars(); break;
		case 2: $success = $this->_runPageTestIncludes(); break;
		case 3: die('Not build yet');
		default:
			/** @see Jadva_Installer_Exception */
			require_once 'Jadva/Installer/Exception.php';
			throw new Jadva_Installer_Exception('Invalid page: ' . $this->_page);
		}

		if( $success ) {
			$this->_resultsCookie['latestFinishedPage']++;
		}
	}
	//------------------------------------------------
	/**
	 * Runs the PHP variable tests
	 *
	 * @return  boolean  TRUE if the required PHP tests success, FALSE otherwise
	 */
	protected function _runPageTestPhpVars()
	{
		$tableRequired = array();
		$tableAdvised  = array();

		$result = TRUE;
		foreach($this->_testPhpVarList as $varTest) {
			$testResult = $varTest->test();

			$tableRow = array($varTest->getVarName(), $varTest->renderExpectedValue(), $varTest->renderActualValue(), $testResult ? 'success' :'failed');

			if( $varTest->getRequired() ) {
				$result &= $testResult;

				$tableRequired[] = $tableRow;
			} else {
				$tableAdvised[] = $tableRow;
			}
		}

		if( NULL !== $this->_miniumPhpVersion ) {
			$versionTest = version_compare(PHP_VERSION, $this->_miniumPhpVersion, '>=');

			$result &= $versionTest;
		} else {
			$versionTest = NULL;
		}

		//Output
		$header = array(array('PHP Variable', 'Expected value', 'Actual value', 'Result'));

		$this->_outputHtmlHeader();
		$this->_outputHtml('<h1>Tests for PHP Values:</h1>');

		if( NULL !== $versionTest ) {
			$this->_outputHtml('<h2>PHP Version:</h2>');

			$out = 'Your PHP version is ' . PHP_VERSION . '.';
			if( $versionTest ) {
				$out .= ' That is sufficient to match the requirements of this application (' . $this->_miniumPhpVersion . ' or higher).';
			} else {
				$out .= ' This application requires at least ' . $this->_miniumPhpVersion;
			}

			$this->_outputHtml('<p class="' . ($versionTest ? 'success' : 'failed') . '">' . $out . '</p>');
		}

		if( 0 < count($tableRequired) ) {
			$this->_outputHtml('<h2>Required variable values:</h2>');
			$this->_outputHtmlTable($tableRequired, $header);
		}


		if( 0 < count($tableAdvised) ) {
			$this->_outputHtml('<h2>Advised variable values:</h2>');
			$this->_outputHtmlTable($tableAdvised, $header);
		}

		$this->_outputHtml('<h2>Results:</h2>');
		if( $result ) {
			$this->_outputHtml('<p>All required variable tests passed!');
			if( 0 < count($tableAdvised) ) {
				$this->_outputHtml(' Some advised variable tests did not pass. It is advisable to update your PHP configuration so all advised variable values match as well.');
			}

			$this->_outputHtml(' You can continue to <a href="./index.php">the next step</a><p>');
		} else {
			$this->_outputHtml('<p>Some required tests did not pass. Please update your PHP configuration and <a href="./index.php">try again</a><p>');
		}

		$this->_outputHtmlFooter();

		return $result;
	}
	//------------------------------------------------
	/**
	 * Runs the include tests
	 *
	 * @return  boolean  TRUE if the include tests success, FALSE otherwise
	 */
	protected function _runPageTestIncludes()
	{
		$errorList = array();

		if( $this->_allowUserIncludes ) {
			if( !array_key_exists('includeList', $this->_resultsCookie) ) {
				$this->_resultsCookie['includeList'] = array();

				//Since this class is running, the directory below is in the include path. However, this could
				//be because of the installer script. It could be that the application requires our libray and
				//that it's not default in the path. The only way to be really sure that such an application
				// won't boop up, is to add ourselves always to the include path.
				$this->_resultsCookie['includeList'][] = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
			}

			if( !empty($_POST['additional_include_paths']) ) {
				$list = explode(PHP_EOL, $_POST['additional_include_paths']);
				foreach($list as $path) {
					$path = trim($path);
					$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
					$path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

					if( is_dir($path) && is_readable($path) ) {
						if( !in_array($path, $this->_resultsCookie['includeList']) ) {
							$this->_resultsCookie['includeList'][] = $path;
						}
					} else {
						$errorList[] = 'Could not read "' . $path . '"';
					}
				}
			}


			//Add the already set include directories to prevent false negatives
			$currentPathList = explode(PATH_SEPARATOR, get_include_path());
			foreach($this->_resultsCookie['includeList'] as $includeDirectory) {
				if( !in_array($includeDirectory, $currentPathList) ) {
					set_include_path($includeDirectory . PATH_SEPARATOR . get_include_path());
				}
			}
		}


		//Output and do tests
		$this->_outputHtmlHeader();

		if( 0 < count($errorList) ) {
			$this->_outputHtml('<h2>Some includes could not be added:</h2>');
			$this->_outputHtml('<ul>');
			foreach($errorList as $error) {
				$this->_outputHtml('<li>' . $error . '</li>');
			}
			$this->_outputHtml('</ul>');
		}

		$this->_outputHtml('<h1>Tests for includes:</h1>');

		if( 0 === count($this->_testIncludes) ) {
			$this->_outputHtml('<p>This application requires no additional includes. Proceed to <a href="./index.php">the next step</a>.<p>');
			$this->_outputHtmlFooter();
			return TRUE;
		}

		$header = array(array('Include', 'Should contain', 'Result', 'Included paths'));

		$result = TRUE;
		$table  = array();
		foreach($this->_testIncludes as $includeTest) {
			$testResult = $includeTest->test();

			$table[] = array($includeTest->getInclude(), $includeTest->getType() . ' ' . $includeTest->getName(), $testResult ? 'success' :'failed', implode('<br />', $includeTest->getPathList()));

			$result &= $testResult;
		}

		$this->_outputHtml('<h2>Include tests:</h2>');
		$this->_outputHtmlTable($table, $header);

		$this->_outputHtml('<h2>Path directories:</h2>');
		$currentPathList = explode(PATH_SEPARATOR, get_include_path());
		if( 0 === count($currentPathList) ) {
			$this->_outputHtml('<p>None.</p>');
		} else {
			$this->_outputHtml('<ul>');
			foreach($currentPathList as $path) {
				$this->_outputHtml('<li>' . $path . '</li>');
			}
			$this->_outputHtml('</ul>');
		}


		if( $this->_allowUserIncludes ) {
			$this->_outputHtml('<h2>Included directories:</h2>');
			$this->_outputHtml('<ul>');
			foreach($this->_resultsCookie['includeList'] as $path) {
				$this->_outputHtml('<li>' . $path . '</li>');
			}
			$this->_outputHtml('</ul>');

			$this->_outputHtml('<h2>Include additional directories:</h2>');
			if( !$result ) {
				$this->_outputHtml('<form method="post" action="">');
				$this->_outputHtml('<textarea name="additional_include_paths" rows="5" cols="140"></textarea><br />');
				$this->_outputHtml('<input type="submit" value="Add the given paths to the include path" />');
				$this->_outputHtml('</form>');
			}
		}

		$this->_outputHtml('<h2>Results:</h2>');
		if( $result ) {
			$this->_outputHtml('<p>All includes could be found. You can continue to <a href="./index.php">the next step</a><p>');
		} else {
			$this->_outputHtml('<p>Some includes could not be found.');
			if( $this->_allowUserIncludes ) {
				$this->_outputHtml('Please fill in additional include directories in the textbox above to add the to the include list. If you changed the files, you can <a href="./index.php">retry</a>.');
			} else {
				$this->_outputHtml('Please update your PHP configuration and update the include path to ensure the above include tests pass. When you\'ve updated your PHP configuration, you can <a href="./index.php">retry</a>.');
			}

			$this->_outputHtml('<p>');
		}


		$this->_outputHtmlFooter();

		return $result;
	}
	//------------------------------------------------
	/**
	 * Loads the class with the given name
	 *
	 * @param  string  $in_className  The name of the class to load
	 *
	 * @return  void
	 */
	protected function _loadClass($in_className)
	{
		$className = (string) $in_className;
		$fileName  = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		require_once $fileName;
	}
	//------------------------------------------------
	//
	// HTML ouput functions
	//
	//------------------------------------------------
	/**
	 * Adds the HTML header to the output
	 *
	 * @return  void
	 */
	protected function _outputHtmlHeader()
	{
		$this->_outputHtml('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">');
		$this->_outputHtml('<html xmlns="http://www.w3.org/1999/xhtml">');
		$this->_outputHtml('<head>');
			$this->_outputHtml('<title>Installer for ' . $this->getApplicationName() . ', page ' . $this->_page . '</title>');
			$this->_outputHtml('<meta name="generator" content="Jadva_Installer" />');
		$this->_outputHtml('</head>');
		$this->_outputHtml('<body>');
	}
	//------------------------------------------------
	/**
	 * Adds a HTML Table to the output
	 *
	 * @param  array  $rows    The body table rows
	 * @param  array  $header  (OPTIONAL) The header table rows
	 * @param  array  $footer  (OPTIONAL) The footer table rows
	 *
	 * @return  void
	 */
	protected function _outputHtmlTable(array $rows, array $header = array(), array $footer = array())
	{
		$this->_outputHtml('<table>');

		if( 0 < count($header) ) {
			$this->_outputHtml('<thead>');
			foreach($header as $row) {
				$this->_outputHtmlTr($row);
			}
			$this->_outputHtml('</thead>');
		}

		if( 0 < count($footer) ) {
			$this->_outputHtml('<tfoot>');
			foreach($footer as $row) {
				$this->_outputHtmlTr($row);
			}
			$this->_outputHtml('</tfoot>');
		}

		if( 0 < count($rows) ) {
			$this->_outputHtml('<tbody>');
			foreach($rows as $row) {
				$this->_outputHtmlTr($row);
			}
			$this->_outputHtml('</tbody>');
		}

		$this->_outputHtml('</table>');
	}
	//------------------------------------------------
	/**
	 * Adds a HTML Table Row to the output
	 *
	 * @param  array  $cellList  The list of Table cells
	 *
	 * @return  void
	 */
	protected function _outputHtmlTr(array $cellList)
	{
		$this->_outputHtml('<tr>');
		foreach($cellList as $cell) {
			$this->_outputHtml('<td>' . $cell . '</td>');
		}
		$this->_outputHtml('</tr>');
	}
	//------------------------------------------------
	/**
	 * Adds the HTML footer to the output
	 *
	 * @return  void
	 */
	protected function _outputHtmlFooter()
	{
		$this->_outputHtml('<hr >');
		$this->_outputHtml('<p>JAdVA Installer for ' . $this->getApplicationName() . '</p>');
		$this->_outputHtml('</body></html>');
	}
	//------------------------------------------------
	/**
	 * Adds a string of HTML to the output
	 *
	 * @param  string  $in_html  The HTML to add to the output
	 *
	 * @return  void
	 */
	protected function _outputHtml($in_html)
	{
		$this->_output .= (string) $in_html;
	}
	//------------------------------------------------
	/**
	 * Echos the output
	 *
	 * @return  void
	 */
	protected function _echoOutput()
	{
		echo $this->_output;
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
