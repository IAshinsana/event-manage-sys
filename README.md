# 🎉 Event Management System (EMS v2)

A comprehensive web-based Event Management and Ticketing System built with PHP and MySQL. This system enables organizations to create, manage, and sell tickets for events while providing a seamless booking experience for users.

## 📋 Table of Contents
- [Features](#-features)
- [Screenshots](#-screenshots)
- [Technologies](#-technologies)
- [System Requirements](#-system-requirements)
- [Installation](#-installation)
- [Database Setup](#-database-setup)
- [User Roles](#-user-roles)
- [Demo Accounts](#-demo-accounts)
- [Project Structure](#-project-structure)
- [API Endpoints](#-api-endpoints)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

### 🎫 Event Management
- **Event Creation & Publishing**: Create detailed events with images, descriptions, venue information
- **Event Approval System**: Admin moderation for event publishing
- **Multiple Ticket Types**: Support for different ticket categories with individual pricing
- **Event Categories**: Organize events by categories (Music, Conference, Workshop, Sports)
- **Event Archiving**: Archive past events for record keeping
- **Venue Management**: Detailed venue information and capacity management

### 🎟️ Ticketing System
- **Dynamic Pricing**: Flexible pricing for different ticket types
- **Inventory Management**: Real-time ticket availability tracking
- **Unique Ticket Codes**: Auto-generated unique codes for each ticket
- **Group Bookings**: Support for multiple ticket purchases
- **Printable Tickets**: Generate PDF tickets with QR codes
- **Ticket Validation**: Check-in system with ticket code verification

### 👥 User Management
- **Role-Based Access Control**: Admin, Coordinator, Checker, and User roles
- **User Registration & Authentication**: Secure login system
- **Coordinator Applications**: Application system for event organizers
- **Profile Management**: User profile updates and management
- **Dashboard Systems**: Customized dashboards for each user role

### 💳 Booking & Orders
- **Easy Booking Process**: Streamlined ticket purchasing workflow
- **Order Management**: Track and manage all bookings
- **Payment Status Tracking**: Monitor payment status for orders
- **Email Validation**: Verify attendee information
- **Order History**: Complete purchase history for users

### 📊 Admin Features
- **System Analytics**: Comprehensive statistics and reporting
- **User Management**: Manage user accounts and permissions
- **Event Approval**: Review and approve/reject events
- **Order Monitoring**: Track all orders and payments
- **Revenue Tracking**: Financial analytics and reporting
- **Application Review**: Manage coordinator applications

### 🔍 Additional Features
- **Search & Filter**: Advanced search functionality for events
- **Responsive Design**: Mobile-friendly interface
- **Real-time Updates**: Dynamic content loading
- **Error Handling**: Comprehensive error management
- **Security**: SQL injection prevention and input validation

## 📸 Screenshots


![Homepage](https://github.com/user-attachments/assets/f09bc663-3c56-40ea-879b-6233f8dcbd3a)
*Clean and modern homepage showcasing upcoming events with intuitive navigation*

### Homepage
![Event Listings](https://github.com/user-attachments/assets/9cbca63b-8b6e-422b-8745-6fc76b96adee)
*Comprehensive event browsing with filtering and search capabilities*

### Admin Dashboard
![Admin Dashboard](https://github.com/user-attachments/assets/e6abf2e5-2026-4e7c-9c90-0cbc58ad0bc3)
*Powerful admin interface for system management and analytics*

### Event Creation
![Event Creation](https://github.com/user-attachments/assets/da39c6ad-fa0f-4b48-8f2b-bdcdcec81432)
*User-friendly event creation form with comprehensive event details*

## 🛠️ Technologies

**Backend:**
- PHP 7.4+
- MySQL 8.0+
- Apache/Nginx Web Server

**Frontend:**
- HTML5 & CSS3
- JavaScript (ES6+)
- Responsive Design
- AJAX for dynamic content

**Database:**
- MySQL with InnoDB engine
- Foreign key constraints
- Optimized queries

**Security:**
- Session-based authentication
- SQL injection prevention
- Input validation and sanitization
- Role-based access control

## 📋 System Requirements

- **Web Server**: Apache 2.4+ or Nginx 1.16+
- **PHP**: Version 7.4 or higher
- **Database**: MySQL 8.0+ or MariaDB 10.3+
- **Storage**: Minimum 500MB for application and uploads
- **Browser**: Modern browsers (Chrome, Firefox, Safari, Edge)

### PHP Extensions Required:
- mysqli
- session
- json
- fileinfo
- gd (for image handling)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/event-management-system.git
cd event-management-system
```

### 2. Web Server Setup

#### For XAMPP:
1. Copy project folder to `htdocs/emsv2`
2. Start Apache and MySQL services
3. Access via `http://localhost/emsv2`

#### For Production Server:
1. Upload files to web root directory
2. Configure virtual host if needed
3. Set appropriate file permissions

### 3. Database Configuration
1. Import the database schema:
```sql
mysql -u root -p < database.sql
```

2. Update database connection in `includes/db.php`:
```php
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "event_v2";
```

### 4. Configure Base URL
Update `includes/base_url.php` with your domain:
```php
$BASE_URL = "http://your-domain.com/";
```

### 5. Set File Permissions
```bash
chmod 755 uploads/
chmod 755 uploads/events/
```

## 🗄️ Database Setup

The system uses a MySQL database with the following main tables:

- **users**: User accounts and authentication
- **events**: Event information and details
- **ticket_types**: Different ticket categories per event  
- **orders**: Booking and purchase records
- **order_items**: Individual ticket purchases
- **attendees**: Ticket holder information
- **categories**: Event categories
- **coordinator_applications**: Coordinator application system

### Import Database:
```sql
CREATE DATABASE event_v2;
USE event_v2;
SOURCE database.sql;
```

## 👨‍💼 User Roles

### 🔹 Admin
- System administration and management
- Event approval and moderation
- User management and role assignment
- System analytics and reporting
- Order and payment management

### 🔹 Coordinator
- Create and manage events
- Set up ticket types and pricing
- View event analytics and bookings
- Manage attendee lists
- Check-in system access

### 🔹 Checker
- Ticket validation and check-in
- Verify attendee information
- Access to specific events for validation

### 🔹 User (Ordinary)
- Browse and search events
- Purchase tickets and make bookings
- View order history and tickets
- Manage personal profile

## 🔑 Demo Accounts

For testing purposes, use these demo accounts:

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| User | `uoc` | `uoc` |
| Checker | `checker` | `checker` |

*Note: Change these credentials in production environment*

## 📁 Project Structure

```
emsv2/
├── admin/                  # Admin panel pages
│   ├── coordinator_applications.php
│   ├── events_approval.php
│   ├── users_list.php
│   └── ...
├── coordinator/           # Coordinator dashboard
│   ├── dashboard.php
│   ├── event_create.php
│   └── ...
├── assets/               # Static assets
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript files
│   ├── img/             # Images
│   └── icons/           # Icons and favicons
├── includes/            # PHP includes
│   ├── auth.php         # Authentication functions
│   ├── db.php          # Database connection
│   ├── header.php      # Common header
│   └── footer.php      # Common footer
├── process/            # Backend processing
│   ├── login_proc.php  # Login processing
│   ├── reg_proc.php    # Registration processing
│   └── ...
├── uploads/           # File uploads
│   └── events/        # Event images
├── database.sql       # Database schema
├── index.php         # Homepage
├── events.php        # Event listings
├── login.php         # Login page
└── register.php      # Registration page
```

## 🔌 API Endpoints

The system uses AJAX calls to various PHP processors:

### Authentication
- `POST /process/login_proc.php` - User login
- `POST /process/reg_proc.php` - User registration

### Event Management
- `POST /process/event_add_proc.php` - Create new event
- `POST /process/event_edit_proc.php` - Update event
- `POST /process/event_approval_proc.php` - Approve/reject events

### User Management
- `POST /process/user_manage_proc.php` - User role updates
- `POST /process/user_delete_proc.php` - User management

### Booking System
- `GET /process/get_month_events.php` - Get monthly events
- `POST /process/ticket_manage_proc.php` - Ticket operations

## 🤝 Contributing

We welcome contributions to improve the Event Management System!

### How to Contribute:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Contribution Guidelines:
- Follow PSR-12 coding standards for PHP
- Write meaningful commit messages
- Test your changes thoroughly
- Update documentation as needed
- Ensure responsive design compatibility

### Areas for Contribution:
- 🐛 Bug fixes and improvements
- ✨ New features and functionality
- 📚 Documentation improvements
- 🎨 UI/UX enhancements
- 🔒 Security improvements
- 📱 Mobile responsiveness
- 🧪 Test coverage
- 🌐 Internationalization

## 🛡️ Security Features

- **SQL Injection Protection**: Parameterized queries and input sanitization
- **Session Management**: Secure session handling with proper timeouts
- **Role-Based Access**: Strict permission checking for all operations
- **Input Validation**: Server-side validation for all form inputs
- **File Upload Security**: Restricted file types and secure upload handling
- **CSRF Protection**: Protection against cross-site request forgery
- **XSS Prevention**: Output sanitization and content security

## 🐛 Known Issues

- Mobile responsive design needs improvements in some areas
- Email notification system is not implemented
- Payment gateway integration is pending
- Advanced reporting features are in development

## 🔄 Future Enhancements

- [ ] Email notification system for bookings
- [ ] Payment gateway integration (PayPal, Stripe)
- [ ] Mobile application
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] Social media integration
- [ ] Calendar integration
- [ ] Bulk operations for admin
- [ ] API for third-party integrations
- [ ] Enhanced reporting system

## 📞 Support

For support and questions:

- 📧 Create an issue on GitHub
- 💬 Join our Discord community
- 📖 Check the documentation
- 🐛 Report bugs via GitHub Issues

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

### 🙏 Acknowledgments

- Thanks to all contributors who have helped improve this system
- Special thanks to the PHP and MySQL communities
- Icons provided by various open source icon libraries

### 📊 Project Statistics

- **Lines of Code**: ~15,000+
- **Database Tables**: 12
- **Pages**: 30+
- **JavaScript Functions**: 50+
- **Supported Browsers**: Chrome, Firefox, Safari, Edge

---

**Made with ❤️ for the event management community**

*Last updated: September 2025*

