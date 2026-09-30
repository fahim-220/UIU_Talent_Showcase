# UIU Talent Showcase - Project Plan

Course project for Web Programming.
Allowed stack: **HTML, CSS, JavaScript, PHP, MySQL on XAMPP**. Nothing else.

---

## 0. Rules for the AI agent (read first)

1. Work on **one phase at a time**. Finish it, run its checklist, then stop and report.
2. **Do not add new features.** Only build what is listed in this plan.
3. Keep the current design (dark theme, colors, fonts, layout). Do not redesign.
4. Use only HTML, CSS, vanilla JavaScript, PHP, and MySQL. No frameworks, no Node, no Composer packages, no CDN libraries except the ones already used (Google Fonts Poppins, FontAwesome 6.5.0).
5. All database access uses **PDO with prepared statements**.
6. All user output uses `htmlspecialchars()`.
7. Passwords use `password_hash()` and `password_verify()`.
8. Never hardcode passwords in JavaScript or HTML.
9. Keep code simple and commented. This is a student project.
10. After each phase, list the files created, changed, and deleted.

---

## 1. Scope

### Existing features (keep, make them real)
- Pages: Home, Achievers, Video, Audio, Blog, Competitions, How It Works
- Register, login, logout
- Upload entry (video, audio, text/blog)
- Likes and comments on posts
- Competitions list with category filter and registration
- Leaderboard and achievers ranked by points
- Mobile menu, toasts, modals (already built)

### New (only these two)
- **User dashboard**
- **Admin dashboard** (replaces the old hardcoded admin modal)

### Out of scope
Payments, real monetization, email sending, social login, chat, notifications, any extra feature not listed above. The "Supporters", "Organizations", and "Testimonials" sections on the home page stay **static content**.

---

## 2. Target repo structure

```
UIU_Talent_Showcase/
├── index.php
├── achievers.php
├── audio.php
├── video.php
├── blog.php
├── competitions.php
├── how-it-works.php
├── login.php
├── register.php
├── logout.php
├── dashboard.php              # user dashboard
│
├── admin/
│   ├── index.php              # admin dashboard overview
│   ├── users.php
│   ├── posts.php
│   ├── competitions.php
│   └── points.php
│
├── includes/
│   ├── config.php             # constants (DB name, paths, upload limits)
│   ├── db.php                 # PDO connection
│   ├── auth.php               # session helpers, require_login(), require_admin()
│   ├── functions.php          # small helpers (escape, flash messages, file validation)
│   ├── header.php             # shared navbar
│   └── footer.php             # shared footer
│
├── api/                       # small endpoints called with fetch()
│   ├── like.php
│   ├── comment.php
│   ├── upload.php
│   ├── join_competition.php
│   ├── delete_post.php
│   └── admin_points.php
│
├── assets/
│   ├── css/
│   │   ├── style.css          # global theme (variables, navbar, footer, buttons)
│   │   ├── dashboard.css      # user + admin dashboard styles
│   │   └── pages/
│   │       ├── achievers.css
│   │       ├── audio.css
│   │       ├── video.css
│   │       ├── blog.css
│   │       ├── competitions.css
│   │       ├── how-it-works.css
      └── auth.css
│   ├── js/
│   │   ├── main.js            # menu, modals, toast, like, comment, filters
│   │   ├── dashboard.js
│   │   └── admin.js
│   └── images/
│
├── uploads/
│   ├── video/
│   ├── audio/
│   └── images/
│
├── database/
│   ├── schema.sql
│   └── seed.sql
│
├── plan.md
└── README.md
```

Notes:
- Old `script.js` becomes `assets/js/main.js`. Old `style.css` and page CSS files move into `assets/css/`.
- Remove the `@import url('style.css')` line from every page CSS file.
- Remove the dead `showTab()` function and the hidden Video/Audio/Blog tab sections from `index.html`.
- Add a `.htaccess` file in `uploads/` that blocks PHP execution.

---

## 3. Database design

Database name: `uiu_talent_showcase` (utf8mb4).

