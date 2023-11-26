FROM alpine:3.13

RUN apk add make mariadb-client php php-mbstring php-dom php-json php-mysqli php-phar php-tokenizer php-xmlwriter vim
