<?php
if (is_file('.htaccess')) {
    echo "<pre>";
    echo htmlspecialchars(file_get_contents('.htaccess'));
    echo "</pre>";
} else {
    echo ".htaccess not found";
}
?>
