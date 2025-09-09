# 🎫 EventTickets - Complete Event Management System

<img width="1737" height="852" alt="image" src="https://github.com/user-attachments/assets/f09bc663-3c56-40ea-879b-6233f8dcbd3a" />

A comprehensive web application for event registration, ticketing, and management built with PHP, MySQL, HTML, CSS, and JavaScript.

<img width="1900" height="897" alt="front" src="https://github.com/user-attachments/assets/9cbca63b-8b6e-422b-8745-6fc76b96adee" />

<img width="1892" height="892" alt="admin" src="https://github.com/user-attachments/assets/e6abf2e5-2026-4e7c-9c90-0cbc58ad0bc3" />

<img width="1895" height="886" alt="add event" src="https://github.com/user-attachments/assets/da39c6ad-fa0f-4b48-8f2b-bdcdcec81432" />

## 🌟 Features

### 👥 User Management
- **Multi-role System**: Admin, Coordinator, Checker, and Regular Users
- **Secure Authentication**: Session-based login with role-based access control
- **User Registration**: Simple registration process with account activation
- **Profile Management**: User dashboard with personalized content

### 🎉 Event Management
- **Event Creation**: Rich event details with images, venue, and scheduling
- **Multi-format Images**: Support for WebP, JPEG, PNG, GIF, and other formats
- **Event Categories**: Organized event browsing and filtering
- **Event Status**: Draft, Published, and Archived event states
- **Responsive Design**: Mobile-optimized event cards and layouts

### 🎫 Advanced Ticketing System
- **Multiple Ticket Types**: VIP, General, Early Bird, etc.
- **Dynamic Pricing**: Flexible pricing with currency support
- **Inventory Management**: Real-time availability tracking
- **Group Tickets**: Individual and group ticket options
- **Ticket Generation**: Unique codes for each ticket

### 📋 Smart Waitlist System
- **Automatic Waitlists**: When no tickets are available
- **Queue Management**: Fair first-come, first-served system
- **24-Hour Grace Period**: Reserved purchase window for invited users
- **Status Tracking**: Waiting, Invited, Expired, Purchased statuses
- **Bulk Invitations**: Coordinator tools for managing waitlists

### 💰 Order & Payment Management
- **Order Processing**: Multi-step booking with validation
- **Payment Tracking**: Manual payment confirmation system
- **Promotional Codes**: Percentage and fixed amount discounts
- **Order History**: Complete order tracking for users
- **Revenue Analytics**: Financial reporting for coordinators

### ✅ Check-in System
- **Ticket Validation**: Search by ticket code or email
- **Attendance Tracking**: Individual and group check-in
- **Real-time Counting**: Live attendance statistics
- **Staff Management**: Role-based check-in permissions
- **Check-in History**: Complete audit trail

### 🎨 Modern UI/UX
- **Clean Design**: Gradient-free, professional appearance
- **Responsive Layout**: Mobile-first design approach
- **Page-specific CSS**: Modular stylesheet architecture
- **SVG Icons**: Scalable vector graphics throughout
- **Smooth Animations**: CSS transitions and hover effects

## 🛠️ Technical Specifications

### Backend
- **PHP 7.4+**: Pure PHP with MySQLi integration
- **MySQL 8.0+**: Relational database with foreign key constraints
- **Session Management**: Secure user state handling
- **File Upload**: Multi-format image support with validation
- **Database Design**: Normalized tables with proper relationships

### Frontend
- **HTML5**: Semantic markup structure
- **CSS3**: Grid, Flexbox, and modern CSS features
- **JavaScript**: Form validation and interactive elements
- **Responsive Design**: Mobile-compatible layouts
- **Cross-browser**: Compatible with modern browsers

### Security Features
- **SQL Injection Prevention**: Prepared statements
- **File Upload Security**: Type and size validation
- **Session Security**: Proper session handling
- **Access Control**: Role-based permissions
- **Data Validation**: Server-side input validation

## 📁 Project Structure

```
event/
├── admin/                 # Admin panel files
├── coordinator/           # Coordinator dashboard
├── assets/               # CSS, JS, and static files
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript files
│   └── images/          # Static images
├── database/            # Database schema files
├── includes/            # Shared PHP includes
├── scripts/             # Automation scripts
├── uploads/             # User-uploaded files
└── *.php               # Main application files
```

## 🚀 Installation

