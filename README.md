# UrbanSpark - City Innovation Platform

A web platform for citizens to submit and track innovative ideas for city improvement.

## Features

- Idea submission with impact calculator
- Real-time statistics dashboard
- Category-based filtering
- Comments and likes system
- Admin dashboard for idea management
- File attachments support
- Real-time updates using Server-Sent Events

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP (recommended for local development)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/urbanspark.git
```

2. Create a MySQL database named `urbanspark`

3. Import the database schema:
```bash
mysql -u your_username -p urbanspark < db/schema.sql
```

4. Copy `db/config.example.php` to `db/config.php` and update with your database credentials:
```php
<?php
$host = 'localhost';
$dbname = 'urbanspark';
$username = 'your_username';
$password = 'your_password';
```

5. Ensure the `uploads` directory has write permissions:
```bash
chmod 777 uploads
```

6. Configure your web server to point to the project directory

## Usage

1. Access the application through your web browser:
```
http://localhost/urbanspark
```

2. Submit new ideas through the submission form
3. View and filter ideas in the gallery
4. Check real-time statistics in the dashboard
5. Admin can manage ideas through the admin panel

## Directory Structure

```
urbanspark/
├── admin/           # Admin dashboard files
├── db/              # Database configuration and schema
├── js/             # JavaScript files
├── php/            # PHP backend files
├── uploads/        # Uploaded files directory
└── index.html      # Main entry point
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details. 