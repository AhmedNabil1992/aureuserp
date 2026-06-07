# AI Architecture Guidelines

This project follows standard Laravel MVC architecture and extends it with Filament Panels as the primary UI delivery layer.

Core expectations:

- Keep business logic in Laravel services, actions, models, or policies, not in views.
- Keep controllers and Livewire actions focused on orchestration and validation.
- Keep panel-specific UX and navigation inside Filament resources, pages, widgets, and related components.

Routing and entry points are managed through Filament Panel Providers (for example, AppPanelProvider and AdminPanelProvider).

When introducing new screens, prefer custom Filament Pages or panel-integrated Livewire components instead of standard Laravel web routes, unless there is a clear non-panel requirement.
