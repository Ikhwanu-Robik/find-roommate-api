# Find Roommate API

An API for a platform to help find roommates with whom you can share your rent with.

<!-- # Documentation -->

# Installation

### 1. Clone the Repository

```bash
git clone https://github.com/Ikhwanu-Robik/find-roommate-api.git
cd find-roommate-api
```

---

### 2. Install Dependencies

#### 🧩 PHP & Laravel dependencies

```bash
composer install
```

#### 🧩 Front-end dependencies

```bash
npm install
```

---

### 3. Configure Environment

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Then, open `.env` and set your environment variables:

```dotenv
APP_NAME="Find Roommate"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=find_roommate
DB_USERNAME=root
DB_PASSWORD=

TAGS_GENERATOR_API_URL=https://tags-generator.org/generate.php
```

---

**What is Tags Generator API❓**

it is an api with a POST endpoint that accept a data with "text" key.
In this application, the "text" key is the user's bio.
Given that input, the API will return an array of "tags" that are the topics of the "text".
See [this](https://github.com/Ikhwanu-Robik/tags-generator-api) for reference

---

Then generate the application key:

```bash
php artisan key:generate
```

Lastly, link the storage/app folder:

```bash
php artisan storage:link
```

---

### 4. Database Setup

Run migrations and seeders:

```bash
php artisan migrate
```

---

### 5. Run the Application

#### 🖥️ Start the development server

```bash
php artisan serve
```

#### 🖥️ Start the reverb server and queue worker for chat feature

```bash
php artisan reverb:start

php artisan queue:work
```
