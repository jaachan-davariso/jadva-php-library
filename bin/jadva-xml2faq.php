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
 * @package    Jadva_Bin
 * @subpackage Jadva_Bin_Faq
 * @copyright  Copyright (c) 2010 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: jadva-xml2faq.php 317 2010-01-23 12:25:58Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
// Set up environment
if( !defined('__DIR__') ) {
	define('__DIR__', dirname(__FILE__));
}

define('REQUIRED_JADVA_VERSION', '0.2.5');
//----------------------------------------------------------------------------------------------------------------------
/**
 * Shows help to the user, and exit()s.
 *
 * @param  string  The error message
 *
 * @return  void
 */
function showHelp($errorMessage = NULL)
{
	global $argv;

	if( NULL !== $errorMessage ) {
		echo $errorMessage . PHP_EOL;
	}

	echo 'Usage: ' . basename($argv[0]) . ' [OPTIONS]... INPUT OUTPUT' . PHP_EOL;
	echo ' where ' . PHP_EOL;
	echo ' INPUT   is the input XML file, or directory with XML files' . PHP_EOL;
	echo ' OUTPUT  is the directory to put the HTML files of the FAQ,' . PHP_EOL;
	echo '         which will be created if it doesn\'t exist' . PHP_EOL;
	echo PHP_EOL;
	echo ' and OPTIONS are one or more of' . PHP_EOL;
	echo ' --help     show this and exit' . PHP_EOL;
	echo ' --verbose  be more verbose to what is happening' . PHP_EOL;
	echo ' --quiet    show no output, save for errors and warnings' . PHP_EOL;
	echo PHP_EOL;
	echo 'Exit status is 0 if OK, 1 if trouble.' . PHP_EOL;
	echo PHP_EOL;
	echo 'For more information, see http://php.jadva.net/' . PHP_EOL;
	exit();
}
//----------------------------------------------------------------------------------------------------------------------
/**
 * Outputs the message if output level is higher than the given level
 *
 * @param  integer  $level    The mimimum required output level
 * @param  string   $message  The message
 *
 * @return  void
 */
function output($level, $message)
{
	global $OPTIONS;

	$options = func_get_args();
	array_shift($options);
	array_shift($options);

	if( 1 < $OPTIONS->verboseLevel ) {
		echo vsprintf($message, $options) . PHP_EOL;
	}
}
//----------------------------------------------------------------------------------------------------------------------
// Input
$inputFile = NULL;
$outputDir = NULL;

$OPTIONS = new stdClass;
$OPTIONS->verboseLevel = 1;
$OPTIONS->libraryLocation = NULL;
$OPTIONS->iconDir = NULL;

for($itArg = 1; $itArg < $argc; $itArg++) {
	$arg = $argv[$itArg];

	if( ('-' != $arg[0]) || ('-' != $arg[1]) ) {
		if( NULL === $inputFile ) {
			$inputFile = $arg;
		} elseif( NULL === $outputDir ) {
			$outputDir = $arg;
		} else {
			showHelp('Unexpected parameter: ' . $arg);
		}
		continue;
	}

	switch($arg) {
	case '--help':
		showHelp();
	case '--verbose':
		$OPTIONS->verboseLevel = 2;
		break;
	case '--quiet':
		$OPTIONS->verboseLevel = 0;
		break;
	case '--library':
		$itArg++;
		if( $itArg == $argc ) {
			showHelp('Missing parameter for option --library');
		}
		$OPTIONS->libraryLocation = $argv[$itArg];
		break;
	case '--icon-dir':
		$itArg++;
		if( $itArg == $argc ) {
			showHelp('Missing parameter for option --icon-dir');
		}
		$OPTIONS->iconDir = $argv[$itArg];
		break;
	default:
		showHelp('Invalid parameter: ' . $arg);
	}
}

if( !$inputFile ) {
	showHelp('Missing INPUT');
}

if( !$outputDir ) {
	showHelp('Missing OUTPUT');
}

