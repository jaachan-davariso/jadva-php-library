<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * JAdVA library
 *
 * This file bootstraps the JAdVA installer, then loads the configure_installer function from the 
 * config.installer.inc.php file in the same directory, and passes an installer object to it. After that, the installer
 * will be run. This should provide for all the bootstrapping you need to make use of the Jadva_Installer class.
 *
 * You can bundle this file along with your application, for example in the /install/ directory of your document root.
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
 * @package    Jadva
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: index.php 68 2009-01-17 16:46:01Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
// Test for cookies
if( empty($_COOKIE['jadva_library_cookie']) ) {
	if( empty($_GET['cookie']) ) {
		setcookie('jadva_library_cookie', 'checked');
		header('Location: ./index.php?cookie=check');
		return;
	} else {
		die('Cookies are not enabled in your browser. Please enable them');
	}
}
//----------------------------------------------------------------------------------------------------------------------
// Find the library itself
if( !empty($_COOKIE['jadva_library_location']) ) {
	set_include_path($_COOKIE['jadva_library_location'] . PATH_SEPARATOR . get_include_path());

	/** @see Jadva_Installer */
	require_once 'Jadva/Installer.php';
} else {
	$pathList = explode(PATH_SEPARATOR, get_include_path());

	foreach($pathList as $path) {
		$path = realpath($path);

		if( $path === FALSE ) {
			continue;
		}

		$file = $path . DIRECTORY_SEPARATOR . 'Jadva' . DIRECTORY_SEPARATOR . 'Installer.php';

		if( !file_exists($file) ) {
			continue;
		}

		require_once $file;

		if( class_exists('Jadva_Installer') ) {
			break;
		}
	}

	if( !class_exists('Jadva_Installer') ) {
		$path  = '';
		$error = '';
		if( !empty($_POST['jadva_library_location']) ) {
			$path = realpath($_POST['jadva_library_location']);

			if( FALSE === $path ) {
				$error = 'Path not found: ' . $_POST['jadva_library_location'];
				$path  = $_POST['jadva_library_location'];
			} else {
				$file = $path . DIRECTORY_SEPARATOR . 'Jadva' . DIRECTORY_SEPARATOR . 'Installer.php';

				if( !file_exists($file) ) {
					$error = 'Could not find installer file: ' . $file;
				} else {
					require_once $file;

					if( !class_exists('Jadva_Installer', FALSE) ) {
						$error = 'File does not contain class Jadva_Installer: ' . $file;
					} else {
						setcookie('jadva_library_location', $path . DIRECTORY_SEPARATOR);
						header('Location: ' . $_SERVER['REQUEST_URI']);
						return;
					}
				}
			}
		}

		echo '<html><body style="background-color: white; color: black;"><form method="post" action="">';
		if( !empty($error) ) {
			echo '<span style="color: red;">Eror: ' . $error . '</span><br />';
		}
		echo 'Could not find installer libary. Please specify path to the JAdVA library: <br />';
		echo '<input type="text" name="jadva_library_location" value="' . htmlspecialchars($path) . '" size="140" /><br />';
		echo '<input type="submit" />';
		echo '</form></body></html>';
		return;
	}
}
//----------------------------------------------------------------------------------------------------------------------
//Create the installer
$installer = new Jadva_Installer;

//Load the configuration
$configInstallerFileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.installer.inc.php';
if( !file_exists($configInstallerFileName) ) {
	die('Missing installer configuration file "' . $configInstallerFileName . '"');
}

$config = require_once $configInstallerFileName;

if( !function_exists('configure_installer') ) {
	die('Configuration file "' . $configInstallerFileName . '" does not contain `function configure_installer(Jadva_Installer $installer)` function');
}

configure_installer($installer);

$installer->run();

//----------------------------------------------------------------------------------------------------------------------
