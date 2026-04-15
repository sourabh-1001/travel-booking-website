# Installation

1. Upload project files to your web root.
2. Copy `config/.env.example` to `config/.env` and configure credentials.
3. Import `database/schema.sql` then `database/sample-data.sql`.
4. Ensure PHP extensions are enabled: `pdo`, `pdo_mysql`, `json`, `mbstring`.
5. Set file permissions for uploads directory write access:
   - `uploads/reviews` (create if not present)
6. Verify endpoints:
   - `/php/api/tours.php`
   - `/php/api/reviews.php`
   - `/php/api/blogs.php`
   - `/php/api/bookings.php`
   - `/php/api/contact.php`