| Table | Columns |
|---|---|
| `users` | id, name, email (unique), password_hash, role (`user`/`admin`), department, bio, avatar, status (`active`/`blocked`), created_at |
| `posts` | id, user_id, type (`video`/`audio`/`text`), title, description, file_path, cover_image, created_at |
| `likes` | id, post_id, user_id, created_at. Unique (post_id, user_id) |
| `comments` | id, post_id, user_id, body, created_at |
| `competitions` | id, title, category (`video`/`audio`/`text`), description, deadline, status (`open`/`closed`), created_at |
| `competition_entries` | id, competition_id, user_id, post_id (nullable), created_at. Unique (competition_id, user_id) |
| `points` | id, user_id, competition_id (nullable), points, note, created_at |

Rules:
- Leaderboard = `SUM(points)` per user, ordered descending.
- Achievers page = top users from the same query.
- Foreign keys with `ON DELETE CASCADE` where it makes sense.
- `seed.sql` creates 1 admin account, about 5 sample users, about 6 posts, 4 competitions, and sample points. Admin default password must be documented in `README.md` only.

---

## 4. Phases

### Phase 1 - Setup and cleanup
**Status:** Done

**Tasks**
- Move the project into `C:\xampp\htdocs\UIU_Talent_Showcase`.
- Create the folder structure from section 2.
- Rename all `.html` pages to `.php`.
- Create `includes/header.php` and `includes/footer.php`. Replace the duplicated navbar and footer in every page with `include`.
- Make the navbar consistent on every page (same links everywhere).
- Move CSS and JS into `assets/`. Update all links.
- Remove duplicate `@import` lines, dead tab code, and the hardcoded admin modal/password.
- Fix dead links (footer social `#`, "Text" dropdown, "Learn" and "Shop talent" chips, "Register Now" buttons).
- Add `alt` text to images and `aria-label` to icon-only buttons.

**Done when**
- Every page opens at `http://localhost/UIU_Talent_Showcase/` with no console errors.
- Changing a nav link in `header.php` changes it on all pages.
- Look and layout match the original.

### Phase 2 - Database and authentication
**Status:** Done
**Tasks**
- Write `database/schema.sql` and `database/seed.sql`.
- Write `includes/config.php`, `db.php`, `auth.php`, `functions.php`.
- Build `register.php`, `login.php`, `logout.php` using sessions.
- Regenerate the session ID on login. Validate email format and password length on the server.
- Navbar shows Login/Register for guests, and Dashboard/Logout for logged-in users. Admins also see an Admin link.
- Add `require_login()` and `require_admin()` guards.

**Done when**
- A new user can register, log in, and log out.
- Wrong password shows an error. Blocked users cannot log in.
- Visiting `admin/` as a normal user redirects away.

### Phase 3 - Posts (upload and display)
**Status:** Done
**Tasks**
- Wire the upload modal to `api/upload.php`.
- Validate file type (by extension and MIME), size, and title. Save with a random filename into `uploads/video|audio|images/`.
- Insert a row in `posts`.
- `video.php`, `audio.php`, `blog.php` load posts from MySQL and render the existing card design with real `src` paths.
- Show a friendly message when there are no posts.
- Set in `php.ini`: `upload_max_filesize = 100M`, `post_max_size = 110M`, `max_execution_time = 120`. Document this in `README.md`.

**Done when**
- A logged-in user uploads a video, audio file, and blog entry, and each appears on the right page and plays.
- Guests see the upload button prompt them to log in.
- A `.php` file renamed to `.mp4` is rejected.

### Phase 4 - Likes and comments
**Tasks**
- `api/like.php`: toggle like for the logged-in user, return the new count as JSON.
- `api/comment.php`: add a comment, return the rendered comment data as JSON.
- Update `main.js` to call these with `fetch()`. Keep the floating heart animation.
- Show the saved like state and comments when the page loads.
- Guests are asked to log in.

**Done when**
- Likes and comments survive a page refresh.
- One user cannot like the same post twice.
- Comment text is escaped (no HTML injection).

### Phase 5 - Competitions
**Tasks**
- `competitions.php` loads competitions from MySQL.
- Category filter (All, Video, Audio, Text) works in JavaScript.
- "Register Now" calls `api/join_competition.php`. One registration per user per competition. Closed or expired competitions cannot be joined.
- Let the user attach one of their own posts to the entry (optional).

