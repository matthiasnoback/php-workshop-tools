#!/usr/bin/env bash

# Fail immediately whenever a command fails
set -e

# Build
docker build -t matthiasnoback/php_workshop_tools_base docker/base/
docker build -t matthiasnoback/php_workshop_tools_library_test docker/library_test/
docker build -t matthiasnoback/php_workshop_tools_simple_webserver docker/simple_webserver/
docker build -t matthiasnoback/php_workshop_tools_image_test docker/image_test/

# Run
docker network create test || true

docker run \
    -d \
    --name=simple_webserver \
    --rm \
    --network=test \
    -p 80:80 \
    --volumes-from=project_files \
    matthiasnoback/php_workshop_tools_simple_webserver \
    /opt/docker/image_test/opt/web

docker run \
    --rm \
    --network=test \
    --volumes-from=project_files \
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

if [[ -v BRANCH && "$BRANCH" == "master" ]]; then
    # Deploy
    if [[ -v DOCKER_USERNAME && -v DOCKER_PASSWORD ]]; then
        docker login -u="$DOCKER_USERNAME" -p="$DOCKER_PASSWORD"
    fi

    docker push matthiasnoback/php_workshop_tools_base
    docker push matthiasnoback/php_workshop_tools_simple_webserver
fi
