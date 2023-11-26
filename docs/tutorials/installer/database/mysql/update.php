<?php

if( empty($argv[1]) ) {
	die('Missing argument: installation scripts directory');
}

$directory = realpath($argv[1]);
if( !$directory ) {
	die('Could not interpret "' . $argv[1] . '" as a proper directory');
}

//Open the configuration file
$config = require_once './config.inc.php';

//Set up a standard TXT output formatter unless the configuration file says different
if( empty($config['outputFormatter']) ) {
	require_once 'Jadva/Installer/OutputFormatter/Txt.php';

	$config['outputFormatter'] = new Jadva_Installer_OutputFormatter_Txt;
}

//Instantiate the installer, and run it
require_once 'Jadva/Installer/Database/Mysqli.php';

$installer = new Jadva_Installer_Database_Mysqli($config);
$installer->addDirectory($directory)
	->install();
