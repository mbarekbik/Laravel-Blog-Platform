# PHP Laravel Blog with Role Management

A straightforward blog page written in PHP/Laravel.

![main](https://github.com/Mati822456/Laravel-Blog-Website/assets/103435077/0968c8ef-77cd-4e7f-ba4b-76e1a8e4388d)

## Table of Contents

-   [General Info](#general-info)
-   [Technologies](#technologies)
-   [Setup](#setup)
-   [Features](#features)
-   [Acknowledgements](#acknowledgements)
-   [Contact](#contact)

## General Info

This project is a simple blog platform built using Laravel, MySQL, and PHP. It provides a user-friendly interface with a fully functional admin panel for managing blog posts, comments, and user roles. The platform supports two types of users: Admin and Writer. Admins have full control over the content, users, and roles, while Writers can create, edit, and manage their own posts.

The system also features email notifications, a responsive design for mobile and desktop devices, and several security measures to ensure safe and smooth operation. Users can comment on posts, and Writers can save drafts, publish articles, and edit or delete their own posts. The blog's frontend is built with Blade templating, and additional JavaScript functionality is added with SweetAlert2 for notifications and FontAwesome for icons.

![post](https://github.com/Mati822456/Laravel-Blog-Website/assets/103435077/e332038e-05bf-4818-b32d-968a45692d84)

## Technologies

This project was built using the following technologies:

Laravel 9.45.1: The PHP framework used for backend development and routing.
Blade: The templating engine for rendering views in Laravel.
PHP 8.1.7: The programming language used for building the application.
MySQL 8.0.29: The database used for storing blog posts, user information, and comments.
HTML5: The standard markup language for structuring web content.
CSS3: The styling language used to design the responsive and modern layout.
JavaScript: Used for adding interactivity and dynamic features to the frontend.
SweetAlert2: A library for custom alerts and notifications.
FontAwesome 6.5.1: Used for incorporating icons throughout the application.
NPM: For managing frontend dependencies and building assets.

## Setup

To run this project you will need to install PHP, MySQL, [Composer](https://getcomposer.org/download/), [NPM](https://www.npmjs.com/package/npm) on your local machine.

If you have everything, you can run these commands:

```
# Clone this respository
> git clone https://github.com/Mati822456/Laravel-Blog-Website.git

# Go into the folder
> cd Laravel-Blog-Website

# Install dependencies from lock file
> composer install

# Install packages from package.json
> npm install

# Compile assets
> npm run build
```

`Create or copy the .env file and configure it. e.g., db_username, db_password, db_database`
</br>
`You will need to configure SMTP in order to send emails.`

```
# Generate APP_KEY
> php artisan key:generate

# Run migrations if you have created database
> php artisan migrate

# Run seeder to create Permissions, Admin and Writer users and 10 random posts
> php artisan db:seed

# Start server
> php artisan serve

# Access using
http://localhost:8000
```

Now you can login using created accounts:

```
Role: Admin
Email: admin@db.com
Password: admin1234

Role: Writer
Email: writer@db.com
Password: writer1234
```

![dashboard](https://github.com/Mati822456/Laravel-Blog-Website/assets/103435077/ab2cbc89-b149-4770-9f90-46fa6287fd8b)
![dashboard_posts](https://github.com/Mati822456/Laravel-Blog-Website/assets/103435077/4c295832-b21c-4f64-bc7f-8da7e73ed3de)
![posts_create](https://github.com/Mati822456/Laravel-Blog-Website/assets/103435077/579c241a-48ee-48fc-8654-f366a3a5f490)

## Features

| Name                             
|----------------------------------
| Version control of each post     
| Tiles on the home page           
| Improve post creation/editing UI 
| Categories                       
| Reading time                     
| Pinned post                      
| Better tiles in history          
| Changelog for posts              
| History post comparison        
| Remove jQuery                   
| Dark mode                        
| More filtering                   
| Auto-save editing post           
| Observer on main page            
| Super-permissions                
| Image Browser                    
| Enhance image selection          
| Notifications                    