### Requirements
- PHP 7.4 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx)
- mod_rewrite enabled (optional)

### Setup Steps

1. **Clone/Download** the project to your web server
2. **Database Setup**:
   ```sql
   CREATE DATABASE event_v2;
   mysql -u root -p event_v2 < database.sql
   mysql -u root -p event_v2 < database/waitlist_schema.sql
   ```
3. **Configure Database**:
   - Update `includes/db.php` with your database credentials
4. **Set Permissions**:
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/events/
   ```
5. **Create Admin Account**:
   - Register a user and manually set role to 'admin' in database
6. **Configure Base URL**:
   - Update `includes/base_url.php` if needed

## 👨‍💼 User Roles

### 🔧 Admin
- Full system access
- User management
- Event approval
- System configuration
- Financial reporting

### 👨‍🎓 Coordinator
- Create and manage events
- Ticket type management
- Waitlist management
- Order processing
- Attendance tracking

### ✅ Checker
- Event check-in access
- Ticket validation
- Attendance recording
- Basic event information

### 👤 Regular User
- Event browsing
- Ticket booking
- Waitlist joining
- Order tracking
- Profile management

## 📊 Key Features by Module

### Event Management
- Event CRUD operations
- Image upload with multiple format support
- Category management
- Event approval workflow
- Archive system

### Ticketing
- Multiple ticket types per event
- Dynamic pricing
- Inventory tracking
- Group ticket support
- Promotional codes

### Waitlist System
- Automatic waitlist activation
- Queue position tracking
- 24-hour grace periods
- Bulk invitation system
- Status management

### Order Management
- Multi-step booking process
- Payment confirmation
- Order status tracking
- Revenue reporting
- Attendee management

### Check-in System
- Multiple search methods
- Individual/group check-in
- Attendance statistics
- Staff activity logging
- Duplicate prevention

## 🔄 Automation

### Cron Jobs
Set up the following cron job for waitlist automation:
```bash
0 * * * * /usr/bin/php /path/to/project/scripts/waitlist_automation.php
```

This handles:
- Automatic invitation expiration
- Email reminder system
- Database cleanup
- System maintenance

## 🎯 Usage Guide

### For Users
1. **Register** an account
2. **Browse Events** on the homepage or events page
3. **Book Tickets** or **Join Waitlists**
4. **Track Orders** in "My Orders"
5. **Access Tickets** in "My Tickets"
6. **Manage Waitlists** in "My Waitlist"

### For Coordinators
1. **Apply** for coordinator status
2. **Create Events** with details and images
3. **Add Ticket Types** with pricing
4. **Manage Waitlists** and invite users
5. **Process Orders** and confirm payments
6. **Track Analytics** and attendance

### For Admins
1. **Approve Coordinators** and events
2. **Manage Users** and roles
3. **Monitor System** activity
4. **Process Payments** and orders
5. **Generate Reports** and analytics

## 🔧 Customization

### Styling
- Modify CSS files in `assets/css/`
- Page-specific styles for targeted changes
- Responsive breakpoints in media queries

### Features
- Add new user roles in `includes/auth.php`
- Extend database schema as needed
- Create new modules following existing patterns

### Configuration
- Database settings in `includes/db.php`
- Base URL in `includes/base_url.php`
- File upload limits in event creation forms

## 📱 Mobile Support

The system is fully responsive with:
- Mobile-optimized layouts
- Touch-friendly interfaces
- Adaptive navigation
- Responsive images
- Mobile-first CSS

## 🔐 Security

- Prepared SQL statements
- File upload validation
- Session security
- Role-based access control
- Input sanitization
- CSRF protection considerations

## 📈 Performance

- Optimized database queries
- Image compression support
- Minimal external dependencies
- Efficient CSS/JS loading
- Database indexing

## 🆕 Latest Updates

- **Waitlist System**: Complete queue management
- **Enhanced Images**: WebP and multi-format support
- **Clean UI**: Gradient-free modern design
- **Page-specific CSS**: Better maintainability
- **Automation**: Cron job scripts for maintenance

## 📝 License

This project is developed for educational and portfolio purposes. Feel free to use and modify as needed.

## 🤝 Contributing

This is a complete, production-ready system. For enhancements:
1. Follow the existing code structure
2. Test thoroughly before deployment
3. Update documentation as needed
4. Consider security implications

---

**EventTickets** - Complete Event Management Solution

