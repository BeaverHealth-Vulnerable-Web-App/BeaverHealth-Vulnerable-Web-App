# BeaverHealth Vulnerable Web App

## Running the Application
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
If you'd like to override defaults (e.g., the app port), you can manually create and edit a `.env` file before running `setup.sh`. The setup script will not override your existing `.env`. You can use `.env.example` as a base.

## Disclaimer
This application is intended for educational and testing purposes only, and we do not take responsibility for any misuse.

## Warning
Do not expose this application to the internet, as it contains vulnerabilities that could be exploited, potentially compromising your system. If you are not fully confident in your network security settings, consider running it in a virtual machine with NAT mode to better isolate it from your host system.
