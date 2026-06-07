# AI Security Policy

Use Laravel's built-in security capabilities as the default baseline, including validation, CSRF protection, authentication guards, and authorization checks.

All data access and sensitive actions must be protected by explicit Laravel Policies or Gates.

Role separation is mandatory:

- Customer roles must remain within customer-facing panel permissions and navigation.
- Admin roles must remain within administrative panel permissions and navigation.
- End-users must never access administrative resources, routes, actions, or data.

Any feature that crosses panel boundaries must include deliberate authorization checks and least-privilege access rules.
