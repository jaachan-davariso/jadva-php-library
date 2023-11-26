<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * JAdVA library tests, helper file
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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2009 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: TestHelper.php 252 2009-08-21 10:47:40Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------------------
$jadvaRoot    = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$jadvaLibrary = $jadvaRoot . 'library' . DIRECTORY_SEPARATOR;
$jadvaLibraryDev = $jadvaRoot . 'library_dev' . DIRECTORY_SEPARATOR;
$jadvaTests   = $jadvaRoot . 'tests' . DIRECTORY_SEPARATOR;

$path = array(
	$jadvaLibrary,
	$jadvaLibraryDev,
	$jadvaTests,
	get_include_path()
);

set_include_path(implode(PATH_SEPARATOR, $path));
//----------------------------------------------------------------------------------------------------------------------
/*
 * Include configuration
 */
if( file_exists($jadvaTests . 'TestConfiguration.php') ) {
	require_once $jadvaTests . 'TestConfiguration.php';
} else {
	require_once $jadvaTests . 'TestConfiguration.php.dist';
}
//----------------------------------------------------------------------------------------------------------------------
