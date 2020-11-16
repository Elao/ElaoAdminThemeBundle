###########
# Install #
###########

install:
	composer install

########
# Lint #
########

lint: lint-twig

lint-twig:
	php bin/lint.twig.php Resources/views
