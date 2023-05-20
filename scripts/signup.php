<?php
include_once('../conttrollers/includes.php');
// Check if the sign up form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // save the log
    $users->email = $_POST['email'];
    $users->password = $_POST['password'];
    $users->firstname = $_POST['firstname'];
    $users->lastname = $_POST['lastname'];

    echo json_encode($users->signup(), JSON_PRETTY_PRINT);
}