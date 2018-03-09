PHP=php
PHPUNIT=bin/phpunit
PHPCS=vendor/bin/phpcs
PHPMD=vendor/bin/phpmd
PDEPEND=vendor/bin/pdepend
PHPUNIT_CONFIG=phpunit.xml
PHPMD_CONFIG=phpmd.xml
PDEPEND_CONFIG=pdepend.xml
FOLDERS=src,tests

ifeq ("$(wildcard $(PHPUNIT_CONFIG))","")
PHPUNIT_CONFIG=phpunit.xml.dist
endif

ifeq ("$(wildcard $(PHPMD_CONFIG))","")
PHPMD_CONFIG=phpmd.xml.dist
endif

ifeq ("$(wildcard $(PDEPEND_CONFIG))","")
PDEPEND_CONFIG=pdepend.xml.dist
endif

.PHONY:
help:
	@echo "ServerStatus: please use \`make <target>\` where <target> is one of:"
	@echo "  test           launch tests"
	@echo "  phpcs          show code sniffer result"
	@echo "  phpmd          show mess detector result"
	@echo "  pdepend        show quality of design result"
	@echo "  fixtures-dev   load fixtures in dev environment's db"

.PHONY:
test:
	$(PHP) $(PHPUNIT) --configuration="$(PHPUNIT_CONFIG)"

.PHONY:
phpcs:
	$(PHP) $(PHPCS) --report=full

.PHONY:
phpmd:
	$(PHP) $(PHPMD) $(FOLDERS) text $(PHPMD_CONFIG)

.PHONY:
pdepend:
	$(PHP) $(PDEPEND) --configuration="/$(PDEPEND_CONFIG)" $(FOLDERS)

.PHONY: fixtures-dev
fixtures-dev:
	$(PHP) bin/console doctrine:schema:drop --env=dev --force
	$(PHP) bin/console doctrine:schema:create --env=dev
	$(PHP) bin/console doctrine:fixtures:load --env=dev --append --no-interaction
