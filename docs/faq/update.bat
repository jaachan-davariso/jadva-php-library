@ECHO OFF
REM Updates the faq data

IF EXIST jadva-php-faq RD /S /Q jadva-php-faq
php ..\..\bin\jadva-xml2faq.php . .\jadva-php-faq --verbose
