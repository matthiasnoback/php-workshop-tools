#!/usr/bin/env bash

# Fail immediately whenever a command fails
set -e

# Build
docker-compose build

# Test
docker-compose up -d
docker-compose ps

IMAGE_TEST_SERVICE_NAME="image_test"
image_test_container_id=$(docker-compose ps -q "$IMAGE_TEST_SERVICE_NAME")
image_test_exit_code=$(docker wait "$image_test_container_id")

LIBRARY_TEST_SERVICE_NAME="library_test"
library_test_container_id=$(docker-compose ps -q "$LIBRARY_TEST_SERVICE_NAME")
library_test_exit_code=$(docker wait "$library_test_container_id")

docker-compose stop

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

    docker-compose push
fi
