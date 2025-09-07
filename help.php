<?php
$page_title = "Help & User Guide";
include 'includes/header.php';
?>

<div class="container" style="margin-top: 2rem;">
    <h1>❓ Help & User Guide</h1>
    
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <p style="font-size: 1.1rem; color: #666; text-align: center; margin-bottom: 2rem;">
            Welcome to EventGate! This guide will help you navigate through our event registration and ticketing system.
        </p>
    </div>
    
    <!-- Getting Started -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: var(--primary); margin-bottom: 1.5rem;">🚀 Getting Started</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">📝 Creating an Account</h4>
                <ol style="color: #666; line-height: 1.8;">
                    <li>Click the "Register" button in the navigation</li>
                    <li>Fill in your full name, username, and password</li>
                    <li>Click "Register" to create your account</li>
                    <li>Login with your new credentials</li>
                </ol>
            </div>
            
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">🔐 Logging In</h4>
                <ol style="color: #666; line-height: 1.8;">
                    <li>Click "Sign In" in the navigation</li>
                    <li>Enter your username and password</li>
                    <li>Click "Sign In" to access your account</li>
                    <li>You'll be redirected to the main page</li>
                </ol>
            </div>
        </div>
    </div>
    
    <!-- For Users -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: var(--primary); margin-bottom: 1.5rem;">👤 For Users</h2>
        
        <div style="margin-bottom: 2rem;">
            <h4 style="color: var(--accent); margin-bottom: 1rem;">🎫 How to Book Tickets</h4>
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                <ol style="color: #666; line-height: 1.8; margin: 0;">
                    <li><strong>Browse Events:</strong> Visit the Events page to see available events</li>
                    <li><strong>Select Event:</strong> Click "View Details" on any event</li>
                    <li><strong>Choose Tickets:</strong> Click "Book Now" and select ticket quantity</li>
                    <li><strong>Enter Details:</strong> Provide contact information for all attendees</li>
                    <li><strong>Review Order:</strong> Check details and complete booking</li>
                    <li><strong>Wait for Confirmation:</strong> Admin will confirm payment</li>
                </ol>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">📋 My Orders</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>View all your event orders</li>
                    <li>Track payment status (Pending/Paid/Cancelled)</li>
                    <li>See order details and event information</li>
                    <li>Contact admin for payment issues</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">🎟️ My Tickets</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Access tickets after payment confirmation</li>
                    <li>Print individual or group tickets</li>
                    <li>Present tickets at event entrance</li>
                    <li>Check ticket codes and attendee info</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- For Coordinators -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: var(--primary); margin-bottom: 1.5rem;">👨‍💼 For Coordinators</h2>
        
        <div style="margin-bottom: 2rem;">
            <h4 style="color: var(--accent); margin-bottom: 1rem;">🎉 Creating Events</h4>
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                <ol style="color: #666; line-height: 1.8; margin: 0;">
                    <li><strong>Access Dashboard:</strong> Login and go to Coordinator section</li>
                    <li><strong>Create Event:</strong> Click "Create New Event"</li>
                    <li><strong>Fill Details:</strong> Add event name, description, venue, date</li>
                    <li><strong>Upload Image:</strong> Add event poster/banner</li>
                    <li><strong>Add Tickets:</strong> Create ticket types with pricing</li>
                    <li><strong>Submit:</strong> Wait for admin approval</li>
                </ol>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">📊 Event Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>View your event list and status</li>
                    <li>Edit event details before approval</li>
                    <li>Track ticket sales and revenue</li>
                    <li>Manage ticket types and pricing</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">🎫 Ticket Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>View all ticket sales</li>
                    <li>Check attendee information</li>
                    <li>Monitor inventory levels</li>
                    <li>Export attendee lists</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- For Admins -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: var(--primary); margin-bottom: 1.5rem;">👨‍💼 For Administrators</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">🎉 Event Approval</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Review submitted events</li>
                    <li>Approve or reject event applications</li>
                    <li>Manage event categories</li>
                    <li>Archive old events</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">💰 Order Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>View all orders across events</li>
                    <li>Mark orders as paid manually</li>
                    <li>Handle order cancellations</li>
                    <li>Generate revenue reports</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">👥 User Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Manage user accounts</li>
                    <li>Approve coordinator applications</li>
                    <li>Set user roles and permissions</li>
                    <li>Handle account issues</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">📊 System Statistics</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>View dashboard analytics</li>
                    <li>Track total users and events</li>
                    <li>Monitor system performance</li>
                    <li>Generate system reports</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- For Checkers -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: var(--primary); margin-bottom: 1.5rem;">✅ For Ticket Checkers</h2>
        
        <div style="margin-bottom: 2rem;">
            <h4 style="color: var(--accent); margin-bottom: 1rem;">🔍 Checking In Attendees</h4>
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                <ol style="color: #666; line-height: 1.8; margin: 0;">
                    <li><strong>Access Check-in:</strong> Login and go to "Check Tickets"</li>
                    <li><strong>Search Tickets:</strong> Enter ticket code or email</li>
                    <li><strong>Verify Details:</strong> Check attendee and event information</li>
                    <li><strong>Check In:</strong> Click "Check In" for individuals or "Check In All" for groups</li>
                </ol>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">🔍 Search Methods</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li><strong>Ticket Code:</strong> TKT123, GRP456, etc.</li>
                    <li><strong>Email Address:</strong> user@example.com</li>
                    <li><strong>Partial Search:</strong> Works with partial codes</li>
                    <li><strong>Case Insensitive:</strong> Uppercase/lowercase doesn't matter</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">📊 Attendance Tracking</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>View total seats per ticket</li>
                    <li>Track checked-in vs remaining attendees</li>
                    <li>See check-in timestamps</li>
                    <li>Prevent duplicate check-ins</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Common Issues -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: var(--primary); margin-bottom: 1.5rem;">⚠️ Common Issues</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">🔒 Login Problems</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li><strong>Forgotten Password:</strong> Contact admin for reset</li>
                    <li><strong>Account Issues:</strong> Check with admin for activation</li>
                    <li><strong>Wrong Credentials:</strong> Verify username and password</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">🎫 Booking Issues</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li><strong>Sold Out:</strong> Event tickets are no longer available</li>
                    <li><strong>Payment Pending:</strong> Wait for admin to confirm payment</li>
                    <li><strong>Invalid Details:</strong> Check email format and mobile number</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: var(--accent); margin-bottom: 1rem;">🖼️ Image Upload</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li><strong>File Too Large:</strong> Keep images under 5MB</li>
                    <li><strong>Wrong Format:</strong> Use JPEG, PNG, WebP, or GIF</li>
                    <li><strong>Upload Failed:</strong> Check file and try again</li>
                </ul>
            </div>
            
          
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
