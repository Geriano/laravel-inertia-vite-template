# Laravel Inertia Vite Template

**Simple yet powerful template for Laravel Vue applications.**

Create your awesome project with awesome template, featuring lots of features and ready to use such as :

 - Full single page application with simplified UI, routing and navigation.
 - Permission will help you organize user, role more flexible with builtin settings.
 - Builtin draggable menu to set your menu easily.
 - Builtin authentication with seeder.
 - Supports multi-language web application.
 - Dark and light themes 🌓
---
## Requirements
 - PHP 8.1 or higher
 - Node JS 14 or higher

---
## Installation

  This might take awhile but I'm pretty sure this is worth it. So let's get started.

  1. Clone the repository

     ```git clone https://github.com/Geriano/laravel-inertia-vite-template.git```

  2. Install dependencies

      ```
      cd laravel-inertia-vite-template
      composer install
      npm install
      ```
  
  3. Copy .env.example to .env

      ``` cp .env.example .env ```
  
  4. Generate key application

      ``` php artisan key:generate ```
  
  5. Configure your database in .env
      ```
      DB_CONNECTION=mysql (can be changed to other database)
      DB_HOST=your_database_host
      DB_PORT=your_database_port
      DB_DATABASE=your_database_name
      DB_USERNAME=your_database_user
      DB_PASSWORD=your_database_password
      ```
  6. Run migrations and initial seeder

      ``` php artisan migrate:fresh --seed ```
  
  7. Run web server 
  
      ``` php artisan serve ```

  8. Run vite server 
  
      ``` npm run dev ```
    
  9. Open your browser and go to [localhost](http://localhost:8000) and now you ready to build your apps!
  
## Permission
![permission-index](https://user-images.githubusercontent.com/59258929/195477625-455c16de-7fd2-40d8-954a-222b7d8d8bb1.png)
![permission-create](https://user-images.githubusercontent.com/59258929/195477640-ba4259c6-d59a-43c8-abb6-8bc4513da753.png)
![permission-update](https://user-images.githubusercontent.com/59258929/195477649-dac35e42-e7ad-49a8-b2e8-e5aeee23c322.png)

## Role
![role-index](https://user-images.githubusercontent.com/59258929/195477702-3e67dde0-3518-4ca0-a76d-fecf6f976c63.png)
![role-create](https://user-images.githubusercontent.com/59258929/195477686-fe3787b9-086a-4557-bdc1-8b94dc6f591c.png)
![role-update](https://user-images.githubusercontent.com/59258929/195477705-c15c5b22-c4ce-4a16-a89b-a0046b25f052.png)

## User
![user-index](https://user-images.githubusercontent.com/59258929/195477741-68baf73e-572a-44a6-8d61-8f8c272a4dfe.png)
![user-create](https://user-images.githubusercontent.com/59258929/195477735-9add4f2c-10d7-4651-bf98-29fa31a8fadb.png)
![user-update](https://user-images.githubusercontent.com/59258929/195477745-2ffb3f4a-ed40-4df0-89d9-3d75f50839b9.png)

## Menu builder
![menu-builder](https://user-images.githubusercontent.com/59258929/195477770-2e5f7591-2e3c-486c-b8d3-8d1fde75e115.png)
![menu-create](https://user-images.githubusercontent.com/59258929/195477773-024f8400-8f64-468f-b293-aca4c6eabf4b.png)
![menu-update](https://user-images.githubusercontent.com/59258929/195477776-e7270888-3e74-47e2-9a63-7c4d670a67d3.png)
![icon-picker](https://user-images.githubusercontent.com/59258929/195477764-48fdc7b9-ac34-4e00-b3e1-07d70a99a6c5.png)

## Login activity
![login-activity](https://user-images.githubusercontent.com/59258929/195477886-c80ca296-85c8-4425-befb-42411f85ec11.png)

## Translation
![translation-index](https://user-images.githubusercontent.com/59258929/195477960-4b329b2c-6ab0-4b87-802c-f38934535c75.png)
