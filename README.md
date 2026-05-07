# SECURITY PLAYGROUND V2

This project is a hands-on demo for OWASP training: it illustrates risks and good practices across the following Top 10 categories:

- **A01** Broken Access Control
- **A02** Security Misconfiguration
- **A03** Software Supply Chain Failures
- **A04** Cryptographic Failures
- **A05** Injection
- **A06** Insecure Design
- **A07** Authentication Failures
- **A08** Software or Data Integrity Failures
- **A09** Security Logging and Alerting Failures
- **A10** Mishandling of Exceptional Conditions

## Prerequisites

The project essentially uses Docker, so you need to have it installed and up to date on your machine. You can install it via [Docker Desktop](https://www.docker.com/products/docker-desktop/).

## The web application

#### 1. Clone the project

```shell
git clone git@github.com:thecodingmachine/security-playground-v2.git
```

#### 2. Set up the environment variables (password variables mostly)

```shell
cp .env.example .env
```

#### 3. Project with HTTPS

The project works with HTTPS in local, so you need to create a self-signed local certificates to emulate secure protocol.

- First, you need to install [mkcert](https://github.com/FiloSottile/mkcert?tab=readme-ov-file#installation).
- If it's your first time installing `mkcert` on your machine, you need to run `mkcert -install` once. You will not need to do this ever again.
- Then, you need to create a folder `certs` in the folder `orchestrator` of the project and run the following commands:

```shell
cd orchestrator/certs
mkcert -cert-file local-cert.pem -key-file local-key.pem "owasp.localhost" "*.owasp.localhost"
```

Two files should be generated: `local-cert.pem` and `local-key.pem`. And then you're good to go as far as HTTPS is concerned.

#### 4. Once you've made sure all environment variables are properly set, you can start the project and access its services with the URLs given above.

```shell
docker compose up -d
```

#### 5. Make sure all dependencies are installed, all migrations are run & seed the database (backend container)

```shell
make back
composer install
php artisan migrate:fresh --seed
exit
```

## Available commands

If you wish to reset your database with test data, you can use this command:

```bash
make reset-db
```

If you want to clear the cache:

```bash
make clear-cache
```
