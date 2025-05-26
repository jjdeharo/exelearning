# Deployment

eXeLearning Web can be deployed easily using Docker. Official container images are published at Docker Hub and GitHub Container Registry at:

```
docker.io/exelearning/exelearning
ghcr.io/exelearning/exelearning
```

Docker images are automatically built for `amd64` and `arm64` architectures on every release using GitHub Actions.

---

## Quick Start

To try out eXeLearning instantly, run:

```bash
docker run -p 8080:8080 exelearning/exelearning
```

This will start eXeLearning at `http://localhost:8080` with default settings 

---

## Deployment Options

We provide ready-to-use Docker Compose configurations for the three supported database engines:

| Database   | File                                     | Notes                      |
| ---------- | ---------------------------------------- | -------------------------- |
| SQLite     | `doc/deploy/docker-compose.sqlite.yml`   | Easiest and default option |
| MariaDB    | `doc/deploy/docker-compose.mariadb.yml`  | Suitable for most uses     |
| PostgreSQL | `doc/deploy/docker-compose.postgres.yml` | Ideal for high-load setups |

To deploy eXeLearning with one of them:

```bash
docker compose -f doc/deploy/docker-compose.sqlite.yml up -d
```

Replace `sqlite.yml` with `mariadb.yml` or `postgres.yml` as needed.

---

## Configuration

You can configure the application in two ways:

1. **Using a `.env` file** in the same folder as your `docker-compose.yml`
2. **Directly inside the Compose file** using `${VARIABLE:-default}` syntax

All Compose files support variables for:

* Application settings (e.g. `APP_ENV`, `APP_SECRET`, `APP_AUTH_METHODS`)
* Database connection (`DB_DRIVER`, `DB_NAME`, `DB_USER`, etc.)
* Test user creation
* Real-time integration (Mercure keys)
* File storage path (`FILES_DIR`)
* Post-configuration setup (`POST_CONFIGURE_COMMANDS`)

> Make sure to configure secrets like `APP_SECRET` and `MERCURE_JWT_SECRET_KEY` in your `.env` file or override them when starting the container.

---

## Quick Example

```bash
# Clone the repository
git clone https://github.com/exelearning/exelearning.git
cd exelearning

# Start with SQLite (simplest)
docker compose -f doc/deploy/docker-compose.sqlite.yml up -d

# Or with MariaDB
docker compose -f doc/deploy/docker-compose.mariadb.yml up -d

# Or with PostgreSQL
docker compose -f doc/deploy/docker-compose.postgres.yml up -d
```

Access the app at [http://localhost:8080](http://localhost:8080) (or change `APP_PORT` if needed).

---

## Production Notes

* Always set a strong `APP_SECRET` value.
* Avoid using default credentials in production.
* Consider using MariaDB or PostgreSQL for multi-user environments.
* Setup volume backups and HTTPS termination with a reverse proxy like Traefik or Nginx.

