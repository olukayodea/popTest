<?php
include_once('../conttrollers/includes.php');

echo json_encode($users->logout(), JSON_PRETTY_PRINT);
?>