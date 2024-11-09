# Beaver HealthCare Vulnerable Web Appliction

---

## Prerequisites

- PHP and Dependencies: `sudo apt install -y php php-xml php-dom`
- Composer: https://getcomposer.org/download/
- Docker Compose: https://docs.docker.com/compose/install/
- Sail: `composer require laravel/sail --dev`

## Running the Application

- Change the `.env.example` file to `.env` so that Laravel can use it within the Docker image.
- Within the root of the project and run `./vendor/bin/sail up --build` in your terminal.
- In another terminal within the root of the project, run this command:
  - `./vendor/bin/sail artisan migrate && ./vendor/bin/sail artisan db:seed`
