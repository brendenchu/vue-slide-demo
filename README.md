# Vue Slide Form Demo

A Laravel 12 application with Vue 3 + TypeScript frontend, demonstrating a multi-step slide form system with role-based access control.

## Overview

This application showcases a production-ready implementation of a multi-step form system where users (clients, consultants, admins) interact with "Story" projects through an intuitive slide-based interface. The project emphasizes clean architecture, domain-driven design, and modern web development practices.

### Key Features

- **Multi-Step Slide Forms** - Intuitive step-by-step form navigation with animations
- **Role-Based Access Control** - Client, Guest, Consultant, Admin, and Super Admin roles
- **Domain-Driven Architecture** - Code organized by business domain for maintainability
- **Incremental Form Saving** - Progress automatically saved and resumable
- **Modern Tech Stack** - Laravel 12, Vue 3 Composition API, TypeScript, Inertia.js v2

## Tech Stack

### Backend

- **Laravel 12** - PHP 8.4+
- **Laravel Sanctum** - API authentication
- **Spatie Laravel Permission** - Role/permission management
- **Pest v3** - Testing framework

### Frontend

- **Vue 3** - Composition API with TypeScript
- **Inertia.js v2** - SPA routing without building an API
- **Tailwind CSS v3** - Utility-first CSS
- **DaisyUI** - Component library
- **Vite** - Frontend build tool

## Architecture

This application follows **Domain-Driven Design (DDD)** principles with code organized by business domain:

- **Account** - User profiles, teams, subscriptions, terms
- **Story** - Project/story workflow, forms, responses, tokens
- **Admin** - Administrative functions, user management
- **Auth** - Authentication and authorization
- **API** - External API endpoints

For comprehensive architecture documentation, see [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md).

## Development Setup

### Prerequisites

- PHP 8.4+
- Composer
- Node.js 18+
- MySQL/PostgreSQL

### Installation

1. **Clone and install dependencies:**

   ```bash
   composer install
   npm install
   ```

2. **Configure environment:**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Run migrations and seed:**

   ```bash
   php artisan migrate --seed
   ```

4. **Build frontend assets:**
   ```bash
   npm run build
   # OR for development with hot reload:
   npm run dev
   ```

### Development Commands

#### Backend

```bash
php artisan test                    # Run all tests
php artisan test --filter=testName # Run specific tests
vendor/bin/pint                     # Format PHP code
vendor/bin/pint --dirty             # Format only changed files
```

#### Frontend

```bash
npm run dev         # Start Vite dev server with hot reload
npm run build       # Build for production
npm run type-check  # TypeScript type checking
npm run lint        # Lint JavaScript/TypeScript/Vue
npm run format      # Format with Prettier
```

### Demo Accounts

The seeder creates demo accounts with the following credentials:

| Role        | Email                   | Password    |
| ----------- | ----------------------- | ----------- |
| Guest       | guest@example.com       | guest       |
| Client      | client@example.com      | client      |
| Consultant  | consultant@example.com  | consultant  |
| Admin       | admin@example.com       | admin       |
| Super Admin | super-admin@example.com | super-admin |

**Guest Login Alias:** You can login with just `guest` instead of the full email address (case-insensitive).

## Project Structure

```
app/
├── Enums/              Domain-specific enums
├── Http/
│   ├── Controllers/    Domain-organized controllers
│   ├── Requests/       Form validation classes
│   └── Resources/      API/frontend data transformers
├── Models/             Domain-organized Eloquent models
└── Services/           Business logic layer

resources/
├── js/
│   ├── Components/     Reusable Vue components
│   │   └── Slide/      Multi-step slide form system
│   ├── Layouts/        Layout components
│   └── Pages/          Inertia.js page components
└── views/              Blade templates

routes/
├── web.php             Main router
└── modules/            Route modules by domain
```

## Documentation

- **[ARCHITECTURE.md](docs/ARCHITECTURE.md)** - Comprehensive architecture documentation
- **[CLAUDE.md](CLAUDE.md)** - Development guidelines for Claude Code

## Development Guidelines

### Adding New Features

When adding new features, follow the domain-driven organization:

1. **Identify the domain** - Account, Story, Admin, Auth, or API?
2. **Follow existing patterns** - Check sibling files for conventions
3. **Use the service layer** - Keep controllers thin, business logic in services
4. **Write tests** - Feature tests for workflows, unit tests for services
5. **Update documentation** - Keep CLAUDE.md and ARCHITECTURE.md current

### Code Organization Rules

- **Core entities** (User, Role, Permission) → Root level
- **Domain entities** → Domain folders (Account/, Story/, etc.)
- **Controllers** → Thin, delegate to services
- **Services** → Business logic, multi-model coordination
- **Form Requests** → Validation rules and authorization
- **Resources** → Data transformation for frontend/API

## Testing

Tests use **Pest v3** with Laravel plugin:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/Auth/LoginRequestTest.php

# Run with filter
php artisan test --filter=guest_login
```

**Test Organization:**

- `tests/Feature/` - Integration tests organized by domain
- `tests/Unit/` - Unit tests for models, services, enums

## Contributing

1. Follow the domain-driven architecture patterns
2. Write tests for all new features
3. Use Form Request classes for validation
4. Keep controllers thin - use services for business logic
5. Run `vendor/bin/pint --dirty` before committing
6. Check existing files for naming/structure conventions

## License

This is a demonstration project for educational purposes.

---

**Built with ❤️ using Laravel 12, Vue 3, and Inertia.js**
