# How to Run the Project

## Prerequisites:
Docker Desktop: Ensure that Docker Desktop is installed and running on your system. You can download it from the official Docker website: https://www.docker.com/products/docker-desktop

## Steps:
1. Clone the repository
2. Open the terminal and navigate to the project folder
    ```bash
    cd path/to/project
    ```
3. Start Laravel Sail
    ```bash
    ./vendor/bin/sail up
    ```
    - This will build and start the necessary Docker containers for your Laravel application (web server, database, etc.).
    - For the first time, this might take a while as it downloads the images.
    - The `-d` flag (e.g.,` ./vendor/bin/sail up -d`) will run the containers in detached mode (in the background).

4. Run the migrations
    ```bash
    ./vendor/bin/sail artisan migrate
    ```
    - This will run the migrations and create the necessary tables in the database.

5. Seed the database (Optional)
    ```bash
    ./vendor/bin/sail artisan db:seed
    ```
    - This will seed the database with sample data.

6. Execute NPM commands
    ```bash
    ./vendor/bin/sail npm install
    ./vendor/bin/sail npm run dev
    ```
    - This will install the necessary npm packages and compile the assets.

7. Open the browser and navigate to `http://localhost`
8. To stop the containers, run
    ```bash
    ./vendor/bin/sail down
    ```
    - This will stop and remove the containers.
    - To stop the containers without removing them, use the `-d` flag (e.g., `./vendor/bin/sail down -d`).
    - To stop and remove the containers along with the volumes, use the `-v` flag (e.g., `./vendor/bin/sail down -v`).

9. To run the tests, use the following command
    ```bash
    ./vendor/bin/sail test
    ```
    - This will run the tests and display the results.

Note: All php artisan commands can be run using `./vendor/bin/sail artisan` in place of `php artisan`.
