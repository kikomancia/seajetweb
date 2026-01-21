<?php
    // header("Access-Control-Allow-Origin: *");
    // header("Content-Type: application/json; charset=UTF-8");

    require 'db_connection.php';

    //fetching inquiry messages
    $sql = "SELECT * FROM `sj_messages` ORDER by date_time DESC;";
    $result = mysqli_query($connect, $sql);
    $fetchMessage = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $connect->close();

    // Return JSON response
    // echo json_encode($fetchMessage, JSON_PRETTY_PRINT);
?>