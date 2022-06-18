PORT := 8000
TEST := ""

.PHONY: test setup deploy

test:
	@php test.php $(TEST)

setup:
	@php setup.php

debug:
	@php -S localhost:$(PORT) index.php
