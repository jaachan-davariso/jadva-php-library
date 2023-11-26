
docs:
	-rm -rf docs/phpDocumentor
	phpdoc -c phpDocumentor.ini

.PHONY: docs