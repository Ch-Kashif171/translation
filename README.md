# Translation Application

## Setup Guide

Follow the steps below to set up and run the translation project.

### 1. Clone the Repository

```sh
git clone https://github.com/Ch-Kashif171/translation.git
cd translation
```

### 2. Install Dependencies

```sh
composer install
```

### 3. Configure the Database

- Create a database in MySQL.
- Set the database credentials in the `.env` file.

### 4. Run Migrations

```sh
php artisan migrate
```

### 5. Seed the Database

Run the following command to seed translations via seeder files:

```sh
php artisan db:seed --class=TranslationSeeder
```

Alternatively, if you want to import data via JSON files, run:

```sh
php artisan db:seed --class=TranslationJsonSeeder
```

(Ensure JSON files are placed under `database/seeders/json`.)

---

## Translation API Endpoints

> **Note:** These APIs are protected using Sanctum token authentication. First, create a user, log in, and use the Bearer token for authorization in subsequent requests.

### Authentication APIs

1. **Register User**
    - `POST api/register`

2. **Login User**
    - `POST api/login`

### Translation APIs

3. **Search Translations**
    - `GET api/translations?tags=web&search=welcome`

4. **Store a Translation**
    - `POST api/translations`
    - **Params:**
      ```json
      {
        "locale": "en",
        "key": "platform",
        "value": "iPhone",
        "tags": {
          "web": "web",
          "mobile": "mobile",
          "desktop": "desktop"
        }
      }
      ```

5. **View a Translation**
    - `GET api/translations/{id}`

6. **Update a Translation**
    - `POST api/translations/{id}`
    - **Params:** 
    ```json
      {
        "locale": "en",
        "key": "platform.opensource",
        "value": "Andriod",
        "tags": {
          "web": "web",
          "mobile": "mobile",
          "desktop": "desktop"
        }
      }
      ```

7. **Delete a Translation**
    - `DELETE api/translations/{id}`

8. **Export Translations**
    - `GET api/translations/export`

---

### Here is the apis documentation for postman:

You can find the API documentation [here](https://documenter.getpostman.com/view/1614227/2sAYXEDHQG).

## Performance Note

All API endpoints respond in under 200ms.


## Running Unit Tests

Run the following commands to execute unit and feature tests:

1. **Test All Translation APIs**
   ```sh
   php artisan test --group=translation-api
   ```

2. **Test Relations and Translation Creation**
   ```sh
   php artisan test --group=translation
   ```

3. **Test Large Data Insertion Performance**
   ```sh
   php artisan test --group=performance
   ```

### PHPUnit Configuration

Add the following lines to `phpunit.xml` to configure the database for testing:

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="translation"/>
