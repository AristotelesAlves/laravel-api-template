# Laravel API Template

<!--
Template logo:
- Replace the image URL below with your own template/project logo when needed.
- Recommended local path: docs/assets/logo.svg or docs/assets/logo.png
- Recommended width: 220-320px
-->
<p align="center">
  <img src="https://upload.wikimedia.org/wikipedia/commons/1/11/Laravel_logotype_min.svg" alt="Laravel API Template logo" width="260">
</p>

<p align="center">
  A Docker-ready Laravel API starter template with PostgreSQL, Redis, Nginx, Sanctum, and Make commands.
</p>

A reusable template for quickly starting Laravel APIs with Docker, PostgreSQL, Redis, Nginx, Laravel Sanctum, and a Makefile for the most common development commands.

The goal of this project is to be used as a base: clone or copy it, rename what you need, adjust the `.env` variables, and start building without recreating the infrastructure from scratch.

## What is included

- Laravel 13 with PHP 8.3.
- Docker Compose with app, Nginx, PostgreSQL, and Redis containers.
- `docker/Dockerfile` organized inside the `docker` directory.
- `.dockerignore` to keep Docker build contexts small and avoid copying local-only files.
- Nginx configuration at `docker/nginx/default.conf`.
- Laravel Sanctum authentication using Bearer Tokens.
- Login, logout, authenticated user, and protected test endpoints.
- Seeder with an initial user.
- Simple structure with Controllers, Requests, Resources, Services, Repositories, and DTOs.
- Makefile shortcuts for starting the environment, installing dependencies, running migrations, running tests, checking code style, clearing cache, and fixing permissions.
- PHPUnit configuration and feature tests for the authentication flow.
- GitHub Actions workflow for tests and Laravel Pint.
- Bruno collection for testing the API.

## Stack

- PHP 8.3
- Laravel 13
- PostgreSQL 16
- Redis 7.4
- Nginx 1.27
- Laravel Sanctum
- PHPUnit
- Laravel Pint
- GitHub Actions
- Docker Compose
- Bruno API Client

## Requirements

Install these tools on your machine:

- Docker
- Docker Compose
- Make

You do not need PHP, Composer, PostgreSQL, or Redis installed locally. Everything runs inside containers.

## Project structure

```text
.
├── .github/
│   └── workflows/
│       └── ci.yml
├── .dockerignore
├── app/
│   ├── DTO/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Models/
│   ├── Repositories/
│   └── Services/
├── bruno/
├── config/
├── database/
├── docker/
│   ├── Dockerfile
│   └── nginx/
│       └── default.conf
├── routes/
├── scripts/
│   └── rename-template.sh
├── storage/
├── tests/
├── docker-compose.yml
├── Makefile
├── phpunit.xml
└── README.md
```

## Using this as a template

Clone or copy this project into a new directory:

```bash
cp -r laravel-api-template my-api
cd my-api
```

Then adjust the project names as needed:

- Run `make rename PROJECT=my-api` to update the main template metadata automatically.
- `composer.json`: review `name` and `description`.
- `.env`: review `APP_NAME`, `APP_URL`, `COMPOSE_PROJECT_NAME`, `APP_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.
- `database/seeders/DatabaseSeeder.php`: update the initial user.
- `README.md`: update the documentation for the new project.

If you use Git, initialize a new repository after copying:

```bash
rm -rf .git
git init
```

## Renaming the template

Use the `rename` target after copying this template into a new project directory:

```bash
make rename PROJECT=my-api
```

This updates:

- `APP_NAME`, `COMPOSE_PROJECT_NAME`, `DB_DATABASE`, and `DB_USERNAME` in `.env.example`.
- The same variables in `.env`, if the file already exists.
- `name` and `description` in `composer.json`.
- The README title, logo alt text, and default template names.

After renaming, refresh the Composer lock metadata when dependencies are available:

```bash
make composer CMD="update --lock"
```

You can also set a different Composer vendor:

```bash
make rename PROJECT=my-api VENDOR=acme
```

That produces a Composer package name like:

```text
acme/my-api
```

Allowed `PROJECT` characters:

- letters
- numbers
- hyphens
- underscores

The script converts underscores to hyphens for the project slug and to underscores for database names.

## Template logo

The README already includes a Laravel logotype at the top so the template has a clean visual identity from the first commit.

To replace it with your own logo, edit the `<img>` tag near the top of this file:

```html
<img src="https://upload.wikimedia.org/wikipedia/commons/1/11/Laravel_logotype_min.svg" alt="Laravel API Template logo" width="260">
```

Recommended local structure:

```text
docs/
└── assets/
    └── logo.svg