if( !$OPTIONS->libraryLocation ) {
	$result = @include_once 'Jadva/Version.php';

	if( !$result && file_exists(dirname(__DIR__) . '/library/Jadva/Version.php') ) {
		$OPTIONS->libraryLocation = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'library';
	}
}

if( $OPTIONS->libraryLocation ) {
	set_include_path(
		realpath($OPTIONS->libraryLocation)
		. PATH_SEPARATOR . get_include_path()
	);
}
//----------------------------------------------------------------------------------------------------------------------
//Check environmnent
$result = @include_once 'Jadva/Version.php';
if( !$result ) {
	die('JAdVA library not found.' . PHP_EOL);
}
if( version_compare(str_replace('.dev', '', Jadva_Version::VERSION), REQUIRED_JADVA_VERSION, 'lt') ) {
	die('Requires JAdVA library ' . REQUIRED_JADVA_VERSION . ' or higher; ' . Jadva_Version::VERSION . ' found.');
}
//----------------------------------------------------------------------------------------------------------------------
// Load classes
/** @see Jadva_File */
require_once 'Jadva/File.php';
/** @see Jadva_FaqList */
require_once 'Jadva/FaqList.php';
//----------------------------------------------------------------------------------------------------------------------
// Verify input
try {
	$inputPath = realpath($inputFile);
	if( FALSE === $inputPath ) {
		showHelp('Faulty INPUT (' . $inputFile . '): File or directory does not exist.');
	}

	$inputFile = Jadva_File_Abstract::verifyExistance($inputPath);
} catch( Jadva_File_Exception $e ) {
	showHelp('Faulty INPUT (' . $inputFile . '): ' . $e->getMessage());
}

if( !is_dir($outputDir) ) {
	output(2, 'Creating output directory %1$s', $outputDir);
	mkdir($outputDir, 0755, TRUE);
}

try {
	$outputDir = Jadva_File_Directory::verifyExistance(realpath($outputDir));
} catch( Jadva_File_Exception $e ) {
	showHelp('Faulty OUTPUT (' . $outputDir . '): ' . $e->getMessage());
}

if( $OPTIONS->iconDir ) {
	$path = realpath($OPTIONS->iconDir);
	if( FALSE === $path ) {
		showHelp('Faulty INPUT (' . $OPTIONS->iconDir . '): File or directory does not exist.');
	}

	Jadva_FaqList::$iconDirectory = $path;
}
//----------------------------------------------------------------------------------------------------------------------
//Generate FAQ for all .xml files, and create HTML output
$faq = new Jadva_FaqList;

if( 0 == $OPTIONS->verboseLevel ) {
	error_reporting(0);
}

$inputFiles = 0;
if( !$inputFile->isDir() ) {
	output(1, 'Loading data from ' . $inputFile->getPath());
	$faq->loadXml($inputFile->getContents());

	$sourceDir = $inputFile->getParent();
	$inputFiles = 1;
} else {
	foreach($inputFile->filter('Extension', array('extensions' => array('xml'))) as $file) {
		output(1, 'Loading data from ' . $file->getPath());
		$faq->loadXml($file->getContents());
		$inputFiles++;
	}

	$sourceDir = $inputFile;
}

output(1, '%1$d files added', $inputFiles);

$errors = $faq->getErrors();
if( 0 < count($errors) ) {
	foreach($errors as $msg) {
		echo '[Error] ' . $msg . PHP_EOL;
	}
	exit(1);
}

output(1, 'Saving HTML files to ' . $outputDir->getPath());
$faq->toHtml($outputDir, $sourceDir);
//----------------------------------------------------------------------------------------------------------------------
// Inform user of completion
$warnings = $faq->getWarnings();
if( 0 === count($warnings) ) {
	output(0, 'Done.');
	exit(0);
}

foreach($warnings as $msg) {
	echo '[Warning] ' . $msg . PHP_EOL;
}

output(0, 'Done. There were %1$d warning(s).', count($warnings));
//----------------------------------------------------------------------------------------------------------------------
exit(0);
