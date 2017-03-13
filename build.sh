#!/usr/bin/env bash

# Fail immediately whenever a command fails
set -e

# Build
docker build -t matthiasnoback/php_workshop_tools_base docker/base/
docker build -t matthiasnoback/php_workshop_tools_library_test docker/library_test/
docker build -t matthiasnoback/php_workshop_tools_simple_webserver docker/simple_webserver/
docker build -t matthiasnoback/php_workshop_tools_image_test docker/image_test/

# Run
docker network create test 2> /dev/null || true

docker run \
    -d \
    --name=simple_webserver \
    --rm \
    --network=test \
    -v $(pwd):/opt \
    matthiasnoback/php_workshop_tools_simple_webserver \
    /opt/docker/image_test/document_root

docker run \
    --rm \
    --network=test \
    -v $(pwd):/opt \
    matthiasnoback/php_workshop_tools_library_test
library_test_exit_code=$?

docker run \
    --rm \
    --network=test \
    matthiasnoback/php_workshop_tools_image_test
image_test_exit_code=$?

docker kill simple_webserver
docker network rm test

if (( image_test_exit_code > 0 )); then
    echo "Image tests failed, so we don't push the new images"
    docker logs $image_test_container_id
    exit 1;
fi

if (( library_test_exit_code > 0 )); then
    echo "Library tests failed, so we don't push the new images"
    docker logs $library_test_container_id
    exit 1;
fi

if [[ "$1" == "push" ]]; then
    docker push matthiasnoback/php_workshop_tools_base
    docker push matthiasnoback/php_workshop_tools_simple_webserver
fi
