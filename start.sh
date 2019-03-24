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
    docker-compose exec php composer install
    docker-compose exec php bin/console doctrine:migrations:migrate
}

tests() {
    while getopts d option; do
        case "$option" in
            f) docker-compose exec php php bin/phpunit --group=functional
            ;;
            u) docker-compose exec php php bin/phpunit --exclude-group=functional
            ;;
            *) docker-compose exec php php bin/phpunit
            ;;
        esac
    done
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