# UIU Talent Hunter

UIU Talent Hunter is a dedicated platform designed for the students of United International University (UIU) to discover, develop, and display their unique gifts. By bridging creators with audiences, it empowers students to showcase their work, compete in talent categories, and be recognized across the community.

## Features
- **Public Pages:** View the live talent feed across Video, Audio, and Text (Blog) categories.
- **Authentication:** Secure user registration, login, and logout.
- **Content Upload:** Seamlessly upload and share Video, Audio, or Blog entries.
- **Community Engagement:** Like and comment on peers' showcases.
- **Competitions:** Participate in active competitions with advanced filtering and a streamlined join flow.
- **Leaderboards & Achievers:** Track top-ranked students dynamically updated based on community votes and admin points.
- **User Dashboard:** Manage your profile, view your posts, and track your points.
- **Admin Dashboard:** Fully manage posts, users, competitions, and seamlessly award/deduct points directly from the Manage Posts view.

## Tech Stack
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP 8+
- **Database:** MySQL
- **Environment:** XAMPP

## Requirements
- PHP >= 8.0
- MySQL Server (via XAMPP or similar)
- Apache Web Server (via XAMPP)

## Setup Steps
1. Place the project folder inside your XAMPP htdocs directory and ensure the folder name is exactly `UIU_Talent_Showcase` (i.e. `C:\xampp\htdocs\UIU_Talent_Showcase`), as the `BASE_URL` in `includes/config.php` depends on this naming.
2. Start Apache and MySQL from your XAMPP Control Panel.
3. Open phpMyAdmin (`http://localhost/phpmyadmin`), create a database named `uiu_talent_showcase`, and import `database/schema.sql` first, followed by `database/seed.sql` to populate sample data and demo accounts.
4. Open `php.ini` via XAMPP and adjust the following limits to support video/audio uploads:
   ```ini
   upload_max_filesize = 100M
   post_max_size = 110M
   max_execution_time = 120
   ```
5. Restart the Apache server.
6. Open your browser and navigate to `http://localhost/UIU_Talent_Showcase/`.

## Demo Accounts
- **Admin:** `admin@uiu.ac.bd` / `Admin@12345`
- **Users:** Seeded sample users have passwords listed in `seed.sql` (default is `password123` or explicitly seeded hash).
*Note: Please change the demo account passwords in a production environment.*

## Configuration
- **Social URLs:** Edit the constants (`SOCIAL_FACEBOOK_URL`, `SOCIAL_INSTAGRAM_URL`, `SOCIAL_YOUTUBE_URL`) in `includes/config.php` to link your active social media accounts. Leaving them empty gracefully disables the links in the footer.

## Security Measures
- Complete prevention of SQL Injection through the rigorous use of PDO Prepared Statements.
- Universal CSRF protection enforced across all state-changing POST forms and API endpoints.
- XSS prevention via deep HTML escaping (`htmlspecialchars`) utilizing the `e()` helper function.
- `.htaccess` secured upload directories executing standard file extension checks.
- Sessions secured with `HttpOnly` and `SameSite=Lax` cookie parameters.

## Known Limitations
- Local XAMPP architecture limits real-world scalable file storage.
- No real-world payment gateway or monetization architecture.
- No real-time email verification or automated password reset flow.

## Team / Course
- **Team Name:** <Team Name>
- **Members:**
  - <Member 1 Name> (<Member 1 ID>)
  - <Member 2 Name> (<Member 2 ID>)
- **Course:** <Course Name / ID>
- **Instructor:** <Instructor Name>
