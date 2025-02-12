# Arcanite test

## Project Description

This is a test assigment for Arcanite.

## Project structure

- **.github/** - directory with configuration files for GitHub actions
- **backend/** - PHP (Laravel) application with REST API
- **docker/** - application code
- **docker-compose.yaml** - compose file describing project environment
- **Makefile** - file helping automate routine actions

## Setup

Run init command with make:

- `make init`

Or execute commands manually:

- `docker compose down -v --remove-orphans`
- `docker compose build`
- `docker compose up -d`
- `docker compose run --rm php-cli cp .env.example .env`
- `docker compose run --rm php-cli composer install`
- `docker compose run --rm php-cli php artisan key:generate`
- `docker compose run --rm php-cli php artisan migrate`

Then set env variables for your Telegram bot in backend/.env file:

- TELEGRAM_BOT_TOKEN=\<token of your bot\>
- TELEGRAM_WEBHOOK_URL=https://\<your host address\>/telegram/webhook
- ORDER_API_HOST=https://\<address of orders api host\>

And finally set up webhook by running command:

- `make setup-webhook`

Or manually:

- `docker compose run --rm php-cli php artisan telegram:webhook --setup`
