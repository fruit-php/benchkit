#!/usr/bin/make -f

.PHONY: force-update clean prepare lint \
	phploc pdepend phpmd phpcs phpcpd test docs

lint:
	find src -name '*.php' -exec php -l {} \;
	find example -name '*.php' -exec php -l {} \;

check: lint

docs:
	doxygen doxygen.conf

composer.phar:
	curl -sS https://getcomposer.org/installer | php

vendor/autoload.php: composer.lock
	./composer.phar dumpautoload

composer.lock: composer.json composer.phar
	./composer.phar update

update: composer.phar
	./composer.phar install

force-update: composer.phar
	./composer.phar selfupdate
	./composer.phar update

test: vendor/autoload.php
	bin/bench run example

clean:
	rm -fr build/coverage
	rm -fr build/logs
	rm -fr build/pdepend
	rm -fr build/phpdox
	rm -fr build/docs

prepare: vendor/autoload.php
	mkdir -p build/api
	mkdir -p build/coverage
	mkdir -p build/logs
	mkdir -p build/pdepend
	mkdir -p build/phpdox
	mkdir -p build/docs

phploc: prepare
	vendor/bin/phploc --count-tests --log-csv build/logs/phploc.csv --log-xml build/logs/phploc.xml src

pdepend: prepare
	vendor/bin/pdepend --jdepend-xml=build/logs/jdepend.xml --jdepend-chart=build/pdepend/dependencies.svg --overview-pyramid=build/pdepend/overview-pyramid.svg src

phpmd: prepare
	vendor/bin/phpmd src xml phpmd.xml --reportfile build/logs/pmd.xml || echo

phpcs: prepare
	vendor/bin/phpcs --report=checkstyle --report-file=build/logs/checkstyle.xml --standard=PSR2 --extensions=php --ignore=autoload.php src || echo

phpcpd: prepare
	vendor/bin/phpcpd --log-pmd build/logs/pmd-cpd.xml src

all: clean prepare lint phploc pdepend phpmd phpcs phpcpd test docs
