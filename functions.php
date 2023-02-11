<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '\\wordpress\\wp-load.php');
require_once 'vendor/autoload.php';

function check_valid_form($post)
{
    echo "checking data ... \n ";
    return true; // true/false based on data
}
add_filter('valid-form', 'check_valid_form', 10, 1);

function createForm()
{
    ?>
<h1>Client Form</h1>

<form action="index.php" method="post">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name"><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password"><br><br>

    <input type="submit" value="Submit">
</form>

<?php
}
add_action('drive_form', 'createForm');

function mysetdirectoryid($post)
{
    return '13Q5GtwxHTua6qQgQaLi0N0W33yLR13WW'; // here i define my folder id in my drive
}
add_action('setdirectoryid', 'mysetdirectoryid', 10, 1);

function mysetfilename($post)
{
    return $post['email'];
}
add_action('setfilename', 'mysetfilename', 10, 1);


function mymodifydata($data)
{
    return $data;
}
add_action('modify-data', 'mymodifydata', 10, 1);

function googleApi($post, $fileName, $driveDirectoryId)
{
    putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials.json');

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope(Google_Service_Drive::DRIVE);

    $driveService = new Google_Service_Drive($client);

    $fileMetadata = new Google_Service_Drive_DriveFile(
        array(
            'name' => $fileName,
            'parents' => array($driveDirectoryId),
            'mimeType' => 'application/vnd.google-apps.spreadsheet'
        )
    );
    $file = $driveService->files->create(
        $fileMetadata,
        array(
            'fields' => 'id'
        )
    );

    return $file->id;
}

add_action('data-to-drive-sheet', 'googleApi', 10, 3);

?>