<?php

set_include_path(
	'../../../../../library' . PATH_SEPARATOR
	. get_include_path()
);

return array(
	'credentials' => array(
		'username' => 'tut_jadva',
		'password' => 'tut_jadva',
		'host'     => '192.168.2.80',
	),
	'restoreDirectory' => 'restore',
	'databaseName'     => 'tut_jadva_dbins_mysql',
);
