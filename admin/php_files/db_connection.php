<?php

//////////////////////////////////////////////////////////////
/////////////////// ONLINE CONNECTION ////////////////////////
/////////////////////////////////////////////////////////////
$connect = mysqli_connect('localhost', 'seajetin_admindb', '@DminDATABASE!23', 'seajetin_admin_acct');
// $connect = mysqli_connect('localhost', 'USERNAME', 'PASSWORD', 'DB_NAME');
//check connection
if ($connect->connect_error) {
	die("connection failed : " . $connect->connect_error);
} else {
	//echo json_encode(array("statusCode" => 200));
	//echo "Connection success!";
}


//////////////////////////////////////////////////////////////
/////////////////// LOCAL CONNECTION ////////////////////////
//////////////////////////////////////////////////////////////

// $connect = mysqli_connect('localhost', 'root', '', 'sj_datab');
// //check connection
// if ($connect->connect_error) {
// 	die("connection failed : " . $connect->connect_error);
// } else {
// 	//echo json_encode(array("statusCode" => 200));
// 	//echo "Connection success!";
// }

?>