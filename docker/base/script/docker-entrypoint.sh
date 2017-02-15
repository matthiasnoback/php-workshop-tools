#!/usr/bin/env bash

set -e

if [ "$#" -eq 0 ]; then
    echo "Provide a command that you'd like to run inside the container"
    exit 0
fi

exec "$@"
