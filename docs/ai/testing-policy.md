# AI Testing Policy

Testing should prioritize feature-level confidence for panel behavior and user workflows.

Primary approach:

- Write Feature tests for Filament Resources and panel-driven flows.
- Test Livewire components for state changes, validation, authorization, and rendered output.
- Cover happy paths, validation failures, and permission/authorization boundaries.

When adding or changing functionality, include or update tests closest to the affected Filament resource or Livewire component to protect expected behavior.
