# Hostinger Deployment Guide

1. Create hosting account, domain, and SSL certificate.
2. Create a MySQL database in hPanel.
3. Import `database/schema.sql` and `database/sample-data.sql` via phpMyAdmin.
4. Upload all project files to `public_html` using FTP/File Manager.
5. Create `config/.env` with Hostinger DB and app URL values.
6. Test APIs and forms (booking, review, contact).
7. Enable HTTPS redirect in hPanel and update canonical domain metadata.
8. Configure SMTP credentials for transactional emails.
9. Set daily database backups in Hostinger backup panel.
