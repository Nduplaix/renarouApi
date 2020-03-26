DOCKER_COMPOSE = docker-compose

EXEC_PHP       = $(DOCKER_COMPOSE) exec php /entrypoint

SYMFONY        = $(EXEC_PHP) bin/console
COMPOSER       = $(EXEC_PHP) composer

##
## Project
## -------
##
configure: docker/data/history

pull: configure
	$(DOCKER_COMPOSE) pull
	$(DOCKER_COMPOSE) build --pull

build: configure
	$(DOCKER_COMPOSE) build

kill:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

install: configure build start db                            ## Install and start the project

reset: kill install                                          ## Stop and start a fresh install of the project

start: configure                                             ## Start the project
	$(DOCKER_COMPOSE) up -d
	$(DOCKER_COMPOSE) ps

restart: stop start                                          ## Restart the project

stop:                                                        ## Stop the project
	$(DOCKER_COMPOSE) down --remove-orphans

clean: kill                                                  ## Stop the project and remove generated files
	rm -rf vendor var/cache/* var/log/* var/sessions/* docker/data/*

open:
	@xdg-open "http://$$(cat .env | grep DOMAIN_DEFAULT | cut -d= -f2)" > /dev/null 2>&1

.PHONY: configure pull build kill install reset start stop clean open

##
## Utils
## -----
##

db: configure vendor                                        ## Reset the database and load fixtures
	rm -rf public/uploads/pdf/* public/uploads/images/*
#	@$(EXEC_PHP) php docker/php/wait-database.php
	$(SYMFONY) doctrine:database:drop --if-exists --force
	$(SYMFONY) doctrine:database:create --if-not-exists
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration
#	$(SYMFONY) doctrine:fixtures:load --no-interaction --append

cc:                                                          ## Clear the cache in dev env
	$(SYMFONY) cache:clear --no-warmup
	$(SYMFONY) cache:warmup

php:                                                         ## Run interactive bash inside php
	$(DOCKER_COMPOSE) run --rm php bash

xdebug:                                                      ## Run interactive xdebug bash inside php
	$(DOCKER_COMPOSE) run --rm -e XDEBUG_CLI=1 php bash

mysql:                                                       ## Connect to mysql
	$(DOCKER_COMPOSE) exec mysql bash -c 'mysql -u $$MYSQL_USER -p"$$MYSQL_PASSWORD" $$MYSQL_DATABASE'

mysqldump:                                                   ## Mysqldump
	$(DOCKER_COMPOSE) exec mysql bash -c 'mysqldump -u $$MYSQL_USER -p"$$MYSQL_PASSWORD" $$MYSQL_DATABASE' > docker/data/mysqldump_$$(date +%Y-%m-%d_%H:%M).sql

.PHONY: db cc php xdebug mysql mysqldump

# rules based on files
composer.lock: composer.json
	$(COMPOSER) update --lock --no-scripts --no-interaction

vendor: composer.lock
	$(COMPOSER) install

docker/data/history:
	touch docker/data/history

##
## Quality assurance
## -----------------
##

lint: ly                                                     ## Lints twig and yaml files

ly: vendor
	$(SYMFONY) lint:yaml config

php-cs-fixer:                                                ## php-cs-fixer (http://cs.sensiolabs.org)
	$(EXEC_PHP) ./vendor/bin/php-cs-fixer fix --verbose --diff

.PHONY: lint ly php-cs-fixer

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
