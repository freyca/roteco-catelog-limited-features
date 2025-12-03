
# Project Documentation: Roteco Catalog

## Stack & Architecture

- **Framework:** Laravel 12
- **Frontend:** Filament Admin Panel (Livewire), Tailwind CSS, Vite
- **Database:** MariaDB
- **Testing:** Pest (preferred), PHPUnit (legacy), Livewire test utilities
- **Other:** Docker, Composer, npm, PHPStan, custom DTOs/Enums

## Key Conventions

- **DTOs/Enums:** Used for roles, address types, payment methods, order status, etc. Always use enums/DTOs in factories, models, and tests for type safety.
- **Factories:** All major models have factories. Factories must create required relationships (e.g., Order always has a shipping address).
- **Translations:** Spanish (es.json) and English supported. All user-facing strings should be translatable.
- **Notifications:** Event-driven, with admin user checks. Throws if no admin exists.
- **Filament Resources:** Only list/view for orders (no edit). Address resource supports CRUD. Authorization enforced at resource/page level.

## Testing Patterns

- **Pest Usage:**
  - Do not use `let()` or `$this->user()` style helpers for persistent variables.
  - Instead, define variables in `beforeEach` and attach them to the test context using `test()->variableName = ...`.
  - Access them in tests with `test()->variableName`.
  - Example:
    ```php
    beforeEach(function () {
        test()->user = User::factory()->create();
    });
    it('does something', function () {
        $user = test()->user;
        // ...
    });
    ```
- **Livewire Testing:** Use `Livewire::test()` for Filament pages/components. Use `assertSee`, `assertDontSee`, `assertSuccessful`, etc.

## Project Structure

- `app/` — Main Laravel app code (models, DTOs, enums, listeners, notifications, Filament resources, etc.)
- `database/` — Migrations, factories, seeders
- `resources/` — Views, lang, assets
- `tests/` — Pest and PHPUnit tests (Feature/Unit, Datasets)
- `public/` — Public assets, entrypoint
- `config/` — Laravel config files

## Other Notes

- Always keep factories, migrations, and tests in sync regarding required fields and relationships.
- All new tests should use Pest syntax and context variable pattern as above.
- See this file for up-to-date conventions and patterns for this project.
