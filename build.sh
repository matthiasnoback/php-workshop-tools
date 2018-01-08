#!/usr/bin/env bash

# Fail immediately whenever a command fails, fail if a variable is undefined, show all commands
set -eux

# Build
docker build -t matthiasnoback/php_workshop_tools_base:latest docker/base/
docker build -t matthiasnoback/php_workshop_tools_simple_webserver:latest docker/simple_webserver/
docker build -t matthiasnoback/php_workshop_tools_library_test:latest docker/library_test/
docker build -t matthiasnoback/php_workshop_tools_image_test:latest docker/image_test/

# Upon exit, clean up
function clean_up {
    docker kill simple_webserver
    docker network rm test
}
trap clean_up EXIT

# Setup
docker network create test 2> /dev/null || true

docker run \
    -d \
    --name=simple_webserver \
    --rm \
    --network=test \
    -v $(pwd):/opt \
    matthiasnoback/php_workshop_tools_simple_webserver:latest \
    /opt/docker/image_test/document_root

# Run tests
docker run \
    --rm \
    --network=test \
    -v $(pwd):/opt \
    matthiasnoback/php_workshop_tools_library_test:latest

docker run \
    --rm \
    --network=test \
    matthiasnoback/php_workshop_tools_image_test:latest

# Optionally, push the new images
if [[ "$1" == "push" ]]; then
    docker push matthiasnoback/php_workshop_tools_base:latest
    docker push matthiasnoback/php_workshop_tools_simple_webserver:latest
fi
