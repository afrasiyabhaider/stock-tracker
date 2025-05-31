# Stock Tracker

A robust and extensible stock tracking application designed for modern development workflows.

## Getting Started

### Prerequisites

Ensure the following tools are installed on your system:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/)

### Setup Instructions

1. **Clone the Repository**

    ```bash
    git clone https://github.com/afrasiyabhaider/stock-tracker.git
    cd stock-tracker
    ```

2. **Configure Environment Variables**

    All application configuration is managed via environment variables in the `.env` file at the project root. Key variables include:

    ```env
    APP_NAME=StockTracker
    APP_ENV=local
    APP_KEY=base64:...
    APP_DEBUG=true
    APP_PORT=8090
    APP_URL=http://localhost:8090

    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=stocktracker
    DB_USERNAME=root
    DB_PASSWORD=secret
    FORWARD_DB_PORT:3319

    MAIL_MAILER=smtp
    MAIL_HOST=sandbox.smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your_mailtrap_username
    MAIL_PASSWORD=your_mailtrap_password
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS=example@example.com
    MAIL_FROM_NAME="${APP_NAME}"
    ```

    > **Tip:** Duplicate `.env.example` to `.env` and adjust values as needed:
    >
    > ```bash
    > cp .env.example .env
    > ```

3. **Build Docker Containers**

    Execute the following to build containers (first-time setup):

    ```bash
    docker-compose build --no-cache
    ```

4. **Start Docker Containers**

    ```bash
    docker-compose up -d
    ```

## Dependency Installation

- **PHP Dependencies:**  
  Install Composer dependencies within the container:

    ```bash
    docker-compose exec laravel.test composer install
    ```

- **Node.js Dependencies:**  
  Install Node.js packages:

    ```bash
    docker-compose exec laravel.test npm install
    ```

## Application Management with Laravel Sail

- **Execute Artisan Commands:**

    ```bash
    ./vendor/bin/sail artisan migrate --seed
    ```
- **Run NPM Scripts:**

    ```bash
    ./vendor/bin/sail npm run dev
    ```


### Sail Alias for Convenience

To streamline Sail usage, add the following alias to your shell profile (`~/.bashrc`, `~/.zshrc`, etc.):

```bash
alias sail='sh $([ -f sail ] && echo sail || echo ./vendor/bin/sail)'
```

Reload your shell or source your profile:

```bash
source ~/.zshrc
# or
source ~/.bashrc
```

You can now use `sail` as a shorthand:

```bash
sail up -d
sail npm install
sail npm run dev
sail artisan migrate
```

Refer to the [Laravel Sail documentation](https://laravel.com/docs/sail) for advanced usage.

## Mail Configuration (Mailtrap)

For safe email testing during development, integrate [Mailtrap](https://mailtrap.io/):

1. Register at [Mailtrap](https://mailtrap.io/).
2. Create an inbox and obtain SMTP credentials.
3. Update your `.env` file with the provided credentials:

    ```
    MAIL_MAILER=smtp
    MAIL_HOST=sandbox.smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your_mailtrap_username
    MAIL_PASSWORD=your_mailtrap_password
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS=example@example.com
    MAIL_FROM_NAME="${APP_NAME}"
    ```

All outgoing emails will be routed to Mailtrap for secure testing.

## Running Unit and Feature Tests

Execute the test suite using:

```bash
sail artisan test
```

This command runs all PHPUnit tests and outputs results in your terminal.

## Accessing the Application

Once running, access the application at [http://localhost:8090/](http://localhost:8090/).  
API endpoints are available at [http://localhost:8090/api](http://localhost:8090/api).

## Default Test User

After starting the application, you can log in using the following test credentials:

- **Email:** `test@example.com`
- **Password:** `password`

These credentials are seeded by default and can be used to access the application and test API endpoints.

## API Documentation (Swagger)

Interactive API documentation is accessible via Swagger UI:

[http://localhost:8090/api/documentation](http://localhost:8090/api/documentation)

### Authenticating via Swagger

1. Obtain your API token (e.g., via login or registration).
2. In Swagger UI, click **Authorize**.
3. Enter your token and confirm.
4. Authenticated requests can now be made directly from the UI.

---
