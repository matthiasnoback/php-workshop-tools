#!/usr/bin/env bash

set -e

composer install
./vendor/bin/phpunit

exit 0;