```

Then update the image path:

```html
<img src="docs/assets/logo.svg" alt="Project logo" width="260">
```

Recommended logo guidelines:

- Use SVG when possible.
- Keep the logo width between `220px` and `320px`.
- Use a transparent background.
- Keep the `alt` text updated with the real project name.
- Replace the Laravel logo if the new project should have its own brand.

The default image is the Laravel logotype from Wikimedia Commons, sourced from laravel.com and authored by Laravel LLC. Laravel brand assets may be protected as trademarks, so use your own logo for client or product-specific projects when appropriate.

## First setup

Copy the environment file:

```bash
cp .env.example .env
```

Start the containers and prepare Laravel:

```bash
make setup
```

The `make setup` command runs these targets in order:

```text
make up
make install
make key
make migrate
make seed
make cache-reset
make cache-clear
make fix-permissions
```

When it finishes, the API is available at:

```text
http://localhost:8080
```

## Manual setup without Make

If you prefer to run everything manually:

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

## Containers

The `docker-compose.yml` file creates these services:

| Service | Purpose |
| --- | --- |
| `app` | PHP-FPM, Composer, and Artisan |
| `nginx` | HTTP server for the application |
| `postgres` | PostgreSQL database |
| `redis` | Cache, sessions, and queues |

Container names are generated by Docker Compose from `COMPOSE_PROJECT_NAME`. This avoids hardcoded container names and makes it easier to run multiple projects based on this template on the same machine.

Exposed ports:

| Local port | Container | Usage |
| --- | --- | --- |
| `8080` | `nginx:80` | HTTP API |
| `5432` | `postgres:5432` | PostgreSQL |
| `6379` | `redis:6379` | Redis |

If another application is already using one of these ports, update these variables in `.env`:

```text
APP_PORT=8080
DB_FORWARD_PORT=5432
REDIS_FORWARD_PORT=6379
```

For example, to expose the API on port `8081`:

```text
APP_PORT=8081
APP_URL=http://localhost:8081
```

## Dockerfile

The Dockerfile is located at:

```text
docker/Dockerfile
```

Compose still uses the project root as the build context:

```yaml
build:
  context: .
  dockerfile: docker/Dockerfile
