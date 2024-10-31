<?php
/**
 * Plugin Name: Reset
 * Plugin URI: https://wordpress.org/plugins/reset
 * Description: Reset the WordPress database to its default state without losing its connection.
 * Version: 1.5
 * Author: Smartzminds
 * Author URI: https://profiles.wordpress.org/shubhamgrover7256
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
// Add a menu item to the dashboard


add_action('admin_menu', 'reset_db_add_menu_item');
function reset_db_add_menu_item() {
  add_menu_page('Reset', 'Reset', 'activate_plugins', 'reset_db', 'reset_db_page');
}

function my_plugin_enqueue_styles() {
  wp_enqueue_style( 'data_reset', plugin_dir_url( __FILE__ ) . '/style.css' );
}
add_action( 'admin_enqueue_scripts', 'my_plugin_enqueue_styles' );

// Create reset page
function reset_db_page() {
  // Check if the user has permission to reset the database
  if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }

  // Check if the reset button was clicked
  if (isset($_POST['reset_db']) && $_POST['reset_db'] == 'true') {
    
    $confirmation_word = $_POST['wordpress_reset_confirm'];
    if ($confirmation_word === 'reset') { // Check if the confirmation word is 'reset'
    // Reset the database to its default state
    
    global $wpdb;
    $tables = $wpdb->get_col("SHOW TABLES");
    foreach ($tables as $table) {
      $wpdb->query(" DROP TABLE IF EXISTS $table");
    }
    
    global $current_user;

    $wordpress_reset         = ( isset( $_POST['wordpress_reset'] ) && 'true' == $_POST['wordpress_reset'] );
    $wordpress_reset_confirm = ( isset( $_POST['wordpress_reset_confirm'] ) && 'reset' == $_POST['wordpress_reset_confirm'] );
    $valid_nonce             = ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wordpress_reset' ) );

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $blogname    = get_option( 'blogname' );
    $admin_email = get_option( 'admin_email' );
    $admin_password = get_option( 'admin_email' );
    $blog_public = get_option( 'blog_public' );

    if ( 'admin' !== $current_user->user_login ) {
      $user = get_user_by( 'login', 'admin' );
    }

    if ( empty( $user->user_level ) || $user->user_level < 10 ) {
      $user = $current_user;
    }

    $result = wp_install( $blogname, $user->user_login, $user->user_email, $blog_public );
    extract( $result, EXTR_SKIP );

    $query = $wpdb->prepare( "UPDATE $wpdb->users SET user_pass = %s, user_activation_key = '' WHERE ID = %d", $user->user_pass, $user_id );
    $wpdb->query( $query );

    $get_user_meta    = function_exists( 'get_user_meta' ) ? 'get_user_meta' : 'get_usermeta';
    $update_user_meta = function_exists( 'update_user_meta' ) ? 'update_user_meta' : 'update_usermeta';
  }  
  else 
  {
    // Show an alert message if the confirmation word is not 'reset'
    // echo '<div class="notice notice-error is-dismissible"><p>Invalid confirmation word. Please type the word "reset" in the confirmation field.</p></div>';
  }
  }  
  ?>
<div>
  <h1>RESET PLUGIN</h1>
  <p class="description" style="font-weight:500;">Reset the WordPress database to its default state without losing its
    connection.</p>
</div>
<div class="tab-container">
  <div class="tab-header">
    <a href="<?php echo esc_url( add_query_arg( 'page', 'reset_db', admin_url( 'admin.php' ) ) ); ?>&tab=website-database-reset"
      class="tab-button <?php if((!isset($_GET['tab'])) || (isset($_GET['tab']) && $_GET['tab'] == 'website-database-reset')){ echo 'active'; }?>">Website
      Database Reset</a>
    <a href="<?php echo esc_url( add_query_arg( 'page', 'reset_db', admin_url( 'admin.php' ) ) ); ?>&tab=specific-table-reset"
      class="tab-button <?php if(isset($_GET['tab']) && $_GET['tab'] == 'specific-table-reset'){ echo 'active'; }?>">Specific
      Table Reset</a>
    <a href="<?php echo esc_url( add_query_arg( 'page', 'reset_db', admin_url( 'admin.php' ) ) ); ?>&tab=plugin-deactivate-delete"
      class="tab-button <?php if(isset($_GET['tab']) && $_GET['tab'] == 'plugin-deactivate-delete'){ echo 'active'; }?>">Plugin
      Deactivate & Delete</a>
    <a href="<?php echo esc_url( add_query_arg( 'page', 'reset_db', admin_url( 'admin.php' ) ) ); ?>&tab=delete-themes-and-deactivate-all-themes"
      class="tab-button <?php if(isset($_GET['tab']) && $_GET['tab'] == 'delete-themes-and-deactivate-all-themes'){ echo 'active'; }?>">Delete
      Themes and Deactivate All Themes</a>
    <a href="<?php echo esc_url( add_query_arg( 'page', 'reset_db', admin_url( 'admin.php' ) ) ); ?>&tab=delete-all-comments"
      class="tab-button <?php if(isset($_GET['tab']) && $_GET['tab'] == 'delete-all-comments'){ echo 'active'; }?>">Delete
      All Comments</a>
  </div>
  <div class="tab-content">
    <div
      class="tab-pane <?php if((!isset($_GET['tab'])) || (isset($_GET['tab']) && $_GET['tab'] == 'website-database-reset')){ echo 'active'; }?>">
      <form id="wordpress_reset_form" action="" method="post">
        <h1>Reset Database</h1>
        <?php
          if (isset($_POST['reset_db']) && $_POST['reset_db'] == 'true') {
            $confirmation_word = $_POST['wordpress_reset_confirm'];
            if ($confirmation_word === 'reset') { // Check if the confirmation word is 'reset'
              echo '<div class="notice notice-success is-dismissible"><p>The database has been reset to its default state.</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>Invalid confirmation word. Please type the word "reset" in the confirmation field.</p></div>';
              }
            }
          ?>
        <p style="font-weight:600;">Type "reset" in the confirmation field to confirm the reset and then click the reset
          database button.</p>
        <?php wp_nonce_field( 'wordpress_reset' ); ?>
        <input id="wordpress_reset" type="hidden" name="wordpress_reset" value="true" />
        <input id="wordpress_reset_confirm" type="text" name="wordpress_reset_confirm" value="" />
        <p>Are you sure you want to reset the database to its default state? This will delete all content and settings.
        </p>
        <input type="hidden" name="reset_db" value="true">
        <input type="submit" id="wordpress_reset_submit" class="button button-primary" value="Reset Database">
      </form>
      <div id="reset_error_message"></div>
    </div>
    <div class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab'] == 'specific-table-reset'){ echo 'active'; }?>">
      <?php include( plugin_dir_path( __FILE__ ) . '/table_data.php' ); ?>
    </div>
    <div
      class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab'] == 'plugin-deactivate-delete'){ echo 'active'; }?>">
      <?php include( plugin_dir_path( __FILE__ ) . '/plugin_reset.php' ); ?>
    </div>
    <div
      class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab'] == 'delete-themes-and-deactivate-all-themes'){ echo 'active'; }?>">
      <?php include( plugin_dir_path( __FILE__ ) . '/reset_theme.php' ); ?>
    </div>
    <div class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab'] == 'delete-all-comments'){ echo 'active'; }?>">
      <?php include( plugin_dir_path( __FILE__ ) . '/delete_comments.php' ); ?>
    </div>
  </div>
</div>
<?php 
}


// Add "Rate this plugin" link with 5-star rating
function reset_plugin_action_links($links) {
  $plugin_links = array(
      '<a href="' . admin_url('admin.php?page=reset_db') . '">Settings</a>',
      '<a class="rating-review" href="https://wordpress.org/support/plugin/reset/reviews/" target="_blank">Rate and Review
      <span class="rating-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span></a>',
  );
  return array_merge($plugin_links, $links);
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'reset_plugin_action_links');


// add notification on welcome screen
function reset_plugin_show_rating_notification() {
  if (get_option('reset_plugin_rated')) {
      return;
  }

  ob_start();
  ?>
  <div class="notice notice-info is-dismissible">
      <p><?php _e('Enjoying Reset Plugin? Please consider leaving a 5-star rating!', 'reset-plugin-text-domain'); ?></p>
      <p>
          <a href="https://wordpress.org/support/plugin/reset/reviews/?filter=5/#new-post" target="_blank" class="button button-primary">
              <?php _e('Rate and Review &#9733;&#9733;&#9733;&#9733;&#9733;', 'reset-plugin-text-domain'); ?>
          </a>
          <a href="?reset_plugin_dismiss_rating=1" class="button button-secondary">
              <?php _e('Dismiss', 'reset-plugin-text-domain'); ?>
          </a>
      </p>
  </div>
  <?php

  $notification_html = ob_get_clean();

  echo $notification_html;
}

// Dismiss the rating notification
if (isset($_GET['reset_plugin_dismiss_rating'])) {
  update_option('reset_plugin_rated', 1);
}
// Hook the function to welcome_panel
#add_action('welcome_panel', 'reset_plugin_show_rating_notification');

// Add rating option in plugin setting page 
// Add notification to plugin settings page
function reset_plugin_settings_page_notification() {
  reset_plugin_show_rating_notification();
}

// Hook the function to plugin settings page
add_action('admin_notices', 'reset_plugin_settings_page_notification');