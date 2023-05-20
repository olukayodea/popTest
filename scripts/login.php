<?php
include_once('../conttrollers/includes.php');
// Check if the login form is submitted
// if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Perform the login validation
    // $users->email = $_POST['email'];
    // $users->password = $_POST['password'];
    $users->email = "marguerite@fakemail.com";
    $users->password = "RandomPassword1";

    echo json_encode($users->login(), JSON_PRETTY_PRINT);
// }