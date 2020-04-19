#!/usr/bin/env bash

CURRENT_BASH=$(ps -p $$ | awk '{ print $4 }' | tail -n 1)
case "${CURRENT_BASH}" in
-zsh | zsh)
    CURRENT_DIR=$(cd "$(dirname "${0}")" && pwd)
    ;;
-bash | bash)
    CURRENT_DIR=$(cd "$(dirname ${BASH_SOURCE[0]})" && pwd)
    ;;
*)
    echo 1>&2
    echo -e "\033[0;31m\`${CURRENT_BASH}\` does not seems to be supported\033[0m" 1>&2
    echo 1>&2
    return 1
    ;;
esac

unalias php 2>/dev/null >/dev/null || true
php() {
    docker exec -it app_php sh -c "php $*"
}
export -f php

unalias composer 2>/dev/null >/dev/null || true
composer() {
    docker exec -it app_tools sh -c "COMPOSER_MEMORY_LIMIT=-1 composer $*"
}
export -f composer

unalias csv2json 2>/dev/null >/dev/null || true
csv2json() {
    docker exec -it app_php sh -c "./bin/csv2json $*"
}
export -f csv2json

unalias unit-tests 2>/dev/null >/dev/null || true
unit-tests() {
    docker exec -it app_php sh -c "./bin/unit-tests $*"
}
export -f unit-tests
