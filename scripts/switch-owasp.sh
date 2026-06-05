#!/usr/bin/env bash
set -euo pipefail

OWASP="${1:?Usage: $0 <01|06|07|09>}"
COMPOSE_FILE="${2:-docker-compose.yml}"

case "$OWASP" in
    01|06|07|09) ;;
    *)
        echo "Invalid OWASP module: $OWASP (expected 01, 06, 07 or 09)" >&2
        exit 1
        ;;
esac

for id in 01 06 07 09; do
    if [[ "$id" == "$OWASP" ]]; then
        sed -i "s|^        # - ./owasp-${id}:/var/www/html|        - ./owasp-${id}:/var/www/html|" "$COMPOSE_FILE"
        sed -i "s|^        # - database_owasp_${id}_volume:/var/lib/mysql|        - database_owasp_${id}_volume:/var/lib/mysql|" "$COMPOSE_FILE"
    else
        sed -i "s|^        - ./owasp-${id}:/var/www/html|        # - ./owasp-${id}:/var/www/html|" "$COMPOSE_FILE"
        sed -i "s|^        - database_owasp_${id}_volume:/var/lib/mysql|        # - database_owasp_${id}_volume:/var/lib/mysql|" "$COMPOSE_FILE"
    fi
done

echo "Switched docker-compose.yml to owasp-${OWASP}"
