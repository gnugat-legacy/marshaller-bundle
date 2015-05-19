#!/usr/bin/env bash

marshaller_bundle="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"

rm -rf $marshaller_bundle/Tests/app/cache/* $marshaller_bundle/Tests/app/logs/*
$marshaller_bundle/vendor/bin/phpunit
