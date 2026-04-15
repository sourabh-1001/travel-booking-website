# API
- `GET /php/api/tours.php`
- `GET /php/api/tours.php?id={id}`
- `GET /php/api/tours.php?category=golden-triangle`
- `GET /php/api/tours.php?destination=Delhi`
- `GET /php/api/reviews.php`
- `POST /php/api/reviews.php`
- `GET /php/api/blogs.php`
- `POST /php/api/blogs.php` (admin session required)
- `POST /php/api/bookings.php`
- `POST /php/api/contact.php`
- `GET /php/api/settings.php`
- `POST /php/api/auth.php?action=register`
- `POST /php/api/auth.php?action=login`
- `POST /php/api/auth.php?action=logout`
- `GET /php/api/auth.php?action=profile`

## Security Notes
- POST endpoints enforce CSRF validation via token or same-origin checks.
- Review submission includes basic session-based rate limiting.
- Review photo uploads allow only JPEG/PNG/WebP images.
- Contact inquiries are persisted to the `inquiries` table.
