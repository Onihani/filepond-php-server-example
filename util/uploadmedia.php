<?php

  function uploadImage($file, $fileDestination = "./images/") {
    $fileName = $file['name'];
    $fileType = $file['type'];
    $fileTempName = $file['tmp_name'];
    $fileError = $file['error'];
    $fileSize = $file['size'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowedExts = array('jpg', 'jpeg', 'png', 'svg', 'gif');

    if (in_array($fileActualExt, $allowedExts)) {
      if ($fileError === 0) {
        if ($fileSize < 2000000) {
          $fileNewName = uniqid("", true).".".$fileActualExt;
          $fileDestination = $fileDestination . $fileNewName;
          move_uploaded_file($fileTempName, $fileDestination);

          return $fileNewName;
        } else {
          return false; // error: file size too big
        }
      } else {
        return false; // error: error uploading file
      }
    } else {
      return false; // error: file ext not allowed
    }
  }

?>