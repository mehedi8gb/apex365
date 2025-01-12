# Apex365 Backend API

This is the backend API for the Apex365 project built with Laravel 11, providing features like authentication, role-based access, and various resource management systems for handling institutions, courses, academic years, and more.

## Prerequisites

Before getting started, ensure you have the following installed on your machine:

- PHP >= 8.3
- Composer
- Laravel 11
- MySQL or MariaDB
- Node.js and NPM
- Redis (Optional for cache/session)

## Setup Instructions

### 1. Clone the Repository

Clone the project repository to your local machine.

```bash
git clone https://github.com/mehedi8gb/apex365.git
cd apex365
```

### 2. Install Dependencies

Run the following commands to install PHP and JavaScript dependencies.

#### Install PHP dependencies

```bash
composer install
```

#### Install NPM dependencies

```bash
npm install
```

### 3. Set Up Environment Variables

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Then, configure the database, mail, and JWT settings in the `.env` file:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Apex365_db
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=your_jwt_secret_key
JWT_TTL=240 # Access Token Expiry Time in minutes
```

You can generate a new `JWT_SECRET` key using:

```bash
php artisan jwt:secret
```

### 4. Generate Application Key

Run the following command to generate the application key:

```bash
php artisan key:generate
```

### 5. Database Migration and Seeding

Run migrations and seed the database with the initial data for roles, permissions, and example users:

```bash
php artisan migrate --seed
```

### 6. Serve the Application

After setting up everything, you can now serve the Laravel application.

```bash
php artisan serve
```

This will serve the API on `http://127.0.0.1:8000`.

### 7. Run Queue Worker (Optional)

If your application uses jobs for background processing, you can start the queue worker:

```bash
php artisan queue:work
```

### 8. Compiling Frontend Assets

If you have any frontend assets, you can compile them using the following command:

```bash
npm run dev
```

### 9. Testing (Optional)

Run tests using PHPUnit:

```bash
php artisan test
```
# apex365
