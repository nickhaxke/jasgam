# Jusgam

A PHP MVC web app for selling game setups online.

## Overview
Jusgam is a custom PHP MVC application for listing games/accessories, handling orders, and managing payments. The UI and routes are organized under a lightweight router with views, controllers, and models.

## Stack
- PHP (custom MVC)
- MySQL (configured in config/app.php)
- HTML/CSS/JS (views)

## Folder Structure
- app/
  - controllers/       Request handlers
  - models/            Data access models
  - services/          Domain services
- core/
  - Router.php         Routing system
  - Controller.php     Base controller
  - View.php           View rendering
  - Database.php       DB connection
  - Security/          CSRF and security helpers
- routes/
  - web.php            All routes
- views/
  - home/              Landing pages
  - product/           Product listings and detail
  - cart/              Cart flow
  - order/             Checkout and orders
  - payment/           Payment status screens
  - auth/              Login/register
  - layouts/           Shared layouts
  - admin/             Admin dashboard and tools
- assets/              CSS/JS/images
- uploads/             Uploaded files (images, payments)
- storage/             Logs and runtime data
- vendor/              Composer dependencies (if used)

## Key Routes
- /                  Home
- /products          Product listing
- /games             Games listing
- /product/{id}      Product detail
- /cart              Cart
- /checkout          Checkout (auth)
- /dashboard         User dashboard (auth)
- /admin             Admin dashboard (auth + role:admin)

## Configuration
- config/app.php
  - base_path
  - database credentials
  - csrf token key

## Local Setup
1. Place project in your web root (e.g. c:\wamp64\www\hasheem)
2. Update config/app.php database credentials
3. Ensure the database exists
4. Open: http://localhost/hasheem/

## Notes
- Brand name has been updated to "Jusgam" in main user-facing views.
- Some legacy files may still contain the old brand name and can be updated next.
# jasgam
# jasgam
