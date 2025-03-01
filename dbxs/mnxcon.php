<?php

$host="localhost";
$username="root";
$password="";
$dbname="mynsu";

try{
    $conn= new mysqli($host,$username,$password,$dbname);
}
catch(Exception $e){
    echo '<div class="alert alert-danger">Something went wrong. Ridirecting...</div>';
    header("refresh:1; url=../index.php");
    exit;
}

?>