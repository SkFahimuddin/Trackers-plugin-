# Tracker - Simple Student Activity Plugin

A basic WordPress plugin that tracks how long students spend on pages.

## What It Does

- Tracks time students spend on each page
- Saves data to database
- Shows tracking data in admin dashboard
- Students can view their own history

## Installation

1. Upload the `tracker` folder to `/wp-content/plugins/`
2. Activate the plugin in WordPress admin
3. Go to "Tracker" menu to see data

## How to Use

### For Students
Add this shortcode to any page to show history:
```
[tracker_history]
```

### For Teachers
Go to WordPress Admin → Tracker to see all student activity.

## How It Works

1. **JavaScript Timer**: When a student visits a page, JavaScript starts counting time
2. **Save on Exit**: When they leave the page, the time is saved to the database
3. **View Data**: Teachers can see all data in the admin dashboard

## Files

- `tracker.php` - Main plugin file (everything is in one file for simplicity)
- `README.md` - This file

## Database

Creates one table: `wp_tracker_data`

Table structure:
- id (auto increment)
- user_id (which student)
- page_title (which page they visited)
- time_spent (in seconds)
- visit_date (when they visited)

## Code Explanation

### 1. Database Creation
```php
function tracker_install()
```
Creates the database table when plugin is activated.

### 2. JavaScript Timer
```php
function tracker_add_script()
```
Adds JavaScript to every page that counts time.

### 3. Save Data
```php
function tracker_save_time()
```
Receives data from JavaScript and saves to database.

### 4. Admin Page
```php
function tracker_admin_page()
```
Shows all tracking data in a table.

### 5. Student History
```php
function tracker_student_history()
```
Shortcode that displays student's own history.

## Requirements

- WordPress 5.0+
- PHP 7.0+
- Students must be logged in for tracking to work

## Limitations

- Only tracks time when user is on page (doesn't detect if they're idle)
- Tracks page visits only (no quizzes or assignments)
- Simple design (no charts or graphs)

## Future Ideas

- Add idle detection
- Track quiz scores
- Export data to CSV
- Add charts and graphs
- Email reports

## Author

Created by Fahim for educational purposes.
