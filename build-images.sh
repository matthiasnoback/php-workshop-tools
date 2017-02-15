#!/usr/bin/env bash

# Fail immediately whenever a command fails
set -e

# Build
docker-compose build

# Test
docker-compose up -d
test_exit_code=$(docker wait $(docker-compose ps -q test))
if (( test_exit_code > 0 )); then
    echo "Tests failed, so we don't push the new images"
    exit 1;
fi

if [ "$TRAVIS_BRANCH" == "master" ]; then
    # Deploy
    if [[ -v DOCKER_USERNAME && -v DOCKER_PASSWORD ]]; then
        docker login -u="$DOCKER_USERNAME" -p="$DOCKER_PASSWORD"
    fi

    docker-compose push
fi
