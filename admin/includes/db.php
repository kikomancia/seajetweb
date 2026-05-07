<?php
// Online
$connect = @mysqli_connect('localhost', 'seajetin_admindb', '@DminDATABASE!23', 'seajetin_admin_acct');
// Local
// $connect = @mysqli_connect('localhost', 'root', '', 'sj_datab');

if (!$connect || $connect->connect_error) {
    die(json_encode(["statusCode" => 500, "message" => "Database connection failed"]));
}
