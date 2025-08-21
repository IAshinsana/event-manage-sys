<?php
$page_title = "System Functionalities";
include 'includes/header.php';
?>

<div class="container" style="margin-top: 2rem;">
    <h1>🚀 System Functionalities</h1>
    
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <p style="font-size: 1.1rem; color: #666; text-align: center; margin-bottom: 2rem;">
            This page outlines all the key functionalities implemented in our Event Registration & Ticketing System.
            This comprehensive system was developed for academic purposes to demonstrate modern web development practices.
        </p>
    </div>
    
    <!-- User Management Functions -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #007bff; margin-bottom: 1.5rem;">👥 User Management System</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="border-left: 4px solid #007bff; padding-left: 1rem;">
                <h4>User Registration & Authentication</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Secure user registration with form validation</li>
                    <li>Session-based authentication system</li>
                    <li>Role-based access control (Admin, Checker, Ordinary)</li>
                    <li>Password protection and user management</li>
                </ul>
            </div>
            
            <div style="border-left: 4px solid #28a745; padding-left: 1rem;">
                <h4>Profile Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>User profile creation and editing</li>
                    <li>Account status management (Active/Inactive)</li>
                    <li>User dashboard with personalized content</li>
                    <li>Order and ticket history tracking</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Event Management Functions -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #dc3545; margin-bottom: 1.5rem;">🎉 Event Management System</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="border-left: 4px solid #dc3545; padding-left: 1rem;">
                <h4>Event Creation & Publishing</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Complete event creation with rich details</li>
                    <li>Event categorization and venue management</li>
                    <li>Image upload and media management</li>
                    <li>Event status control (Draft, Published, Archived)</li>
                    <li>Date and time scheduling with validation</li>
                </ul>
            </div>
            
            <div style="border-left: 4px solid #ffc107; padding-left: 1rem;">
                <h4>Event Display & Discovery</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Responsive event listing with search functionality</li>
                    <li>Category-based filtering system</li>
                    <li>Event detail pages with countdown timers</li>
                    <li>Mobile-optimized event cards</li>
                    <li>SEO-friendly URL structure</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Ticketing System Functions -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #17a2b8; margin-bottom: 1.5rem;">🎫 Advanced Ticketing System</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="border-left: 4px solid #17a2b8; padding-left: 1rem;">
                <h4>Ticket Type Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Multiple ticket types per event (VIP, General, etc.)</li>
                    <li>Dynamic pricing with currency support</li>
                    <li>Inventory management with real-time availability</li>
                    <li>Sold-out prevention and quantity validation</li>
                </ul>
            </div>
            
            <div style="border-left: 4px solid #6f42c1; padding-left: 1rem;">
                <h4>Smart Ticket Generation</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Individual ticket codes for each attendee</li>
                    <li>Group ticket functionality with shared codes</li>
                    <li>QR code-compatible ticket identifiers</li>
                    <li>Professional printable ticket designs</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Booking & Order Management -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #28a745; margin-bottom: 1.5rem;">📋 Booking & Order Management</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="border-left: 4px solid #28a745; padding-left: 1rem;">
                <h4>Intelligent Booking Flow</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Multi-step booking process with validation</li>
                    <li>Real-time availability checking</li>
                    <li>Sri Lankan mobile number validation</li>
                    <li>Email validation and confirmation</li>
                    <li>Order review and confirmation system</li>
                </ul>
            </div>
            
            <div style="border-left: 4px solid #fd7e14; padding-left: 1rem;">
                <h4>Order Processing & Tracking</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Order status management (Pending, Paid, Cancelled)</li>
                    <li>Automated inventory updates upon payment</li>
                    <li>Order history and tracking for users</li>
                    <li>Admin order management dashboard</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Payment & Promo System -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #6610f2; margin-bottom: 1.5rem;">💰 Payment & Promotion System</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="border-left: 4px solid #6610f2; padding-left: 1rem;">
                <h4>Offline Payment Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Manual payment confirmation system</li>
                    <li>Admin-controlled payment status updates</li>
                    <li>Revenue tracking and reporting</li>
                    <li>Payment history and audit trails</li>
                </ul>
            </div>
            
            <div style="border-left: 4px solid #e83e8c; padding-left: 1rem;">
                <h4>Promotional Code System</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Percentage and fixed amount discounts</li>
                    <li>Time-limited promotional campaigns</li>
                    <li>Usage limit controls per promo code</li>
                    <li>Event-specific promotion management</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Check-in & Validation -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #20c997; margin-bottom: 1.5rem;">✅ Check-in & Validation System</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="border-left: 4px solid #20c997; padding-left: 1rem;">
                <h4>Smart Check-in Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Ticket code and email-based search</li>
                    <li>Individual attendee check-in tracking</li>
                    <li>Group ticket batch check-in functionality</li>
                    <li>Real-time attendance counting</li>
                    <li>Check-in history and timestamps</li>
                </ul>
            </div>
            
            <div style="border-left: 4px solid #6c757d; padding-left: 1rem;">
                <h4>Validation & Security</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Role-based check-in permissions</li>
                    <li>Paid ticket validation before check-in</li>
                    <li>Duplicate check-in prevention</li>
                    <li>Staff activity logging</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Waitlist Management System -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #ed8936; margin-bottom: 1.5rem;">📋 Advanced Waitlist System</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="border-left: 4px solid #ed8936; padding-left: 1rem;">
                <h4>Smart Waitlist Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Automatic waitlist display when no tickets available</li>
                    <li>Queue position tracking for fair priority system</li>
                    <li>User-friendly waitlist joining with terms acceptance</li>
                    <li>Personal waitlist dashboard with status tracking</li>
                    <li>Real-time status updates (waiting, invited, expired, purchased)</li>
                </ul>
            </div>
            
            <div style="border-left: 4px solid #f39c12; padding-left: 1rem;">
                <h4>24-Hour Grace Period System</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Automated invitation system for waitlisted users</li>
                    <li>24-hour reserved ticket purchase window</li>
                    <li>Automatic expiration of unused invitations</li>
                    <li>Email reminder system preparation</li>
                    <li>Bulk invitation management for coordinators</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Coordinator Features -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #8e44ad; margin-bottom: 1.5rem;">👨‍💼 Enhanced Coordinator Features</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="border-left: 4px solid #8e44ad; padding-left: 1rem;">
                <h4>Waitlist Management Dashboard</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Comprehensive waitlist statistics (waiting, invited, expired, purchased)</li>
                    <li>User contact information and join timestamps</li>
                    <li>Bulk user invitation with configurable grace periods</li>
                    <li>Manual expiration control for old invitations</li>
                    <li>Position-based queue management system</li>
                </ul>
            </div>
            
            <div style="border-left: 4px solid #9b59b6; padding-left: 1rem;">
                <h4>Event Creation & Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Multi-format image upload (WebP, JPEG, PNG, GIF support)</li>
                    <li>Automatic image optimization and validation</li>
                    <li>Event approval workflow system</li>
                    <li>Revenue tracking and attendance analytics</li>
                    <li>Integrated ticket and waitlist management</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Technical Features -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #495057; margin-bottom: 1.5rem;">⚙️ Technical Features & Architecture</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="border-left: 4px solid #495057; padding-left: 1rem;">
                <h4>Modern Frontend Technologies</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Responsive CSS Grid and Flexbox layouts</li>
                    <li>Mobile-first design approach with clean UI</li>
                    <li>Modern CSS animations and transitions</li>
                    <li>SVG icons and optimized graphics</li>
                    <li>Page-specific CSS architecture for better maintainability</li>
                    <li>Gradient-free clean design aesthetic</li>
                </ul>
            </div>
            
            <div style="border-left: 4px solid #007bff; padding-left: 1rem;">
                <h4>Robust Backend Architecture</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Pure PHP with MySQLi database integration</li>
                    <li>Advanced session-based state management</li>
                    <li>Modular include file structure</li>
                    <li>Dynamic base URL for deployment flexibility</li>
                    <li>Multi-role access control (Admin, Coordinator, Checker, User)</li>
                    <li>Database foreign key constraints and data integrity</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Latest Updates & Improvements -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #28a745; margin-bottom: 1.5rem;">� Latest Updates & Improvements</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="border-left: 4px solid #28a745; padding-left: 1rem;">
                <h4>Enhanced Image Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Multi-format support: WebP, JPEG, PNG, GIF, TIFF, BMP</li>
                    <li>Server capability detection for optimal format support</li>
                    <li>File size validation and security checks</li>
                    <li>Consistent image sizing across event cards</li>
                    <li>Fallback handling for unsupported formats</li>
                </ul>
            </div>
            
            <div style="border-left: 4px solid #17a2b8; padding-left: 1rem;">
                <h4>Automation & Monitoring</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Automated cron job scripts for waitlist management</li>
                    <li>Grace period expiration automation</li>
                    <li>Email reminder system framework</li>
                    <li>Database cleanup and maintenance scripts</li>
                    <li>System health monitoring and error logging</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
