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
	@echo "  test       launch tests"
	@echo "  phpcs      show code sniffer result"
	@echo "  phpmd      show mess detector result"
	@echo "  pdepend    show quality of design result"

.PHONY:
test:
	$(PHP) $(PHPUNIT) --configuration="$(PHPUNIT_CONFIG)" tests

.PHONY:
phpcs:
	$(PHP) $(PHPCS) --report=full

.PHONY:
phpmd:
	$(PHP) $(PHPMD) $(FOLDERS) text $(PHPMD_CONFIG)

.PHONY:
pdepend:
	$(PHP) $(PDEPEND) --configuration="/$(PDEPEND_CONFIG)" $(FOLDERS)
