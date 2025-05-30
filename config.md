# Configuration Guide

This document provides information about configuring the BeaverHealth Vulnerable Web App.

## Environment Overview

The application supports two environments:

- **`local`** - Default environment for general users and development
- **`demo`** - Special environment used only for brief public demonstrations by the development team

### Local Environment (`APP_ENV=local`)

- Default configuration for all users
- Seeds a user with username `dev`
- Rate limiting disabled by default
- HTTP connections allowed
- User registration enabled
- Standard proxy handling

### Demo Environment (`APP_ENV=demo`)

- Used exclusively for public demonstrations
- Seeds demo users for each original development team member
- Forces HTTPS connections
- Disables user registration (routes and UI)
- Enables rate limiting
- Trusts proxies (for load balancer compatibility)

**Note:** Unless you're part of the original development team deploying a public demo, you almost certainly want to use `local`.

## Configuration Categories

### Application Settings

#### Core Application

- **`APP_NAME`** (default: `BeaverHealth`) - Application name used throughout the UI
- **`APP_ENV`** (default: `local`) - Environment type (`local` or `demo`)
- **`APP_DEBUG`** (default: `true`) - Shows detailed error messages with stack traces
- **`APP_TIMEZONE`** (default: `UTC`) - Application timezone
- **`APP_PORT`** (default: `9991`) - Port to access the application

#### Localization

- **`APP_LOCALE`** (default: `en`) - Primary application language
- **`APP_FALLBACK_LOCALE`** (default: `en`) - Fallback language
- **`APP_FAKER_LOCALE`** (default: `en_US`) - Locale for generating fake data

### Database Configuration

#### Connection Settings

- **`DB_CONNECTION`** (default: `mysql`) - Database driver
- **`DB_HOST`** (default: `db`) - Database host (Docker service name)
- **`DB_PORT`** (default: `3306`) - Database port
- **`DB_DATABASE`** (default: `vuln_db`) - Database name
- **`DB_USERNAME`** (default: `dev`) - Database username
- **`DB_PASSWORD`** (default: `password`) - Database password

#### MySQL Container Settings

These control the MySQL Docker container:

- **`MYSQL_DATABASE`** - Should match `DB_DATABASE`
- **`MYSQL_USER`** - Should match `DB_USERNAME`
- **`MYSQL_PASSWORD`** - Should match `DB_PASSWORD`
- **`MYSQL_ROOT_PASSWORD`** - Root password for MySQL

### Authentication & Security

#### Rate Limiting - Login Page Access

Controls how frequently users can access the login page itself:

- **`LOGIN_PAGE_ACCESS_RATE_ENABLE`** (default: `false` locally, `true` in demo) - Enable access rate limiting
- **`LOGIN_PAGE_ACCESS_RATE_MAX`** (default: `15`) - Maximum accesses before lockout
- **`LOGIN_PAGE_ACCESS_RATE_DECAY`** (default: `60`) - Seconds before recorded accesses expire

#### Rate Limiting - Login Attempts  

Controls actual authentication attempt frequency:

- **`LOGIN_ATTEMPTS_RATE_ENABLE`** (default: `false` locally, `true` in demo) - Enable login attempt rate limiting
- **`LOGIN_ATTEMPTS_RATE_MAX`** (default: `5`) - Maximum failed attempts before lockout
- **`LOGIN_ATTEMPTS_RATE_DECAY`** (default: `60`) - Seconds before failed attempts expire

**Important:** Both rate limiting features are disabled by default in the local environment.

### Session Management

- **`SESSION_DRIVER`** (default: `file`) - How sessions are stored
- **`SESSION_LIFETIME`** (default: `120`) - Session timeout in minutes
- **`SESSION_ENCRYPT`** (default: `false`) - Encrypt session data on disk
- **`SESSION_SECURE_COOKIE`** - Forces HTTPS for session cookies

### Caching

- **`CACHE_STORE`** (default: `database`) - Cache storage method
- **`DB_CACHE_TABLE`** (default: `cache`) - Database table for cache entries
- **`DB_CACHE_LOCK_TABLE`** (default: `cache_locks`) - Database table for cache locks

### File Storage

- **`FILESYSTEM_DISK`** (default: `local`) - Default storage disk
- **`UPLOAD_LIMIT`** (default: `100M`) - Maximum file upload size

### Logging

- **`LOG_CHANNEL`** (default: `single`) - Primary log destination
- **`LOG_LEVEL`** (default: `debug`) - Minimum log level to record
- **`LOG_DEPRECATIONS_CHANNEL`** (default: `deprecation`) - Where to log PHP deprecation warnings

### Development & Testing

#### Database Seeding

- **`NUM_FAKE_USERS`** (default: `10`) - Number of fake users generated during seeding
- **`DEV_USER_PASSWORD`** (default: `password`) - Password for the `dev` user

## Configuration Files

The `config/` directory contains Laravel configuration files that define how environment variables are used:

- **`app.php`** - Core application settings, maintenance mode, fake user count
- **`auth.php`** - Authentication guards, user providers, rate limiting configuration
- **`cache.php`** - Cache stores and prefixes
- **`database.php`** - Database connections and migration settings
- **`filesystem.php`** - File storage disks including patient records storage
- **`logging.php`** - Log channels including user activity logging
- **`queue.php`** - Queue configuration
- **`session.php`** - Session storage, cookies, and security settings
