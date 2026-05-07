# Phase 2 Implementation Report (UI Revamp + Admin Workflow Enhancements)

## 1) Scope Completed
This phase focuses on requested Admin Panel UI/UX improvements and behavior upgrades, while keeping implementation aligned with `Spec_&_Reqs.md` (Admin Dashboard, Ticketing, User Management, Public Settings).

Completed scope in this phase:

- Full Vietnamese synchronization for updated Admin pages/components
- Header/Footer behavior upgrades (search, profile dropdown, readability)
- Dashboard "Recent Sign-ups" data quality improvement
- Tickets page UX upgrade (in-place detail rendering, no full page redirect required)
- Users page action menu implementation (edit, ban/unban, delete member)
- Settings page brand logo upload implementation and public-site logo binding

No unrelated dump files or out-of-scope modules were added.

---

## 2) Delivered Changes by Requirement

## 2.1 Header & Footer

### a) Search bar functionality
- Implemented global admin search behavior in `public/js/main.js` (`initAdminGlobalSearch`):
  - Supports module-intent keywords (dashboard, services, tickets, news, users, settings)
  - Supports keyword-based redirection to filtered pages:
    - Users: `/admin/users?keyword=...`
    - Tickets: `/admincontacts?keyword=...`

### b) Remove small subtitles under top page title
- Removed subtitle rendering from topbar in `app/views/layouts/admin/header.php`.

### c) User button dropdown behavior
- Updated profile dropdown to exactly 2 actions:
  - `Chỉnh sửa thông tin`
  - `Đăng xuất`
- Implemented in `app/views/layouts/admin/header.php`.

### f) Header controls simplification (follow-up pass)
- Removed topbar buttons:
  - `Ngôn ngữ`
  - `Ứng dụng nhanh`
- Kept only relevant controls (theme, notifications, user menu) for cleaner UI and reduced clutter.

### g) Dropdown stability + layout-safe rendering (hotfix)
- Replaced profile dropdown dependency with custom JS toggle to avoid inconsistent bootstrap-init behavior.
- Ensured dropdown does not break page layout by forcing floating absolute positioning.
- Implemented in:
  - `public/js/main.js` (`initAdminProfileDropdown`)
  - `public/admin_assets/css/admin-modern.css` (`.admin-topbar-tools .admin-dropdown-menu`)

### d) Footer readability on dark background
- Strengthened footer text contrast with `.admin-footer-note` style in `public/admin_assets/css/admin-modern.css`.
- Updated footer text in `app/views/layouts/admin/footer.php`.

### e) Vietnamese synchronization (header/footer/nav labels)
- Converted relevant labels/menu headings/actions to Vietnamese in:
  - `app/views/layouts/admin/header.php`
  - `app/views/layouts/admin/footer.php`

---

## 2.2 Dashboard Page

### a) Recent Sign-ups real data
- Dashboard already consumed real DB data through `User::getRecentUsers`.
- Improved data quality by excluding fallback guest-contact account from recent list:
  - Updated `app/models/User.php` (`getRecentUsers`) with `WHERE username <> 'guest_contact'`.
- Extended payload to include `avatar` field for real avatar rendering in recent signups.

### b) Vietnamese text synchronization on dashboard widgets
- Localized dashboard card labels, notes, panel text, and relative-time strings in:
  - `app/views/admin/index.php`
- Revenue month labels localized in:
  - `app/controllers/Admin.php` (`Thg mm` format)

### c) Recent Sign-ups avatar + created_at synchronization
- Updated dashboard recent members UI to:
  - Use DB avatar URL when available
  - Fallback to initials if avatar not set
  - Render relative time strictly from `created_at`
- Added defensive time diff handling (`max(0, time() - timestamp)`) to avoid invalid negative values.

---

## 2.3 Tickets Page

### a) Remove "Support workflow" card text in filters
- Removed from `app/views/admin/contacts/index.php`.

### b) Render ticket details immediately without page reload
- Added AJAX detail endpoint:
  - `AdminContacts::detail($userId, $contactId)` in `app/controllers/AdminContacts.php`
- Added ticket detail partial view:
  - `app/views/admin/contacts/partials/ticket_detail.php`
- Refactored ticket page to render detail panel from partial:
  - `app/views/admin/contacts/index.php`
- Added client-side handler (`initTicketDetailSelection`) in `public/js/main.js`:
  - Click ticket row -> fetch detail HTML -> replace detail panel dynamically
  - Keeps URL updated with `history.replaceState`
  - Retains normal-link fallback behavior

