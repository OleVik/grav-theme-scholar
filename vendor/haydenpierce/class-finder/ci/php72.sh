#!/usr/bin/env bash
set -Eeuxo pipefail

composer install --working-dir=$1/test/app1 --quiet || exit 1
composer install --working-dir=$1/test/app2 --quiet || exit 1
composer install --working-dir=$1 --quiet || exit 1
php --version
php $1/vendor/bin/phpunit --testsuite all
php $1/vendor/bin/phpunit --testsuite noAutoload
php -d disable_functions=exec ./vendor/phpunit/phpunit/phpunit ./test/app1/src/ClassFinderTest.php --filter=testWorksWhenExecIsDisabled
