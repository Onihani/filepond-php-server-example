<?php

  // Comment if you don't want to allow posts from other domains
  header('Access-Control-Allow-Origin: *');

  // Allow the following methods to access this file
  header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');

  // Allow the following headers in preflight
  header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');

  // Allow the following headers in response
  header('Access-Control-Expose-Headers: upload-offset');

  // Load our configuration for this server
  require_once('config.php');
  require("./util/uploadmedia.php");
  require("./util/read_write_functions.php");

  // writeJsonFile(array());
  // $arrayDBStore = readJsonFile();
  // // echo 123;
  // echo gettype($arrayDBStore);
  // print_r($arrayDBStore);
  // print_r(array());


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $files = $_FILES["filepond"];
    $imageName = null;
    $id = null;

    function saveImagesToTempLocation ($uploadedFile) {

      global $imageName;
      global $id;

      $imageUniqueId = null;

      // check that there were no errors while uploading file 
      if (isset($uploadedFile) && $uploadedFile['error'] === UPLOAD_ERR_OK) {

        $imageName = uploadImage($uploadedFile, UPLOAD_DIR);

        if ($imageName) {

          $filePointer = UPLOAD_DIR . $imageName;
          $arrayDBStore = readJsonFile();
          $id = uniqid();

          $newImageInfo = [
            "id" => $id,
            "name" => $imageName,
            "date" => time()
          ];

          array_push($arrayDBStore , $newImageInfo);

          writeJsonFile($arrayDBStore);

        }

      }

      return $id;

    }

    $structuredFiles = [];
    if (isset($files)) {
      foreach($files["name"] as $filename) {
        $structuredFiles[] = [
          "name" => $filename
        ];
      }

      foreach($files["type"] as $index => $filetype) {
        $structuredFiles[$index]["type"] = $filetype;
      }

      foreach($files["tmp_name"] as $index => $file_tmp_name) {
        $structuredFiles[$index]["tmp_name"] = $file_tmp_name;
      }

      foreach($files["error"] as $index => $file_error) {
        $structuredFiles[$index]["error"] = $file_error;
      }

      foreach($files["size"] as $index => $file_size) {
        $structuredFiles[$index]["size"] = $file_size;
      }
    }

    $uniqueImgID = null;
    if (count($structuredFiles)) {
      foreach ($structuredFiles as $structuredFile) {
        $uniqueImgID = saveImagesToTempLocation($structuredFile);
      }
    }

    $response = [];
    if ($uniqueImgID) {

      $response["status"] = "success";
      $response["key"] = $uniqueImgID;
      $response["msg"] = null;
      $response["files"] = json_encode($structuredFiles);

      http_response_code(200);

    } else {

      $response["status"] = "error";
      $response["key"] = null;
      $response["msg"] = "An error occured while uploading image";
      $response["files"] = json_encode($structuredFiles);

      http_response_code(400);

    }

    header('Content-Type: application/json');
    echo json_encode($response);

    exit();

  } else {

    exit();

  }

  

?>