### e) Repeated-click reliability hardening (follow-up + hotfix)
- Fixed issue where repeated ticket clicks could fail and show toast error by hardening both backend and frontend:
  - Backend:
    - `AdminContacts::isAjaxRequest()` accepts explicit `ajax=1` flag
    - `respondJson()` uses robust JSON encoding options and fallback payload on encode failure
  - Frontend:
    - Adds `ajax=1` and cache-busting `_ts` query
    - Uses `cache: 'no-store'`
    - Parses response via text-first resilient JSON extraction (tolerates noisy output)
    - Handles redirected/non-JSON response gracefully without breaking UI flow

### c) Remove "Workflow: Support conversation" line in detail section
- Removed in partialized detail UI (`ticket_detail.php`).

### d) Vietnamese synchronization on ticket statuses/priorities
- Added Vietnamese label maps in ticket list/detail rendering.

---

## 2.4 Users Page

### a) Implement "Edit profile" action
- Added dedicated admin edit user screen:
  - View: `app/views/admin/users/edit.php`
  - Controller: `Admin::editUser($userId)` in `app/controllers/Admin.php`
- Supports editing:
  - Full name
  - Email
  - Role
  - Status

### b) Implement "Ban profile" action
- Kept status toggle action and localized behavior/message:
  - `Admin::toggleUserStatus($userId)` in `app/controllers/Admin.php`
  - Menu label updates in `app/views/admin/users/index.php`

### c) Remove "Change password" from 3-dots menu
- Removed from `app/views/admin/users/index.php`.

### d) Add "Delete member" action
- Added delete action in menu (`Xóa thành viên`) and backend handler:
  - `Admin::deleteUser($userId)` in `app/controllers/Admin.php`
  - `User::deleteUserById($userId)` in `app/models/User.php`
- Added safeguards:
  - Prevent deleting currently logged-in admin
  - Prevent deleting admin accounts through member delete flow

### e) Supporting validation method
- Added `User::isEmailUsedByAnother($email, $excludeUserId)` to validate unique email during edit.

### f) Avatar pipeline alignment (follow-up pass)
- Finalized avatar flow exactly as requested:
  - User uploads image
  - Backend stores file in `/public/uploads/avatars/<username>.<ext>`
  - DB stores URL path (e.g. `/uploads/avatars/testuser.png`)
  - Frontend resolves DB URL for rendering
- Additional implementation details:
  - Replaces old extension variants for same username on new upload
  - Syncs avatar path to session (`$_SESSION['user_avatar']`) after login/profile refresh
  - Uses real avatar in:
    - Admin Users table
    - Admin Dashboard recent signups
    - Public site top header (logged-in user block)
    - Client profile page (compatible URL resolver)

---

## 2.5 Settings Page (Brand Logo)

### a) Implement changing brand logo
- Added secure upload flow in `Admin::settings()` via helper:
  - `uploadBrandingAsset()` in `app/controllers/Admin.php`
- Validations implemented:
  - File type: `png`, `svg`, `ico`
  - Max size: 2MB
  - Basic SVG safety checks (reject script/event/javascript payload patterns)
- Upload destination:
  - `public/uploads/branding/`
- Persisted in settings key:
  - `site_logo_image`

### b) Settings model support
- Added `site_logo_image` to:
  - `publicKeys`
  - `defaultPublicSettings`
  in `app/models/Setting.php`.

### c) Admin settings UI support
- Added current-logo preview and localized branding section text in:
  - `app/views/admin/settings/index.php`

### d) Public layout integration
- Bound uploaded logo to public header/footer display with fallback icon:
  - `app/views/layouts/client/header.php`
  - `app/views/layouts/client/footer.php`

---

## 3) Implementation Quality Notes

- Kept architecture consistent with existing custom MVC (no framework added).
- Reused existing flash/session and autosave interaction patterns.
- Preserved graceful fallback navigation for AJAX-enhanced flows.
- Kept changes focused on requested Phase 2 scope and project spec.

---

## 4) Files Added

- `app/views/admin/contacts/partials/ticket_detail.php`
- `app/views/admin/users/edit.php`

---

## 5) Files Updated

- `app/controllers/Admin.php`
- `app/controllers/AdminContacts.php`
- `app/models/User.php`
- `app/models/Setting.php`
- `app/views/layouts/admin/header.php`
- `app/views/layouts/admin/footer.php`
- `app/views/admin/index.php`
- `app/views/admin/contacts/index.php`
- `app/views/admin/users/index.php`
- `app/views/admin/settings/index.php`
- `app/views/layouts/client/header.php`
- `app/views/layouts/client/footer.php`
- `app/views/client/users/profile.php`
- `app/controllers/Users.php`
- `public/js/main.js`
- `public/admin_assets/css/admin-modern.css`

---

## 6) Current Result

Phase 2 requested collaboration tasks and follow-up hotfixes are implemented and stabilized.  
Current result includes a localized Vietnamese admin experience, stable dropdown/layout behavior, resilient ticket live-rendering across repeated clicks, and a consistent avatar pipeline from upload -> DB URL -> frontend render.
