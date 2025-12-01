<?php
include "../db_connect.php";

$sql = "SELECT COUNT(*) AS total FROM reservations";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

echo $data['total']; 
