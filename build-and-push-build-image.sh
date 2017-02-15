#!/usr/bin/env bash

docker build -t matthiasnoback/php_workshop_tools_build docker/build/
docker push matthiasnoback/php_workshop_tools_build
