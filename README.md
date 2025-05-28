# BeaverHealth Vulnerable Web App
BeaverHealth is a deliberately vulnerable PHP Laravel/MySQL web application designed to educate users about a subset of common web security vulnerabilities from the OWASP Top 10. It offers a secure, controlled environment with detailed documentation for each vulnerability, including exploitation methods and remediation strategies. Our goal with this project is to help security researchers, developers, and students build a deeper understanding of web application security through hands-on experience.

## Wiki
Check out the [BeaverHealth wiki](https://github.com/BeaverHealth-Vulnerable-Web-App/BeaverHealth-Vulnerable-Web-App/wiki), which contains step-by-step exploitation guides for each vulnerability in the app, as well as educational resources covering vulnerability concepts, mitigation strategies, and security best practices.

## Quick Start
1. Install dependencies

    Make sure you have Docker and Docker Compose available on your system:

    - [Docker](https://www.docker.com/get-started/)
    - [Docker Compose](https://docs.docker.com/compose/install/) (if not included with Docker)

2. Clone the repo

3. Navigate to the project root

    ```bash
    cd ./BeaverHealth-Vulnerable-Web-App
    ```

4. Run the setup script

    ```bash
    ./setup.sh --fresh
    ```

    This will:

    - Install PHP/Laravel dependencies
    - Create `.env` (if missing)
    - Generate a secure `APP_KEY`
    - Build and start Docker containers
    - Migrate and seed the database

5. Open the app in your browser

    Go to: http://localhost:9991

    Note: Port `9991` is the default; you can change this by editing `.env` before running the setup script.

## Custom Configuration
If you'd like to override defaults (e.g., the app port), you can manually create and edit a `.env` file before running `setup.sh`. The setup script will not override your existing `.env`. You can use `.env.example` as a base. See the [configuration guide](config.md) for more information about the available options.

## Setup Script

The setup script automates the deployment of the BeaverHealth application. It accepts two mutually exclusive flags:

**`./setup.sh --fresh`**

- Performs a complete fresh deployment
- Removes existing Docker containers and volumes
- Installs Laravel dependencies
- Generates a new APP_KEY
- Rebuilds the application without using Docker cache
- Wipes and recreates the database with fresh migrations and seeding
- Clears all Laravel caches

**`./setup.sh --interactive`**

- Prompts you to choose which deployment actions to perform
- Allows selective execution of setup steps
- Useful for updates or troubleshooting specific components
- Options include: resetting Docker state, installing dependencies, generating APP_KEY, rebuilding the app, migrating/seeding database, and clearing caches

Use `--fresh` for initial setup or when you want to completely reset everything. Use `--interactive` when you only need to update specific parts of the application.

## Disclaimer
This application is intended for educational and testing purposes only, and we do not take responsibility for any misuse.

## Warning
Do not expose this application to the internet, as it contains vulnerabilities that could be exploited, potentially compromising your system. If you are not fully confident in your network security settings, consider running it in a virtual machine with NAT mode to better isolate it from your host system.
