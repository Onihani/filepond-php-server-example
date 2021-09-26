<?php

  function readJsonFile ($filePath = DATABASE_FILE) {
    $file_content = file_get_contents($filePath);
    $json = json_decode($file_content, true);
    return $json;
  }

  function writeJsonFile ($file_content, $filePath = DATABASE_FILE) {
    $fp = fopen($filePath, 'w');
    fwrite($fp, json_encode($file_content));
    fclose($fp);
  }

?>
