# WP-Plugin-DriveForm

in order to run the plugin u need to create a credentials.json like:

{
  "type": "service_account",
  "project_id": "",
  "private_key_id": "",
  "private_key": "",
  "client_email": "",
  "client_id": "",
  "auth_uri": "",
  "token_uri": "",
  "auth_provider_x509_cert_url": "",
  "client_x509_cert_url": ""
}

can be found in the google cloud console, in the IAM&Admin section -> Service Accounts-> keys tab

<=========   In order to change the functionality with hooks    ========>
there are hook filters named: valid-form, setdirectoryid, setfilename, modify-data

and hook actions named: drive_form, data-to-drive-sheet

in order to see functionality see the source code in functions.php (for now)
