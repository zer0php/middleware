default: help

help: ## This help message
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' -e 's/:.*#/: #/' | column -t -s '##'

install: ## Install dependencies
	composer install

install-docker: ## Install dependencies in docker
	docker run -v `PWD`:/opt/project zerosuxx/php-dev:latest composer install

tst: ## run tests
	composer test

tst-docker: ## run tests in docker
	composer test-docker

ctest: ## run tests in docker
	php -r "\$$s = json_decode(file_get_contents('composer.json'))->scripts; echo \$$s->test.' && '.\$$s->infection;" | bash