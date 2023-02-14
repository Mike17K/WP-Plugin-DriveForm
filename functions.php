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


function getFileId($fileId)
{

}
add_action('get-file-id', 'getFileId', 10, 3);

function doesFileExist($driveService, $fileName, $driveDirectoryId, $post)
{
    // Check if the file already exists
    $results = $driveService->files->listFiles(
        array(
            'q' => "name='" . $fileName . "' and trashed = false",
            'fields' => 'nextPageToken, files(id, name)'
        )
    );
    return $results;
}
add_filter('check-file-exist', 'doesFileExist', 10, 4);

function myAddDataToSheet($sheetService, $fileId, $data) /////////////////////////// fix add data
{
    print_r($data);

    $data_keys=[];
    $data_values=[];
    foreach($data as $key => $val) {
        array_push($data_keys,$key);
        array_push($data_values,$val);
    }

    $valueRange = new Google_Service_Sheets_ValueRange();
    $valueRange->setValues(
        [
            $data_keys,
            $data_values
        ]
    );
    $options = array(
        "valueInputOption" => "RAW"
    );

    $sheetService->spreadsheets_values->update(
        $fileId,
        "A1:C2",
        $valueRange,
        $options
    );
}
add_action('add-data-to-sheet', 'myAddDataToSheet', 10, 3);

function googleApi($post, $fileName, $driveDirectoryId)
{
    putenv("GOOGLE_APPLICATION_CREDENTIALS=C:\Users\\User\Documents\api-google-drive\credentials.json");

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->addScope(Google_Service_Sheets::SPREADSHEETS);

    $driveService = new Google_Service_Drive($client);
    $sheetService = new Google_Service_Sheets($client);

    $fileMetadata = new Google_Service_Drive_DriveFile(
        array(
            'name' => $fileName,
            'parents' => array($driveDirectoryId),
            'mimeType' => 'application/vnd.google-apps.spreadsheet'
        )
    );

    $results = apply_filters('check-file-exist', $driveService, $fileName, $driveDirectoryId, $post);

    if (count($results->getFiles()) == 0) {
        // Create a new file
        $file = $driveService->files->create(
            $fileMetadata,
            array(
                'fields' => 'id'
            )
        );

        $fileId = $file->id;
    } else {
        $fileId = $results->getFiles()[0]->id;
        echo 'file exists';
    }

    // Add data 
    do_action('add-data-to-sheet', $sheetService, $fileId, $post);

}

add_action('data-to-drive-sheet', 'googleApi', 10, 3);


?>