<?php
/*
    Deactivate & Delete code file
*/
echo "<br><h4><strong>Select The Plugin(s) To Deactivate & Delete</strong></h4>";
// Get a list of installed plugins
$plugins = get_plugins();

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get the selected plugins from the form submission
    $selected_plugins = isset($_POST['plugin_select']) ? $_POST['plugin_select'] : array();

    // Check if any plugins are selected
    if (empty($selected_plugins)) {
        echo '<div class="notice notice-error is-dismissible"><p>No plugins are selected. Please select at least one plugin to deactivate and delete.</p></div>';
    } else {
        // Perform the corresponding action based on the selected option
        if ($_POST['action'] === 'reset') {
            $confirmation_word = sanitize_text_field($_POST['wordpress_reset_confirm']); // Get the confirmation word from the form

            if ($confirmation_word === 'reset') {
                foreach ($selected_plugins as $plugin_path) {
                    $plugin_file = plugin_basename($plugin_path);
                    
                    // Get the plugin's information
                    $plugin_info = get_plugin_data($plugin_path);

                    // Skip the "Reset Database" plugin
                    if ($plugin_info['Name'] === 'Reset Database') {
                        continue;
                    }

                    deactivate_plugins($plugin_file);
                    delete_plugins(array($plugin_file)); // Delete the plugin from the website
                }
                echo '<div class="notice notice-success is-dismissible"><p>The selected plugins have been deactivated and deleted successfully.</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>Invalid confirmation word. Please type the word "reset" in the confirmation field.</p></div>';
            }
        }
    }
}

// Output the checkboxes and button
echo '<form method="post" action="">';
foreach ($plugins as $plugin_path => $plugin_info) {
    $plugin_name = $plugin_info['Name'];

    // Skip the "Reset Database" plugin
    if ($plugin_info['Name'] === 'Reset') {
        continue;
    }

    echo '<label>';
    echo '<input type="checkbox" name="plugin_select[]" value="' . esc_attr($plugin_path) . '"> ';
    echo esc_html($plugin_name);
    echo '</label>';
    echo '<span class="error-message" id="error_' . esc_attr($plugin_path) . '"></span><br>'; // Add a span element for displaying error messages
}

// Output the deactivate button
echo '<div>';
echo '<p style="font-weight: 600;">Type "reset" in the confirmation field to confirm deactivation and deletion.</p>';
echo '<input id="wordpress_reset_confirm" type="text" name="wordpress_reset_confirm" value=""> <!-- Added the confirmation field -->';
echo '<input type="hidden" name="action" value="reset"> <!-- Updated the input field name -->';
echo '<div>';
echo '<button type="submit" name="submit" class="button button-primary" style="margin-top:20px;">Deactivate and Delete</button>';
echo '</div>';
echo '</div>';
echo '</form>';
?>