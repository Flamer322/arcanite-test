init: docker-down-clear \
	docker-build docker-up \
	composer-install make-env generate-key migrate
up: docker-up
down: docker-down
restart: down up

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-build:
	docker compose build

composer-update:
	docker compose run --rm php-cli composer update

composer-install:
	docker compose run --rm php-cli composer install

migrate:
	docker compose run --rm php-cli php artisan migrate

make-env:
	docker compose run --rm php-cli cp .env.example .env

generate-key:
	docker compose run --rm php-cli php artisan key:generate
