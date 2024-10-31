<?php
/*
    Delete All Comments code file
*/
echo "<br><h4><strong>Delete All Comments</strong></h4>";



// Check if form is submitted
if (isset($_POST['delete_all_comments'])) {
    // Get all comments
    $comments = get_comments(array(
        'status' => 'all', // Include all comments (approved, pending, spam, trash)
        'number' => 0, // Retrieve all comments
    ));
    
    // Delete each comment
    foreach ($comments as $comment) {
        wp_delete_comment($comment->comment_ID, true); // Set second parameter to true for permanently deleting comments
    }
    
    echo '<div class="notice notice-success is-dismissible"><p>All comments have been deleted successfully.</p></div>';
}
// Output the delete comments  button
echo '<form method="post" action="">';
echo '<input type="hidden" name="delete_all_comments" value="true">';
echo '<input type="submit" class="button button-primary comments" value="Delete All Comments">';
echo '</form>';

?>