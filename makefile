setup:
	docker compose up -d

get-deps: phpunit vfsStream

deps/vfsStream:
	git clone https://github.com/bovigo/vfsStream.git deps/vfsStream --branch RELEASE-0.11.2 --depth 1

vfsStream:
	ln -s deps/vfsStream/src/main/php/org/bovigo/vfs/ vfsStream

phpunit:
	wget -O phpunit https://phar.phpunit.de/phpunit-5.phar
	chmod +x phpunit

php7-dev-container:
	docker compose exec php7-dev-container sh

run-tests:
	./phpunit tests

docs:
	-rm -rf docs/phpDocumentor
	phpdoc -c phpDocumentor.ini

.PHONY: docs