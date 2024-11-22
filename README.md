# BeaverHealth Vulnerable Web App
## Running the Application
1. Install dependencies
- [PHP](https://www.php.net/manual/en/install.php)
- [Composer](https://getcomposer.org/doc/00-intro.md)
- [Docker](https://www.docker.com/get-started/)
- [Docker Compose](https://docs.docker.com/compose/install/)
2. Clone the repo 🌀   
  `git clone git@github.com:BeaverHealth-Vulnerable-Web-App/BeaverHealth-Vulnerable-Web-App.git`
3. Navigate to the project root 🫚  
  `cd ./BeaverHealth-Vulnerable-Web-App`
4. Install PHP dependencies 📦  
  `composer install`
5. Run the application 🏃  
  `./vendor/bin/sail up -d`
6. Allow a minute or two for database to initialize.
7. Migrate the database 🧳  
  `./vendor/bin/sail artisan migrate`
8. Seed the database 🌱  
  `./vendor/bin/sail artisan db:seed`
9. In a web browser, navigate to `localhost:9991` 🗺️  
10. Log in with the following credentials ㊙️  
  - Username: `admin`
  - Password: `password`
