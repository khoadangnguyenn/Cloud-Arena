# Phase 1 Implementation Report (Member 1)

## 1) Scope Completed
This phase was implemented strictly within Member 1 responsibilities from the project specification:

- Public `Homepage` improvements
- Public `Contact` page + contact submit flow
- Admin `Public Info Settings` management
- Admin `Contact/Ticket` management (list/filter/status/priority/reply/delete)

No out-of-scope module ownership (other members' feature areas) was intentionally expanded.

---

## 2) Frameworks / Technologies / Patterns Applied

- **Backend language**: PHP (custom MVC architecture, no framework)
- **Database**: MySQL with PDO wrapper (`Database.php`)
- **Routing structure**: Controller-method URL mapping via custom `App.php`
- **Frontend stack**: HTML5, CSS3, JavaScript
- **UI libraries/templates**:
  - Tailwind CSS (client UI styling)
  - SrtDash/Admin template with Bootstrap classes (admin area)
  - Font Awesome icons
- **Map integration**: Google Maps embed URL (configurable via admin settings)

### Techniques used
- Prepared statements + parameter binding for DB actions
- Server-side form validation in controllers
- Basic client-side required/minlength constraints on forms
- Output escaping with `htmlspecialchars()` to reduce XSS risk
- Session-based flash message pattern (Post/Redirect/Get style for success feedback)
- Role-based admin access guards
- Filter + pagination logic for admin ticket listing
- Dynamic setting injection into client views with safe fallback values
- UI interaction enhancements (parallax + quick search navigation)

---

## 3) Detailed Work Completed

## 3.1 Route/Controller Contract Stabilization
- Started session bootstrap in `app/init.php`.
- Normalized client contact navigation route in `app/views/layouts/client/header.php` to `pages/contact`.
- Normalized admin contact menu route in `app/views/layouts/admin/header.php` to `admincontacts`.
- Added admin role guard (`requireAdmin`) in:
  - `app/controllers/Admin.php`
  - `app/controllers/AdminContacts.php`
- Added compatibility redirect `Admin::contacts()` to keep `/admin/contacts` route behavior stable.

## 3.2 Public Contact Processing (Model + Controller)
- Implemented complete contact domain logic in `app/models/Contact.php`:
  - create contact ticket (supports composite key style `user_id + contact_id`)
  - guest contact user fallback creation
  - list/count with filters and pagination inputs
  - ticket detail retrieval
  - update status, update priority, reply handling, delete ticket
- Implemented public contact POST processing in `app/controllers/Pages.php`:
  - Validate `name`, `email`, `subject`, `message`
  - Save contact via model
  - Flash success + redirect back to contact page
  - Display fallback error when save fails

## 3.3 Admin Contact/Ticket Management
- Implemented admin contact actions in `app/controllers/AdminContacts.php`:
  - `index`
  - `updateStatus`
  - `updatePriority`
  - `reply`
  - `delete`
- Built full admin contact UI in `app/views/admin/contacts/index.php`:
  - ticket filter bar (`status`, `priority`, `keyword`)
  - paginated table listing
  - thread-style detail panel (customer message + admin reply)
  - status/priority update controls
  - reply form
  - delete action with confirmation

## 3.4 Admin Public Info Settings
- Implemented settings model APIs in `app/models/Setting.php`:
  - `getPublicSettings()`
  - `updatePublicSettings()`
- Implemented settings save/load + validation in `app/controllers/Admin.php`.
- Built settings management form in `app/views/admin/settings/index.php` for:
  - logo text
  - hotline
  - contact email
  - address
  - public about snippet
  - map embed URL

## 3.5 Dynamic Public Layout Wiring
- Added centralized public setting injection in `app/core/Controller.php` for client views.
- Replaced hardcoded public info with DB-driven values + fallback defaults in:
  - `app/views/layouts/client/header.php`
  - `app/views/layouts/client/footer.php`

## 3.6 Homepage and Contact UI Enhancements
- Homepage (`app/views/client/pages/index.php`):
  - parallax visual layers in hero
  - quick resource search UI block (products/news)
  - dynamic about snippet display from settings
- Client JS (`public/js/main.js`):
  - scroll-based parallax effect
  - quick search redirect logic using `window.URLROOT`
- Contact page (`app/views/client/contact.php`):
  - completed required fields and validation messages
  - success/error notices
  - dynamic contact info section
  - Google Maps embed block via admin-configured URL

---

## 4) Security & Quality Actions Completed

- Enforced parameterized queries through the existing PDO wrapper usage.
- Escaped output in new/updated views (`htmlspecialchars`, `nl2br` with escaped content).
- Added server-side input validation in controllers for critical forms.
- Preserved clean and simple implementation style (minimal abstraction, readable flow).
- Checked diagnostics after edits (no new linter issues reported by IDE diagnostics tool).

---

## 5) Notes for Next Phase

- Implement CSRF token protection for POST forms (recommended hardening step).
- Add deeper integration test checklist for ticket flow and settings persistence.
- Optional: improve homepage search to perform true backend search (currently redirect-focused quick search UX).
