<?php 
/* 
* Single Table Reset Database 
*
*/
echo "<h4><strong>Select the database table(s) to reset:</strong></h4>";
global $wpdb;
$sql = "SHOW TABLES LIKE '%'";
$results = $wpdb->get_results($sql);

// Check if the reset button was clicked
if (isset($_POST['list_tb']) && $_POST['list_tb'] == 'reset') {
    if (isset($_POST['tbname'])) {
        $selectedTables = $_POST['tbname']; // Get the selected tables
        
        $confirmation_word = $_POST['wordpress_reset_confirm']; // Get the confirmation word from the form
        
        if ($confirmation_word === 'reset') { // Check if the confirmation word is 'reset'
            foreach ($selectedTables as $table) {
                $wpdb->query("TRUNCATE TABLE $table");
            }
            
            global $current_user;
            $wordpress_reset = (isset($_POST['wordpress_reset']) && 'reset' == $_POST['wordpress_reset']);
            $wordpress_reset_confirm = (isset($_POST['wordpress_reset_confirm']) && 'reset' == $_POST['wordpress_reset_confirm']);
            $valid_nonce = (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wordpress_reset'));
            
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            $blogname = get_option('blogname');
            $admin_email = get_option('admin_email');
            $admin_password = get_option('admin_email');
            $blog_public = get_option('blog_public');
            
            if ('admin' !== $current_user->user_login) {
                $user = get_user_by('login', 'admin');
            }
            
            if (empty($user->user_level) || $user->user_level < 10) {
                $user = $current_user;
            }
            
            $result = wp_install($blogname, $user->user_login, $user->user_email, $blog_public);
            extract($result, EXTR_SKIP);
            
            $query = $wpdb->prepare("UPDATE $wpdb->users SET user_pass = %s, user_activation_key = '' WHERE ID = %d", $user->user_pass, $user_id);
            $wpdb->query($query);
            
            $get_user_meta = function_exists('get_user_meta') ? 'get_user_meta' : 'get_usermeta';
            $update_user_meta = function_exists('update_user_meta') ? 'update_user_meta' : 'update_usermeta';
            
            // Show a success message
            echo '<div class="notice notice-success is-dismissible"><p>The selected tables have been reset.</p></div>';
        } else {
            // Show an alert message if the confirmation word is not 'reset'
            echo '<div class="notice notice-error is-dismissible"><p>Invalid confirmation word. Please type the word "reset" in the confirmation field.</p></div>';
        }
    } else {
        // Show an alert message if 'tbname' is not set in the form
        echo '<div class="notice notice-error is-dismissible"><p>No tables were selected.</p></div>';
    }
}
?>


<div class="wrap">
    <form method="post">
        <table class="form-table">
            <tbody>
                <?php foreach ($results as $index => $value) {
                    foreach ($value as $tableName) { ?>
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" name="tbname[]" value="<?php echo $tableName; ?>">
                            <?php echo $tableName; ?>
                        </label>
                    </td>
                </tr>
                <?php }
                } ?>
            </tbody>
        </table>
        <div>
            <p style="font-weight: 600;">Type "reset" in the confirmation field to confirm the reset and then click the
                Reset Database button.</p>
        </div>

        <input id="wordpress_reset_confirm" type="text" name="wordpress_reset_confirm" value="">
        <!-- Added the confirmation field -->
        <input type="hidden" name="list_tb" value="reset"> <!-- Updated the input field to be hidden -->
        <div>
            <button type="submit" class="button button-primary table">Reset Selected Tables</button>
        </div>
    </form>
</div>

<script>
    function myfunction() {
        var confirmation = document.getElementById("wordpress_reset_confirm").value;
        if (confirmation !== 'reset') {
            return false; // Prevent form submission if the confirmation word is not 'reset'
        }
    }
</script>