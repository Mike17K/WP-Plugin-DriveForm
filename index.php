<?php
/*
Plugin Name: Drive Form Bridge
Plugin URI:  https://example.com/my-plugin
Description: This is my first WordPress plugin.
Version:     1.0
Author:      Mike Kaipis
Author URI:  https://example.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: my-plugin
*/

include 'functions.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '\\wordpress\\wp-load.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // check valid data
    $isValid = apply_filters('valid-form', $_POST);

    if ($isValid) {
        // google api comunication here
        $driveDirectoryId = apply_filters('setdirectoryid', $_POST);
        $fileName = apply_filters('setfilename', $_POST);

        $data = apply_filters('modify-data', $_POST);
        do_action('data-to-drive-sheet', $data, $fileName, $driveDirectoryId);

    }
}

do_action('drive_form');


?>