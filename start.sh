#!/usr/bin/env bash

set -e

CUR_FILE=`basename "$0"`
HELP="Usage: ./${CUR_FILE} @action [@options|--help|-h]
    --help|-h shows this help text
actions:
    init: initialises the project from scratch (please make sure you have docker and docker compose installed on the machine)
    tests: run tests
        options:
            -f Run only functional tests
            -u: Run only unit tests
"

init() {
    docker-compose up -d
    docker-compose exec app composer install
    docker-compose exec app php bin/console doctrine:migrations:migrate -n
}

cmd() {
    docker-compose exec app php bin/console $@
}

tests() {
    docker-compose up -d
    ops=''
    while getopts fu option; do
        case "$option" in
            f) ops='--group=integration'
            ;;
            u) ops='--exclude-group=integration'
            ;;
        esac
    done
    docker-compose exec app php bin/phpunit ${ops}
}

route() {
    for i in "$@" ; do
        if [[ $i == "--help" ]] || [[ $i == "-h" ]]; then
            echo "${HELP}"
            exit
        fi
    done

    case ${1} in
        init) init
           ;;
        tests) tests ${@:2}
           ;;
        cmd) cmd ${@:2}
           ;;
        help|h) echo ${HELP}
           ;;
        *) exitWithFailure "illegal action: ${1}"
           ;;
    esac
}

exitWithFailure() {
    echo "${1}" >&2
    echo "${HELP}" >&2
    exit 1
}

route ${@}