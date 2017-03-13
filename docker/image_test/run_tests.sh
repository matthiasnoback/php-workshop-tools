#!/usr/bin/env bash

set -e

function success {
    echo -e "\033[32m✔ $1\033[0m"
}

function failure {
    echo "Failed test:"
    echo -e "\033[31m✘ $1\033[0m"
    exit 1
}

output=$(curl http://simple_webserver:8080/ 2> /dev/null)
if [ "$output" != "It works!" ]; then
    failure "simple_webserver didn't serve a PHP file"
else
    success "simple_webserver serves a PHP file"
fi

output=$(curl http://simple_webserver:8080/static.html 2> /dev/null)
if [ "$output" != "Static file" ]; then
    failure "simple_webserver didn't serve a static HTML file"
else
    success "simple_webserver serves a static HTML file"
fi

echo "All tests passed"

exit 0
