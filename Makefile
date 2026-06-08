.PHONY: up down back clear-cache reset-db install-deps lint owasp-01 owasp-06 owasp-07 owasp-09

# Switch OWASP module: down, update volumes, up, install deps, reset database.
owasp-01 owasp-06 owasp-07 owasp-09:
	$(MAKE) down
	@./scripts/switch-owasp.sh $(subst owasp-,,$@)
	$(MAKE) up
	$(MAKE) install-deps
	$(MAKE) reset-db

# Start the Docker Compose stack.
up:
	docker compose up -d

# Stop the Docker Compose stack.
down:
	docker compose down

# Run bash in the backend service.
back:
	docker exec -ti security-playground-v2-backend-1 bash

# Clear the Laravel application cache and optimize the framework.
clear-cache:
	docker compose exec backend php artisan optimize:clear

# Install PHP dependencies for the active OWASP module.
install-deps:
	docker compose exec -u root backend sh -c 'composer install --no-interaction && chown -R 1001:1001 vendor storage bootstrap/cache'

# Reset the database by running fresh migrations and seeding sample data.
reset-db:
	docker compose exec backend php artisan migrate:fresh --seed

# Running composer lint
lint:
	docker compose exec backend composer lint