```

This allows `COPY . .` inside the Dockerfile to copy the entire project into `/var/www/html` without changing paths.

## Make commands

Run `make help` to list all available commands.

### Environment

```bash
make up
```

Starts the containers in the background.

```bash
make down
```

Stops and removes the containers. Database, Redis, and vendor volumes are kept.

```bash
make restart
```

Restarts the containers.

```bash
make build
```

Rebuilds the images and starts the containers with `--build`. Use this after changing `docker/Dockerfile`, PHP extensions, or image-level configuration.

```bash
make status
```

Shows the container status.

```bash
make logs
```

Shows container logs in real time.

```bash
make shell
```

Opens a shell inside the `app` container.

### Laravel and dependencies

```bash
make install
```

Runs `composer install` inside the `app` container.

```bash
make key
```

Generates the `APP_KEY` in `.env`.

```bash
make migrate
```

Runs pending migrations.

```bash
make seed
```

Runs the seeders.

```bash
make fresh
```

Recreates the database from scratch and runs the seeders with `php artisan migrate:fresh --seed`.

```bash
make test
```

Runs the Laravel test suite.

```bash
make test-filter FILTER=AuthTest
```

Runs only tests matching the given filter.

```bash
make pint
```

Checks code style with Laravel Pint.

```bash
make pint-fix
```

Automatically fixes code style with Laravel Pint.

```bash
make lint
```

Alias for `make pint`.

```bash
make artisan CMD="route:list"
```

Runs an Artisan command inside the `app` container.

```bash
make composer CMD="require vendor/package"
```

Runs a Composer command inside the `app` container.

```bash
make routes
```

Lists application routes.

```bash
make tinker
```

Opens Laravel Tinker.

```bash
make queue
```

Processes one queued job with `php artisan queue:work --once`.

### Cache and permissions

```bash
make cache-clear
```

Runs `php artisan optimize:clear`.

```bash
make cache-reset
```

Removes problematic cache files from `bootstrap/cache` and runs `composer dump-autoload`.

Use this when Laravel keeps trying to load old providers, classes, or configuration.

```bash
make fix-permissions
```

Fixes ownership and permissions for `bootstrap/cache`, `storage`, and `composer.lock` after Docker commands.

Use this when you get permission errors for cache, logs, sessions, views, or `composer.lock`.

```bash
make perms
```

Alias for `make fix-permissions`.

## Common development workflow

First run:

```bash
cp .env.example .env
make setup
```

Daily usage:

```bash
make up
make logs
```

Run Artisan commands:

```bash
make routes
make artisan CMD="make:model Product -m"
make artisan CMD="make:controller ProductController"
```

Run Composer commands:

```bash
make composer CMD="require vendor/package"
make composer CMD="dump-autoload"
```

Enter the container:

```bash
make shell
```

Stop the environment:

```bash
make down
```

## Database

The default Compose credentials are:

```text
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=laravel_template
DB_USERNAME=laravel_template
DB_PASSWORD=secret
```

Inside containers, the database host is `postgres`, which is the Compose service name.

To connect from a local database client, use:

```text
host: localhost
port: 5432
database: laravel_template
user: laravel_template
password: secret
```

## Redis

Inside containers, the Redis host is:

```text
REDIS_HOST=redis
REDIS_PORT=6379
```

Redis is available for cache, sessions, queues, or locks depending on your `.env` configuration.

## Authentication

This template includes Sanctum login.

There is no registration endpoint by default. The initial user is created by the seeder.

Initial credentials:

```text
email: admin@example.com
password: password
```

Available routes:

| Method | Route | Auth | Description |
| --- | --- | --- | --- |
| `POST` | `/api/login` | No | Returns a Bearer token |
| `POST` | `/api/logout` | Yes | Revokes the current token |
| `GET` | `/api/me` | Yes | Returns the authenticated user |
| `GET` | `/api/protected-test` | Yes | Simple route for validating auth |

Test login:

```bash
curl -X POST http://localhost:8080/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

Use the token returned in `data.token`:

```bash
curl http://localhost:8080/api/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Logout:

```bash
curl -X POST http://localhost:8080/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Bruno

The `bruno` directory contains a collection for testing the API.

How to use it:

1. Open Bruno.
2. Open the `bruno` directory as a collection.
3. Select the `Docker` environment.
4. Run the `Login` request.
5. Use the protected requests after logging in.

Available requests:

- `Login`
- `Me`
- `Protected Test`
- `Logout`

The `Login` request tries to automatically save the `token` environment variable from `data.token`. Protected routes use:

```text
Authorization: Bearer {{token}}
```

## Architecture pattern

This template uses a simple structure for APIs:

