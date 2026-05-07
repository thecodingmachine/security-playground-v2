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
	docker compose exec security-playground-v2-backend-1 php artisan optimize:clear

# Reset the database by running fresh migrations and seeding sample data.
reset-db:
	docker compose exec security-playground-v2-backend-1 php artisan db:wipe --database=mysql
	docker compose exec security-playground-v2-backend-1 php artisan migrate
	docker compose exec security-playground-v2-backend-1 php artisan db:seed

# Running composer lint
lint:
	docker compose exec security-playground-v2-backend-1 composer lint