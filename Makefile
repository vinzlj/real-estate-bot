CURRENT_DIR = $(dir $(abspath $(lastword $(MAKEFILE_LIST))))
PCF_IMAGE = cytopia/php-cs-fixer
PCF_VERSION = 2

.PHONY: cs-fix

cs-fix:
	@docker run --rm -v $(CURRENT_DIR):/data $(PCF_IMAGE):$(PCF_VERSION) fix --diff .