- `Controllers`: receive the request, call services, and return responses/resources.
- `Requests`: validate input and transform data when needed.
- `Resources`: standardize API output.
- `Services`: hold application rules and orchestrate flows.
- `Repositories`: handle queries and persistence with Eloquent.
- `DTOs`: move data between layers with simple contracts.
- `Models`: Laravel Eloquent models.

The goal is to keep controllers small and avoid spreading business rules across the codebase.

A separate domain layer should be added only when the project has enough business rules to justify that boundary.

## Creating a new feature

Example flow for creating a `Product` resource:

```bash
make artisan CMD="make:model Product -m"
make artisan CMD="make:controller ProductController"
make artisan CMD="make:request StoreProductRequest"
make artisan CMD="make:resource ProductResource"
```

Then create these files if they make sense for the feature:

```text
app/Services/ProductService.php
app/Repositories/ProductRepository.php
app/DTO/Product/StoreProductDTO.php
```

Register routes in:

```text
routes/api.php
```

Run migrations and tests:

```bash
make migrate
make test
```

## Quality checks

This template includes a basic quality gate:

- PHPUnit configuration in `phpunit.xml`.
- Feature tests for the authentication flow in `tests/Feature/AuthTest.php`.
- Laravel Pint for code style.
- GitHub Actions workflow in `.github/workflows/ci.yml`.

Run the full test suite:

```bash
make test
```

Run a specific test class or method:

```bash
make test-filter FILTER=AuthTest
```

Check code style:

```bash
make pint
```

Fix code style:

```bash
make pint-fix
```

The CI workflow runs on pushes to `main` or `master` and on pull requests. It installs Composer dependencies, prepares the Laravel environment, runs tests, and checks code style with Pint.

## Customizing for a new project

Recommended checklist when creating a project from this template:

- Update `APP_NAME`, `APP_URL`, `COMPOSE_PROJECT_NAME`, and exposed ports in `.env`.
- Update the package name in `composer.json`.
- Update the database name and credentials in `.env`.
- Update the initial user in `database/seeders/DatabaseSeeder.php`.
- Update tests under `tests/` for the new project rules.
- Update `.github/workflows/ci.yml` if the project needs extra CI steps.
- Remove auth endpoints that do not make sense for the new project.
- Update the Bruno collection.
- Update this README with the real rules of the new project.

## Common issues

### Port already in use

If `8080`, `5432`, or `6379` is already in use, update the forwarded ports in `.env`.

Example:

```text
APP_PORT=8081
APP_URL=http://localhost:8081
```

Then access:

```text
http://localhost:8081
```

### Permission error in storage or cache

Run:

```bash
make fix-permissions
```

### Laravel keeps loading an old class or provider

Run:

```bash
make cache-reset
make cache-clear
```

### Dependencies do not appear in the local project

The project uses a named volume for `/var/www/html/vendor`:

```yaml
volumes:
  - vendor:/var/www/html/vendor
```

This prevents the local bind mount from overwriting the `vendor` directory installed inside the container.

To reinstall dependencies:

```bash
make install
```

### Recreate the database from scratch

Use:

```bash
make fresh
```

This drops the tables, runs migrations, and runs seeders again.

### Rebuild Docker images

Use:

```bash
make build
```

Use this mainly after changing `docker/Dockerfile`.

## Useful commands without Make

Equivalent commands:

```bash
docker compose up -d
docker compose down
docker compose ps
docker compose logs -f
docker compose exec app sh
docker compose exec app composer install
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan test
docker compose exec app php artisan optimize:clear
docker compose exec app ./vendor/bin/pint --test
docker compose exec app ./vendor/bin/pint
```

## Technical notes

- Main tables use UUIDs.
- The `personal_access_tokens` table uses `uuidMorphs` to support UUID users.
- Passwords should never be returned from resources.
- The user model is located at `app/Models/User.php`, following the Laravel convention.
- This project is intended for APIs, not Blade applications with a server-side frontend.

## License

This template is open-sourced software licensed under the [MIT license](LICENSE).
