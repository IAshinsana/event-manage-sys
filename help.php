<?php
$page_title = "Help & User Guide";
include 'includes/header.php';
?>

<div class="container" style="margin-top: 2rem;">
    <h1>❓ Help & User Guide</h1>
    
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <p style="font-size: 1.1rem; color: #666; text-align: center; margin-bottom: 2rem;">
            Welcome to EventTickets! This guide will help you navigate through our event registration and ticketing system.
            Whether you're a new user, event organizer, or admin, this guide has everything you need to get started.
        </p>
    </div>
    
    <!-- Getting Started -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #007bff; margin-bottom: 1.5rem;">🚀 Getting Started</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #28a745; margin-bottom: 1rem;">📝 Creating an Account</h4>
                <ol style="color: #666; line-height: 1.8;">
                    <li>Click the "Register" button in the top navigation</li>
                    <li>Fill in your full name, choose a username, and create a password</li>
                    <li>Confirm your password and click "Register"</li>
                    <li>You'll be redirected to login with your new account</li>
                </ol>
            </div>
            
            <div>
                <h4 style="color: #dc3545; margin-bottom: 1rem;">🔐 Logging In</h4>
                <ol style="color: #666; line-height: 1.8;">
                    <li>Click the "Sign In" button in the navigation</li>
                    <li>Enter your username and password</li>
                    <li>Click "Sign In" to access your account</li>
                    <li>You'll be redirected to the home page or admin panel</li>
                </ol>
            </div>
        </div>
    </div>
    
    <!-- For Regular Users -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #17a2b8; margin-bottom: 1.5rem;">👤 For Regular Users</h2>
        
        <div style="margin-bottom: 2rem;">
            <h4 style="color: #6610f2; margin-bottom: 1rem;">🎫 How to Book Tickets</h4>
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #6610f2;">
                <ol style="color: #666; line-height: 1.8; margin: 0;">
                    <li><strong>Browse Events:</strong> Visit the Events page to see all available events</li>
                    <li><strong>Select Event:</strong> Click "View Details" on any event you're interested in</li>
                    <li><strong>Choose Tickets:</strong> Click "Book Now" and select the number of tickets you want</li>
                    <li><strong>Enter Details:</strong> Provide your contact information for all tickets</li>
                    <li><strong>Review Order:</strong> Check your order details and apply any promo codes</li>
                    <li><strong>Confirm:</strong> Complete your booking and wait for payment confirmation</li>
                </ol>
            </div>
        </div>

        <div style="margin-bottom: 2rem;">
            <h4 style="color: #ed8936; margin-bottom: 1rem;">📋 How to Join Waitlists</h4>
            <div style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #ed8936;">
                <ol style="color: #666; line-height: 1.8; margin: 0;">
                    <li><strong>Find Events Without Tickets:</strong> Look for events showing "Join Waitlist" instead of pricing</li>
                    <li><strong>Click Join Waitlist:</strong> Click the orange "Join Waitlist" button on event cards</li>
                    <li><strong>Read Terms:</strong> Review and accept the waitlist terms and conditions</li>
                    <li><strong>Confirm Joining:</strong> Click "Join Waitlist" to secure your position</li>
                    <li><strong>Track Position:</strong> See your position in the queue and current status</li>
                    <li><strong>Wait for Invitation:</strong> You'll be notified when tickets become available</li>
                    <li><strong>Purchase Quickly:</strong> When invited, you have 24 hours to complete your purchase</li>
                </ol>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #28a745; margin-bottom: 1rem;">📋 Managing Your Orders</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Access "My Orders" from the navigation menu</li>
                    <li>View all your orders with status information</li>
                    <li>Track payment status (Pending, Paid, Cancelled)</li>
                    <li>View order details and event information</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: #fd7e14; margin-bottom: 1rem;">🎟️ Accessing Your Tickets</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Go to "My Tickets" after your order is marked as paid</li>
                    <li>Print individual tickets or group tickets</li>
                    <li>Present printed tickets at the event entrance</li>
                    <li>Check ticket codes and attendee information</li>
                </ul>
            </div>

            <div>
                <h4 style="color: #8e44ad; margin-bottom: 1rem;">📋 Managing Your Waitlists</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Visit "My Waitlist" to see all your waitlisted events</li>
                    <li>Check your position and status for each event</li>
                    <li>Leave waitlists for events you're no longer interested in</li>
                    <li>Monitor invitation status and expiration times</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Waitlist System Guide -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #ed8936; margin-bottom: 1.5rem;">📋 Understanding the Waitlist System</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div style="border: 1px solid #ed8936; border-radius: 8px; padding: 1.5rem;">
                <h4 style="color: #ed8936; margin-bottom: 1rem;">⏳ Waiting Status</h4>
                <ul style="color: #666; line-height: 1.8; margin: 0;">
                    <li>You're in the queue waiting for tickets</li>
                    <li>Your position shows where you are in line</li>
                    <li>First-come, first-served priority system</li>
                    <li>You'll be invited when tickets become available</li>
                </ul>
            </div>
            
            <div style="border: 1px solid #3182ce; border-radius: 8px; padding: 1.5rem;">
                <h4 style="color: #3182ce; margin-bottom: 1rem;">✉️ Invited Status</h4>
                <ul style="color: #666; line-height: 1.8; margin: 0;">
                    <li>You've been invited to purchase tickets</li>
                    <li>You have exactly 24 hours to complete purchase</li>
                    <li>Visit the event page to buy your tickets</li>
                    <li>Invitation expires automatically if not used</li>
                </ul>
            </div>

            <div style="border: 1px solid #48bb78; border-radius: 8px; padding: 1.5rem;">
                <h4 style="color: #48bb78; margin-bottom: 1rem;">✅ Purchased Status</h4>
                <ul style="color: #666; line-height: 1.8; margin: 0;">
                    <li>You successfully purchased tickets</li>
                    <li>Check "My Orders" for payment status</li>
                    <li>Access tickets from "My Tickets" when paid</li>
                    <li>You're all set for the event!</li>
                </ul>
            </div>

            <div style="border: 1px solid #e53e3e; border-radius: 8px; padding: 1.5rem;">
                <h4 style="color: #e53e3e; margin-bottom: 1rem;">⏰ Expired Status</h4>
                <ul style="color: #666; line-height: 1.8; margin: 0;">
                    <li>Your 24-hour purchase window has passed</li>
                    <li>You can rejoin the waitlist if still interested</li>
                    <li>You'll go to the back of the queue</li>
                    <li>Act quickly when next invited!</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Ticket Types -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #e83e8c; margin-bottom: 1.5rem;">🎫 Understanding Ticket Types</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div style="border: 1px solid #e83e8c; border-radius: 8px; padding: 1.5rem;">
                <h4 style="color: #e83e8c; margin-bottom: 1rem;">🎪 Individual Tickets</h4>
                <ul style="color: #666; line-height: 1.8; margin: 0;">
                    <li>Each ticket gets a unique code</li>
                    <li>Perfect for individual attendees</li>
                    <li>Can be printed separately</li>
                    <li>Individual check-in tracking</li>
                </ul>
            </div>
            
            <div style="border: 1px solid #20c997; border-radius: 8px; padding: 1.5rem;">
                <h4 style="color: #20c997; margin-bottom: 1rem;">👥 Group Tickets</h4>
                <ul style="color: #666; line-height: 1.8; margin: 0;">
                    <li>One shared code for all tickets in the order</li>
                    <li>Great for families and groups</li>
                    <li>Single group ticket printout</li>
                    <li>Flexible group check-in options</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- For Admins -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #dc3545; margin-bottom: 1.5rem;">👨‍💼 For Administrators & Coordinators</h2>
        
        <div style="margin-bottom: 2rem;">
            <h4 style="color: #007bff; margin-bottom: 1rem;">🎉 Creating and Managing Events</h4>
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #007bff;">
                <ol style="color: #666; line-height: 1.8; margin: 0;">
                    <li><strong>Access Admin Panel:</strong> Login as admin/coordinator and go to Admin/Coordinator section</li>
                    <li><strong>Create Event:</strong> Click "Create New Event" and fill in event details</li>
                    <li><strong>Upload Images:</strong> Support for WebP, JPEG, PNG, GIF, and other formats</li>
                    <li><strong>Add Ticket Types:</strong> Create different ticket categories with pricing</li>
                    <li><strong>Publish Event:</strong> Change status to "Published" to make it visible</li>
                    <li><strong>Manage Orders:</strong> Mark orders as paid when payment is received</li>
                </ol>
            </div>
        </div>

        <div style="margin-bottom: 2rem;">
            <h4 style="color: #ed8936; margin-bottom: 1rem;">📋 Managing Event Waitlists</h4>
            <div style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #ed8936;">
                <ol style="color: #666; line-height: 1.8; margin: 0;">
                    <li><strong>Access Waitlist:</strong> Go to "Manage Waitlist" from your events list</li>
                    <li><strong>View Statistics:</strong> See waiting, invited, expired, and purchased counts</li>
                    <li><strong>Invite Users:</strong> Select number of users to invite from waitlist</li>
                    <li><strong>Set Grace Period:</strong> Users get 24 hours to purchase after invitation</li>
                    <li><strong>Expire Old Invitations:</strong> Clean up expired invitations to keep queue moving</li>
                    <li><strong>Monitor Activity:</strong> Track user contact info and join timestamps</li>
                </ol>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #28a745; margin-bottom: 1rem;">💰 Order Management</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>View all orders in the Orders section</li>
                    <li>Filter orders by status (Pending, Paid, Cancelled)</li>
                    <li>Mark pending orders as paid manually</li>
                    <li>View detailed order information and attendees</li>
                    <li>Track revenue and attendance analytics</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: #6610f2; margin-bottom: 1rem;">📊 Reports & Analytics</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Dashboard shows key statistics</li>
                    <li>Track total users, events, and revenue</li>
                    <li>Monitor pending orders requiring attention</li>
                    <li>View recent activity and order history</li>
                    <li>Waitlist demand insights for event planning</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- For Checkers -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #20c997; margin-bottom: 1.5rem;">✅ For Ticket Checkers</h2>
        
        <div style="margin-bottom: 2rem;">
            <h4 style="color: #17a2b8; margin-bottom: 1rem;">🔍 Checking In Attendees</h4>
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #17a2b8;">
                <ol style="color: #666; line-height: 1.8; margin: 0;">
                    <li><strong>Access Check-in:</strong> Login and go to "Check Tickets" section</li>
                    <li><strong>Search Tickets:</strong> Enter ticket code or email address</li>
                    <li><strong>Verify Information:</strong> Check attendee details and event information</li>
                    <li><strong>Check In:</strong> Use "Check In" button for individual attendees</li>
                    <li><strong>Group Check-in:</strong> Use "Check In All" for group tickets</li>
                </ol>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #ffc107; margin-bottom: 1rem;">📱 Search Methods</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li><strong>Ticket Code:</strong> TKT123, GRP456, etc.</li>
                    <li><strong>Email Address:</strong> user@example.com</li>
                    <li><strong>Partial Matches:</strong> Search works with partial codes</li>
                    <li><strong>Case Insensitive:</strong> Uppercase/lowercase doesn't matter</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: #28a745; margin-bottom: 1rem;">📊 Attendance Tracking</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li>View total seats for each ticket</li>
                    <li>Track checked-in vs remaining attendees</li>
                    <li>See check-in timestamps and staff</li>
                    <li>Prevent duplicate check-ins automatically</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Common Issues -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #fd7e14; margin-bottom: 1.5rem;">⚠️ Common Issues & Solutions</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #dc3545; margin-bottom: 1rem;">🔒 Login Problems</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li><strong>Forgotten Password:</strong> Contact admin to reset</li>
                    <li><strong>Account Inactive:</strong> Ask admin to activate account</li>
                    <li><strong>Wrong Credentials:</strong> Check username and password</li>
                    <li><strong>Browser Issues:</strong> Clear cache and cookies</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: #6610f2; margin-bottom: 1rem;">🎫 Booking Issues</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li><strong>Sold Out Events:</strong> Join the waitlist instead</li>
                    <li><strong>Payment Pending:</strong> Wait for admin confirmation</li>
                    <li><strong>Invalid Mobile:</strong> Use Sri Lankan format (070xxxxxxx)</li>
                    <li><strong>Email Issues:</strong> Check format and spelling</li>
                </ul>
            </div>

            <div>
                <h4 style="color: #ed8936; margin-bottom: 1rem;">📋 Waitlist Issues</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li><strong>Can't Join Waitlist:</strong> Ensure you're logged in</li>
                    <li><strong>Position Not Updating:</strong> Refresh the page</li>
                    <li><strong>Invitation Expired:</strong> You can rejoin the waitlist</li>
                    <li><strong>Missing Invitation:</strong> Check "My Waitlist" page</li>
                </ul>
            </div>

            <div>
                <h4 style="color: #17a2b8; margin-bottom: 1rem;">🖼️ Image Upload Issues</h4>
                <ul style="color: #666; line-height: 1.8;">
                    <li><strong>File Too Large:</strong> Keep images under 5MB</li>
                    <li><strong>Wrong Format:</strong> Use JPEG, PNG, WebP, or GIF</li>
                    <li><strong>Upload Failed:</strong> Check file permissions</li>
                    <li><strong>Image Not Showing:</strong> Clear browser cache</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- System Features Summary -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="color: #28a745; margin-bottom: 1.5rem;">✨ Latest System Features</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #ed8936;">
                <h4 style="color: #ed8936; margin-bottom: 1rem;">📋 Smart Waitlist System</h4>
                <ul style="color: #666; line-height: 1.8; margin: 0;">
                    <li>Automatic waitlist when no tickets available</li>
                    <li>Fair queue positioning system</li>
                    <li>24-hour grace period for purchasing</li>
                    <li>Real-time status tracking</li>
                </ul>
            </div>
            
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #17a2b8;">
                <h4 style="color: #17a2b8; margin-bottom: 1rem;">🖼️ Enhanced Media Support</h4>
                <ul style="color: #666; line-height: 1.8; margin: 0;">
                    <li>WebP and modern image format support</li>
                    <li>Automatic server capability detection</li>
                    <li>Optimized image sizing and display</li>
                    <li>Multiple format fallback system</li>
                </ul>
            </div>
            
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #28a745;">
                <h4 style="color: #28a745; margin-bottom: 1rem;">🎨 Modern UI Design</h4>
                <ul style="color: #666; line-height: 1.8; margin: 0;">
                    <li>Clean, gradient-free design</li>
                    <li>Page-specific CSS architecture</li>
                    <li>Mobile-responsive layouts</li>
                    <li>Consistent visual elements</li>
                </ul>
            </div>
            
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #8e44ad;">
                <h4 style="color: #8e44ad; margin-bottom: 1rem;">⚙️ Advanced Automation</h4>
                <ul style="color: #666; line-height: 1.8; margin: 0;">
                    <li>Cron job scripts for maintenance</li>
                    <li>Automatic invitation expiration</li>
                    <li>Email reminder system framework</li>
                    <li>Database cleanup automation</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
