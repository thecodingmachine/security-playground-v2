#!/usr/bin/env bash
set -euo pipefail

OWASP="${1:?Usage: $0 <01|02|04|05|06|07|08|09|10>}"
COMPOSE_FILE="${2:-docker-compose.yml}"

case "$OWASP" in
    01|02|04|05|06|07|08|09|10) ;;
    *)
        echo "Invalid OWASP module: $OWASP (expected 01, 02, 04, 05, 06, 07, 08, 09 or 10)" >&2
        exit 1
        ;;
esac

for id in 01 02 04 05 06 07 08 09 10; do
    if [[ "$id" == "$OWASP" ]]; then
        sed -i "s|^        # - ./owasp-${id}:/var/www/html|        - ./owasp-${id}:/var/www/html|" "$COMPOSE_FILE"
        sed -i "s|^        # - database_owasp_${id}_volume:/var/lib/mysql|        - database_owasp_${id}_volume:/var/lib/mysql|" "$COMPOSE_FILE"
    else
        sed -i "s|^        - ./owasp-${id}:/var/www/html|        # - ./owasp-${id}:/var/www/html|" "$COMPOSE_FILE"
        sed -i "s|^        - database_owasp_${id}_volume:/var/lib/mysql|        # - database_owasp_${id}_volume:/var/lib/mysql|" "$COMPOSE_FILE"
    fi
done

if [[ "$OWASP" == "02" ]]; then
    sed -i "s|^        # APACHE_DOCUMENT_ROOT: public|        # APACHE_DOCUMENT_ROOT: public|" "$COMPOSE_FILE"
    sed -i "s|^        APACHE_DOCUMENT_ROOT: public|        # APACHE_DOCUMENT_ROOT: public|" "$COMPOSE_FILE"
    sed -i "s|^        # APACHE_DOCUMENT_ROOT: \.|        APACHE_DOCUMENT_ROOT: .|" "$COMPOSE_FILE"
    sed -i "s|^        APP_ENV: \${ENV}|        # APP_ENV: \${ENV}|" "$COMPOSE_FILE"
    sed -i "s|^        APP_DEBUG: \${DEBUG}|        # APP_DEBUG: \${DEBUG}|" "$COMPOSE_FILE"
    sed -i "s|^        # APP_ENV: dev|        APP_ENV: dev|" "$COMPOSE_FILE"
    sed -i "s|^        # APP_DEBUG: 1|        APP_DEBUG: 1|" "$COMPOSE_FILE"
else
    sed -i "s|^        APACHE_DOCUMENT_ROOT: \.|        # APACHE_DOCUMENT_ROOT: .|" "$COMPOSE_FILE"
    sed -i "s|^        # APACHE_DOCUMENT_ROOT: public|        APACHE_DOCUMENT_ROOT: public|" "$COMPOSE_FILE"
    sed -i "s|^        APP_ENV: dev|        # APP_ENV: dev|" "$COMPOSE_FILE"
    sed -i "s|^        APP_DEBUG: 1|        # APP_DEBUG: 1|" "$COMPOSE_FILE"
    sed -i "s|^        # APP_ENV: \${ENV}|        APP_ENV: \${ENV}|" "$COMPOSE_FILE"
    sed -i "s|^        # APP_DEBUG: \${DEBUG}|        APP_DEBUG: \${DEBUG}|" "$COMPOSE_FILE"
fi

case "$OWASP" in
    02|04|05|08|10)
        cp "./owasp-${OWASP}/.env.example" "./owasp-${OWASP}/.env"
        ;;
esac

echo "Switched docker-compose.yml to owasp-${OWASP}"
