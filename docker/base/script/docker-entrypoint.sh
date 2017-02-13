#!/usr/bin/env bash

set -e

if [ "$#" -eq 0 ]; then
    exit 0
fi

exec "$@"
