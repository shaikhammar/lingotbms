[![LingoTBMS CI](https://github.com/shaikhammar/lingotbms/actions/workflows/ci.yml/badge.svg)](https://github.com/shaikhammar/lingotbms/actions/workflows/ci.yml)
# LingoTBMS

LingoTBMS is a modular ERP application built for language service providers. It combines Laravel, React, PostgreSQL, and Redis to deliver a scalable platform for managing projects, clients, linguists, billing, and workflows.

## Features

- Modular ERP architecture for flexible deployment
- Project and translation management
- Client and vendor management
- Job tracking and workflow automation
- Invoicing and financial reports
- Real-time notifications and caching with Redis
- API-driven frontend using React

## Built With

- Laravel - backend framework
- React - frontend interface
- PostgreSQL - primary relational database
- Redis - caching and real-time features

## Getting Started

### Requirements

- PHP
- Composer
- Node.js / NPM
- PostgreSQL
- Redis

### Installation

1. Clone the repository
   ```bash
   git clone https://github.com/shaikhammar/lingotbms.git
   cd lingotbms
   ```
2. Install backend dependencies
   ```bash
   composer install
   ```
3. Install frontend dependencies
   ```bash
   npm install
   ```
4. Copy environment file
   ```bash
   cp .env.example .env
   ```
5. Configure database and Redis settings in `.env`
6. Run migrations and seeders
   ```bash
   php artisan migrate --seed
   ```
7. Build frontend assets
   ```bash
   npm run build
   ```
8. Start the application
   ```bash
   php artisan serve
   ```

## Project Structure

- `app/` - Laravel application code
- `resources/js/` - React frontend source
- `database/` - migrations and seeds
- `routes/` - API and web routes

## CONVENTIONS

- route naming pattern for future modules (clients.index, clients.store…)
- page components live per module
- nav registry lives in nav component 