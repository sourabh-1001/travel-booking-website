# API
- `GET /php/api/tours.php`
- `GET /php/api/tours.php?id={id}`
- `GET /php/api/reviews.php`
- `POST /php/api/reviews.php`
- `GET /php/api/blogs.php`
- `POST /php/api/blogs.php` (admin session required)
- `POST /php/api/bookings.php`
- `GET /php/api/settings.php`

## Security Notes
- POST endpoints enforce CSRF validation via token or same-origin checks.
- Review submission includes basic session-based rate limiting.
- Review photo uploads allow only JPEG/PNG/WebP images.
- Contact inquiries are persisted to the `inquiries` table.
