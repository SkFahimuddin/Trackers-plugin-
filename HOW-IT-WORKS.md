# How This Plugin Works - Simple Explanation

## Overview
This plugin tracks how long students spend on pages in WordPress.

## The Flow

1. Student logs in and visits a lesson page
2. JavaScript timer starts counting seconds
3. When student leaves the page, time is saved
4. Teacher can see all the data in admin area
5. Student can see their own history using a shortcode

## Code Breakdown (Line by Line)

### Part 1: Plugin Header (Lines 2-6)
```php
/**
 * Plugin Name: Tracker
 * Description: Simple plugin to track student time on pages
 * Version: 1.0
 * Author: Fahim
 */
```
This tells WordPress about the plugin. WordPress reads this to show the plugin in the plugins list.

### Part 2: Security Check (Lines 8-10)
```php
if (!defined('ABSPATH')) {
    exit;
}
```
This stops people from accessing the file directly (security measure).

### Part 3: Create Database Table (Lines 12-28)
```php
function tracker_install() {
    // Create a table called wp_tracker_data
    // Table has: id, user_id, page_title, time_spent, visit_date
}
register_activation_hook(__FILE__, 'tracker_install');
```
When you activate the plugin, this creates a database table to store tracking data.

**What it creates:**
- Table name: `wp_tracker_data`
- Columns: id, user_id, page_title, time_spent, visit_date

### Part 4: Add JavaScript Timer (Lines 30-53)
```php
function tracker_add_script() {
    // Only for logged-in users
    // Adds JavaScript to the page
}
```
This adds JavaScript code to every page. The JavaScript:
1. Records when the page loads (startTime)
2. Calculates time spent when user leaves
3. Sends data to WordPress using navigator.sendBeacon

**Key JavaScript variables:**
- `startTime` = when page loaded
- `endTime` = when user leaves
- `timeSpent` = difference in seconds

### Part 5: Save Data to Database (Lines 55-73)
```php
function tracker_save_time() {
    // Get data from JavaScript
    // Save to database
}
```
This function receives data from JavaScript and saves it to the database.

**Steps:**
1. Get user_id, page_title, time_spent from form data
2. Clean the data (sanitize)
3. Insert into database table

### Part 6: Add Admin Menu (Lines 75-84)
```php
function tracker_admin_menu() {
    add_menu_page(...);
}
```
This creates a menu item called "Tracker" in the WordPress admin sidebar.

### Part 7: Display Data in Admin (Lines 86-121)
```php
function tracker_admin_page() {
    // Get data from database
    // Display in a table
}
```
This shows all tracking data in the admin area.

**What it does:**
1. Gets last 50 records from database
2. Converts seconds to "Xm Ys" format
3. Displays in a WordPress-style table

### Part 8: Student History Shortcode (Lines 123-157)
```php
function tracker_student_history() {
    // Show current user's history
}
add_shortcode('tracker_history', 'tracker_student_history');
```
This creates a shortcode `[tracker_history]` that students can use to see their own data.

## WordPress Concepts Used

### 1. Hooks
- `register_activation_hook()` - Runs when plugin is activated
- `add_action()` - Adds function to WordPress events
- `add_shortcode()` - Creates a shortcode

### 2. Database ($wpdb)
- `$wpdb->insert()` - Adds data to table
- `$wpdb->get_results()` - Gets data from table
- `$wpdb->prepare()` - Makes queries safe (prevents SQL injection)

### 3. WordPress Functions
- `is_user_logged_in()` - Checks if user is logged in
- `get_current_user_id()` - Gets the user ID
- `admin_url()` - Gets admin URL
- `current_time()` - Gets current date/time
- `sanitize_text_field()` - Cleans user input

## Technologies Used

1. **PHP** - Server-side language
2. **JavaScript** - Client-side for timing
3. **MySQL** - Database to store data
4. **WordPress API** - Built-in WordPress functions

## What Makes This "Beginner Level"

1. ✅ Everything in ONE file (no complex file structure)
2. ✅ Basic HTML tables (no fancy CSS)
3. ✅ Simple JavaScript (just timer and sendBeacon)
4. ✅ No AJAX (uses simpler sendBeacon instead)
5. ✅ No classes/OOP (just functions)
6. ✅ Basic features only (just time tracking)
7. ✅ Simple database (one table, 5 columns)
8. ✅ Lots of comments

## Questions Your Teacher Might Ask

**Q: How does the timer work?**
A: JavaScript records the start time when page loads, then calculates the difference when the user leaves.

**Q: Where is the data stored?**
A: In a WordPress database table called `wp_tracker_data`.

**Q: What happens when someone is not logged in?**
A: The tracking doesn't work. We check `is_user_logged_in()` first.

**Q: What is sendBeacon?**
A: It's a JavaScript method that reliably sends data even when the page is closing.

**Q: What does sanitize_text_field do?**
A: It cleans user input to prevent security issues like SQL injection.

**Q: Could you add more features?**
A: Yes! We could add:
- Idle detection (stop counting when user is inactive)
- Quiz tracking
- Export to CSV
- Charts and graphs

## How to Explain This to Your Teacher

"I created a simple WordPress plugin that tracks student activity. When a student visits a page, JavaScript starts a timer. When they leave, the time is sent to the server and saved in a database table. Teachers can view all the data in the admin area, and students can see their own history using a shortcode."

## Key Learning Points

1. How WordPress plugins work
2. How to create database tables
3. How to use JavaScript with PHP
4. How to create admin pages
5. How to use shortcodes
6. Basic security practices

This is a realistic beginner project that shows understanding of WordPress basics without being too advanced.
