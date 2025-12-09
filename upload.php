<?php
$targetDir = "uploads/";
$filename = time() . "_" . basename($_FILES["image"]["name"]);
$targetFile = $targetDir . $filename;

if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
    echo $filename;
} else {
    echo "";
}
