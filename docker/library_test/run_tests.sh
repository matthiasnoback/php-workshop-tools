#!/usr/bin/env bash

set -e

composer install --prefer-dist
./vendor/bin/phpunit

exit 0;
