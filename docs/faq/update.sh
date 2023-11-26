#!/bin/bash

# Updates the FAQ data

rm -rf jadva-php-faq
php ../../bin/jadva-xml2faq.php . ./jadva-php-faq --verbose