**Done when**
- Filters show and hide the correct cards.
- Joining twice shows a clear message.
- Registration appears in the user dashboard (Phase 6).

### Phase 6 - User dashboard (`dashboard.php`)
Login required. Uses `dashboard.css` and `dashboard.js`.

**Sections**
1. **Overview:** total posts, total likes received, total points, current rank.
2. **My Posts:** table or card list with type, title, date, likes, comments, and a Delete button (`api/delete_post.php`, owner only).
3. **My Competitions:** competitions the user joined, with deadline and status.
4. **My Points:** list of points received with date and note.
5. **Profile:** edit name, department, bio, and avatar. Change password (current password required).

**Done when**
- Every section shows only the logged-in user's own data.
- A user cannot delete another user's post (test by changing the ID).
- Profile and password changes work.

### Phase 7 - Admin dashboard (`admin/`)
Admin role required on every page. Shared admin sidebar. Uses `dashboard.css` and `admin.js`.

**Pages**
1. **`admin/index.php` (Overview):** counts of users, posts, competitions, and total points. Latest 5 users and latest 5 posts.
2. **`admin/users.php`:** list all users, search by name/email, block/unblock, delete. Admin cannot block or delete their own account.
3. **`admin/posts.php`:** list all posts with filter by type, delete any post (also removes the file from `uploads/`).
4. **`admin/competitions.php`:** create, edit, delete, open/close competitions. View entries per competition.
5. **`admin/points.php`:** give points to a user for a competition with a note. Replaces the old admin modal. Shows the live leaderboard.

**Done when**
- Only admins can open these pages.
- Points added here immediately change the leaderboard on the home page and Achievers page, in the correct order.
- Deleting a post removes both the database row and the file.

### Phase 8 - Final polish and delivery
**Tasks**
- Connect the home page leaderboard and stats to real database values.
- Connect Achievers page to the top users query.
- Test on mobile width (375px) and desktop.
- Check every form for validation and error messages.
- Remove unused files and console logs.
- Write `README.md`: project description, XAMPP setup steps, how to import `schema.sql` and `seed.sql`, default admin login, folder structure, and feature list.
- Export a final database dump to `database/uiu_talent_showcase.sql`.

**Done when**
- A fresh clone can run by following the README only.
- The full test checklist below passes.

---

## 5. Test checklist

**Auth**
- [ ] Register, login, logout
- [ ] Wrong password, duplicate email, blocked user
- [ ] Guest cannot open `dashboard.php` or `admin/`
- [ ] Normal user cannot open `admin/`

**Posts**
- [ ] Upload video, audio, text
- [ ] Bad file type and oversized file are rejected
- [ ] Posts show on the correct pages

**Interactions**
- [ ] Like and unlike, count persists
- [ ] Comment persists and is escaped

**Competitions**
- [ ] Filters work
- [ ] Join once only
- [ ] Closed competition cannot be joined

**Dashboards**
- [ ] User dashboard shows only own data
- [ ] Owner-only delete works
- [ ] Admin can manage users, posts, competitions, points
- [ ] Leaderboard reorders after points change

**General**
- [ ] No PHP warnings or JavaScript console errors
- [ ] Works on mobile and desktop
- [ ] SQL injection test (`' OR 1=1 --`) fails on login
- [ ] `<script>alert(1)</script>` in a comment is shown as plain text

---

## 6. Security checklist

- PDO prepared statements everywhere
- `htmlspecialchars()` on all output
- `password_hash()` / `password_verify()`
- `session_regenerate_id(true)` on login
- Role check on every admin page and API endpoint
- Ownership check before editing or deleting anything
- Upload validation: extension, MIME type, size, random filename
- No PHP execution inside `uploads/`
- CSRF token on forms and POST endpoints (simple session token)
- No credentials in JavaScript or committed to the repo

---

## 7. Agent prompt template (per phase)

```
Read plan.md fully. Execute only "Phase N - <name>".
Follow section 0 rules. Do not add features outside the plan.
When done, list files created/changed/deleted and run the
"Done when" checks for this phase. Then stop and wait.
```
