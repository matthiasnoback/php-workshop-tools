#!/usr/bin/env bash

docker pull matthiasnoback/php_workshop_tools_build

project_files_volume_id=$(docker create --name=project_files -v $(pwd):/opt matthiasnoback/php_workshop_tools_build)
docker run \
    --rm \
    --volumes-from=project_files \
    -v /var/run/docker.sock:/var/run/docker.sock \
    matthiasnoback/php_workshop_tools_build \
    ./build.sh

docker rm "$project_files_volume_id"
