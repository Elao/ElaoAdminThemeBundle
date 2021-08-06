.SILENT:

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

## Help
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

###########
# Install #
###########

## Install dependencies
install:
	composer update

########
# Lint #
########

## Run code style checks
lint: lint.php-cs-fixer lint.phpstan lint.composer lint.twig

lint.php-cs-fixer:
	vendor/bin/php-cs-fixer fix

lint.phpstan:
	vendor/bin/phpstan analyse .

lint.composer:
	composer validate --strict

lint.twig:
	php bin/lint.twig.php Resources/views

############
# Security #
############

# Run Symfony security check
security.symfony:
	symfony check:security

########
# Test #
########

## Run tests
test:
	vendor/bin/simple-phpunit
