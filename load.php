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
  
  if ($_SERVER['REQUEST_METHOD'] === "GET") {

    function getRequestHeaders() {
      $headers = array();
      foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'HTTP_') {
          continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
      }
      return $headers;
    }
  
    // $headers = getRequestHeaders();

    // header('Content-Type: application/json');
    // echo json_encode(["headers" =>$headers, "get" => $_GET]);
    // exit();

    $uniqueFileID = (int) $_GET["key"];

    function loadLocalImage () {

      global $uniqueFileID;

      $imageName = null;

      // checking if image exists in db with uniqueFileID
      $arrayDBStore = readJsonFile();

      $imageInfoIndex = array_search($uniqueFileID, array_column($arrayDBStore, 'id'));

      if (isset($imageInfoIndex)) {

        $imageInfo = $arrayDBStore[$imageInfoIndex];
        $imageName = $imageInfo["name"];

      }

      // if imageName was found in the DB, get file with imageName and return file object or blob
      $imagePointer = UPLOAD_DIR . $imageName;
      $fileObject = null;
      if ($imageName && file_exists($imagePointer)) {

        $fileObject = file_get_contents($imagePointer);

      }

      return [$fileObject, $imageName];

    }


    // trigger load local image
    $loadImageResultArr = [$fileBlob, $imageName] = loadLocalImage();

    if ($fileBlob) {
      $imagePointer = UPLOAD_DIR . $imageName;
      $fileContextType = mime_content_type($imagePointer);
      $fileSize = filesize($imagePointer);

      // $handle = fopen($imagePointer, 'r');
      // if (!$handle) return false;
      // $content = fread($handle, filesize($imagePointer));

      http_response_code(200);
      header('Access-Control-Expose-Headers: Content-Disposition, Content-Length, X-Content-Transfer-Id');
      header("Content-Type: $fileContextType");
      header("Content-Length: $fileSize");
      header("Content-Disposition: inline; filename='$imageName'");
      echo $fileBlob;
      // echo json_encode(strlen($fileBlob));
    } else {
      http_response_code(500);
    }

    exit();

  } else {

    http_response_code(400);
    exit();

  }

?>