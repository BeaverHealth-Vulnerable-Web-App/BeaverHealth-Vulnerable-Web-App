# Contributing
This is a capstone group project. While this repository is public, we are currently only accepting contributions from team members.

## Development Environment

### Prerequisites
- Docker Desktop
- Docker Compose

We use Laravel Sail for our development environment.

### Initial Setup
1. Clone the repository
2. Run the setup script
```bash
setup.sh --fresh
```

## Code Style
We use PHP_CodeSniffer to enforce PSR-12 coding standards. All PHP code must pass PHPCS checks before being merged.

### Installing PHP CodeSniffer
Install PHP_CodeSniffer locally on your machine using your preferred package manager. If you have `composer` installed on your machine, you can also install it with:
```bash
composer global require "squizlabs/php_codesniffer=*"
```

Two main commands are available:

### Check code style and show violations
```bash
phpcs
```

### Automatically fix code style violations where possible
```bash
phpcbf
```


## More Information
For more information, see the private wiki:
https://github.com/BeaverHealth-Vulnerable-Web-App/BeaverHealth-Wiki/wiki
