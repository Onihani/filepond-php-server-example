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

  if ($_SERVER['REQUEST_METHOD'] === "DELETE") {

    $uniqueFileID = (int) $_GET["key"];

    function revertImagesFromUploadsLocation () {

      global $uniqueFileID;

      $imgName = null;

      // check if there is a filename in the DB with key and campaignId
      $arrayDBStore = readJsonFile();

      $imageInfoIndex = array_search($uniqueFileID, array_column($arrayDBStore, 'id'));

      if (isset($imageInfoIndex)) {

        $imageInfo = $arrayDBStore[$imageInfoIndex];
        $imgName = $imageInfo["name"];

      }

      // check if there is file ($imgName) in ./images/ path on the server
      $imgFilePointer = UPLOAD_DIR . $imgName;
      // if file exists --> delete file from server
      if (file_exists($imgFilePointer)) {

        $filedeleted = unlink($imgFilePointer);

        if ($filedeleted) {

          // removing file from DB as well
          unset($arrayDBStore[$imageInfoIndex]);
          writeJsonFile ($arrayDBStore);

        }

        return $filedeleted;

      } else {

        return true;

      }

    }


    $response = [];
    // trigger revertFunction
    if (revertImagesFromUploadsLocation()) {

      $response["status"] = "success";
      $response["key"] = $uniqueFileID;

      http_response_code(200);

    } else {

      $response["status"] = "error";
      $response["msg"] = "File could not be deleted";

      http_response_code(400);

    }

    header('Content-Type: application/json');
    echo json_encode($response);

    exit();
  } else {
    exit();
  }

?>