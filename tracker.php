<?php
/**
 * Plugin Name: Tracker
 * Description: Simple plugin to track student time on pages
 * Version: 1.0
 * Author: Fahim
 */

// Stop if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Create database table when plugin is activated
function tracker_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tracker_data';
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        user_id INT NOT NULL,
        page_title VARCHAR(255) NOT NULL,
        time_spent INT NOT NULL,
        visit_date DATETIME NOT NULL,
        PRIMARY KEY (id)
    )";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'tracker_install');

// Add JavaScript to track time
function tracker_add_script() {
    if (is_user_logged_in()) {
        ?>
        <script>
        var startTime = new Date().getTime();
        var pageTitle = document.title;
        var userId = <?php echo get_current_user_id(); ?>;
        
        // Save time when user leaves page
        window.addEventListener('beforeunload', function() {
            var endTime = new Date().getTime();
            var timeSpent = Math.floor((endTime - startTime) / 1000); // seconds
            
            // Send data to server
            var data = new FormData();
            data.append('action', 'save_tracker_time');
            data.append('user_id', userId);
            data.append('page_title', pageTitle);
            data.append('time_spent', timeSpent);
            
            navigator.sendBeacon('<?php echo admin_url('admin-ajax.php'); ?>', data);
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'tracker_add_script');

// Save time data to database
function tracker_save_time() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tracker_data';
    
    $user_id = intval($_POST['user_id']);
    $page_title = sanitize_text_field($_POST['page_title']);
    $time_spent = intval($_POST['time_spent']);
    
    $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'page_title' => $page_title,
            'time_spent' => $time_spent,
            'visit_date' => current_time('mysql')
        )
    );
    
    wp_die();
}
add_action('wp_ajax_save_tracker_time', 'tracker_save_time');

// Add admin menu
function tracker_admin_menu() {
    add_menu_page(
        'Tracker',
        'Tracker',
        'manage_options',
        'tracker',
        'tracker_admin_page',
        'dashicons-chart-line'
    );
}
add_action('admin_menu', 'tracker_admin_menu');

// Admin page to show data
function tracker_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tracker_data';
    
    // Get all tracking data
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY visit_date DESC LIMIT 50");
    
    echo '<div class="wrap">';
    echo '<h1>Student Activity Tracker</h1>';
    
    if (empty($results)) {
        echo '<p>No data yet. Students need to visit pages while logged in.</p>';
    } else {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>Student ID</th>';
        echo '<th>Page Title</th>';
        echo '<th>Time Spent</th>';
        echo '<th>Date</th>';
        echo '</tr></thead>';
        echo '<tbody>';
        
        foreach ($results as $row) {
            $minutes = floor($row->time_spent / 60);
            $seconds = $row->time_spent % 60;
            $time_display = $minutes . 'm ' . $seconds . 's';
            
            echo '<tr>';
            echo '<td>' . esc_html($row->user_id) . '</td>';
            echo '<td>' . esc_html($row->page_title) . '</td>';
            echo '<td>' . esc_html($time_display) . '</td>';
            echo '<td>' . esc_html($row->visit_date) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
    }
    
    echo '</div>';
}

// Shortcode to display student's own history
function tracker_student_history() {
    if (!is_user_logged_in()) {
        return '<p>Please log in to view your history.</p>';
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'tracker_data';
    $user_id = get_current_user_id();
    
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d ORDER BY visit_date DESC LIMIT 20",
        $user_id
    ));
    
    if (empty($results)) {
        return '<p>No activity history yet.</p>';
    }
    
    $output = '<div class="tracker-history">';
    $output .= '<h3>My Activity History</h3>';
    $output .= '<table border="1" style="width:100%; border-collapse:collapse;">';
    $output .= '<tr><th>Page</th><th>Time Spent</th><th>Date</th></tr>';
    
    foreach ($results as $row) {
        $minutes = floor($row->time_spent / 60);
        $seconds = $row->time_spent % 60;
        $time_display = $minutes . 'm ' . $seconds . 's';
        
        $output .= '<tr>';
        $output .= '<td>' . esc_html($row->page_title) . '</td>';
        $output .= '<td>' . esc_html($time_display) . '</td>';
        $output .= '<td>' . esc_html(date('M j, Y g:i A', strtotime($row->visit_date))) . '</td>';
        $output .= '</tr>';
    }
    
    $output .= '</table></div>';
    
    return $output;
}
add_shortcode('tracker_history', 'tracker_student_history');
