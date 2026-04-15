# Database Setup

1. Create a MySQL database (UTF8MB4).
2. Import `/database/schema.sql`.
3. Import `/database/sample-data.sql`.
4. Update `/config/.env` with production DB credentials.
5. Ensure MySQL user has `SELECT, INSERT, UPDATE, DELETE` privileges.

## Core Tables
- `tours`, `reviews`, `blogs`, `bookings`, `users`, `languages`, `inquiries`

## Security
- Use strong DB passwords.
- Restrict DB host access.
- Backup daily using Hostinger scheduled backups.
