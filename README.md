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

#### 4. Start the OWASP module you want

Each module lives in its own folder (`owasp-01`, `owasp-06`, `owasp-07`, `owasp-09`). To start one, run the matching Make target:

```shell
make owasp-01   # A01 - Broken Access Control
make owasp-06   # A06 - Insecure Design
make owasp-07   # A07 - Authentication Failures
make owasp-09   # A09 - Security Logging and Alerting Failures
```

This command stops the stack, mounts the right application and database volume, starts the containers, installs PHP dependencies, and resets the database with seed data.

Other useful commands:

```shell
make reset-db      # reset the database of the active module
make clear-cache   # clear the Laravel cache
make back          # open a shell in the backend container
```
