#!/bin/bash
# SH script to call php script easily

PHP_LOC=`which php`
if [ $? != 0 ]; then
	echo php not found
	exit 1
fi

$PHP_LOC jadva-xml2faq.php $*

exit $?
