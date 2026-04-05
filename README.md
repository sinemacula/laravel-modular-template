# Laravel Modular Template

[![Build Status](https://github.com/sinemacula/laravel-modular-template/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/sinemacula/laravel-modular-template/actions/workflows/tests.yml)
[![Maintainability](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template/maintainability.svg)](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template)
[![Code Coverage](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template/coverage.svg)](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template)

A GitHub template for building **stateless API** applications with Laravel 13 using a modular architecture. Powered by
[`sinemacula/laravel-modules`](https://github.com/sinemacula/laravel-modules), the standard `app/` directory is replaced
by a `modules/` directory where each subdirectory is a self-contained module — all wired into Laravel's native service
discovery with zero boilerplate.

> **Warning**
> This template is designed exclusively for **API-first development**. All frontend scaffolding, Blade views, sessions,
> web middleware, and the root `resources/` directory have been removed. Routes are treated as API routes with no prefix.
> If you need a full-stack web application, this is not the right starting point.

## What's Included

This template comes pre-configured with:

- **Modular architecture** via [`sinemacula/laravel-modules`](https://github.com/sinemacula/laravel-modules) — module
  auto-discovery, caching, and artisan commands
- **Foundation module** — application service provider and parallel testing support
- **User module** — a complete example demonstrating controllers, form requests, API resources, events, listeners,
  observers, and policies
- **100% test coverage** — unit and feature tests for all application code
- **Static analysis** — PHPStan level 8 via qlty with
  [`sinemacula/coding-standards`](https://github.com/sinemacula/coding-standards)

## Project Structure

```text
modules/
├── Foundation/              # Core framework module
│   └── Providers/           # Service providers
└── User/                    # Example domain module
    ├── Events/              # Domain events
    ├── Http/
    │   ├── Controllers/     # API controllers
    │   ├── Requests/        # Form request validation
    │   ├── Resources/       # API resources
    │   └── routes.php       # Module routes
    ├── Listeners/           # Event listeners
    ├── Models/              # Eloquent models
    ├── Observers/           # Model observers
    └── Policies/            # Authorization policies
```

Modules are auto-discovered at boot time and cached for performance. All standard Laravel conventions work inside each
module — there is no new API to learn. See the
[`sinemacula/laravel-modules` documentation](https://github.com/sinemacula/laravel-modules) for full details on what
gets discovered and how module caching works.

## Getting Started

Click **Use this template** on GitHub, then:

```bash
composer setup
```

This installs dependencies, generates an app key, and runs migrations.

### Creating a Module

Use the artisan command provided by `sinemacula/laravel-modules`:

```bash
php artisan module:make Billing
```

This scaffolds the standard directory structure under `modules/Billing/`. The namespace follows PSR-4:
`App\Billing\Models\Invoice`. No registration is required — the module is discovered automatically.

### Artisan Commands

| Command              | Description                                                 |
|----------------------|-------------------------------------------------------------|
| `module:make {name}` | Scaffold a new module with the standard directory structure |
| `module:list`        | List all discovered modules and their paths                 |
| `module:cache`       | Cache discovered module paths for faster resolution         |
| `module:clear`       | Clear the cached module paths                               |

Module caching is integrated into Laravel's `optimize` / `optimize:clear` lifecycle.

## Development

```bash
composer dev             # Server, queue worker, and log viewer
composer test            # Run tests
composer check           # Static analysis and code quality (qlty)
composer format          # Auto-format code
```

Parallel testing is supported out of the box via ParaTest. Each parallel process gets its own database, seeded
automatically via `AppServiceProvider`.

## Requirements

- PHP ^8.3
- Laravel ^13.0

## Contributing

Contributions are welcome via GitHub pull requests.

## Security

If you discover a security issue, please contact Sine Macula directly rather than opening a public issue.

## License

Licensed under the [Apache License, Version 2.0](https://www.apache.org/licenses/LICENSE-2.0).
