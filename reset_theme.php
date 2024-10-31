<?php
/*
    Delete Themes and Activate Default Theme code file
*/
echo "<br><h4><strong>Delete Themes and Activate Default Theme</strong></h4>";

// Check if form is submitted
if (isset($_POST['delete_themes'])) {
    // Get the list of installed themes
    $themes = wp_get_themes();
    
    // Delete each theme except the default theme
    foreach ($themes as $theme) {
        if ($theme->get_stylesheet() !== get_option('stylesheet')) {
            delete_theme($theme->get_stylesheet());
        }
    }
    
    // Activate the default theme
    switch_theme(get_option('template'), get_option('stylesheet'));
    
    echo '<div class="notice notice-success is-dismissible"><p>All themes except the default theme have been deleted, and the default theme has been activated.</p></div>';
}

// Output the delete themes button
echo '<form method="post" action="">';
echo '<input type="hidden" name="delete_themes" value="true">';
echo '<input type="submit" class="button button-primary theme" value="Delete Themes and Activate Default Theme">';
echo '</form>';
?>