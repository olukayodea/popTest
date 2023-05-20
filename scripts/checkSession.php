<?php
include_once('../conttrollers/includes.php');

echo json_encode($users->getData(), JSON_PRETTY_PRINT);
?>