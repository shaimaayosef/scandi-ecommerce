# Scandi Ecommerce Test Task

## Introduction
- This document provides an overview of the Junior Full Stack Developer test task designed by Scandiweb.
- Key components include back-end and front-end development, specifically utilizing PHP for back-end and ReactJS for front-end.

## Technologies Used

### Back-end
- **Programming Language**: PHP 8.1+
- **Database**: MySQL (version ^5.6)
- **GraphQL**: Used for developing schemas and mutations for categories, products, and orders.
- **Composer**: Dependency management for PHP.
- **OOP Principles**: Implemented throughout the application to demonstrate features like inheritance, polymorphism, and clear delegation of responsibilities.

### Front-end
- **Framework**: ReactJS
- **Build Tool**: Vite or Create React App (CRA)
- **Styling**: CSS
- **State Management**: React's built-in state management or other libraries as needed.
- **Linting**: ESLint for maintaining code quality.
- **Package Management**: npm or yarn for managing dependencies.

## Running the Project on a Local Server

To run the project on a local server, follow these steps:

1. Open a terminal in the `database` folder and run the `create-database` and `insert_data` scripts:
    ```sh
    php create-database.php
    php insert_data.php
    ```

2. Open a terminal in the `backend` folder and run the following commands:
    ```sh
    composer install
    php -S localhost:8000
    ```

3. Open a terminal in the `frontend` folder and run the following commands:
    ```sh
    npm install
    npm run dev
    ```