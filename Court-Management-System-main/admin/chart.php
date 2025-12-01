<?php
include "../db_connect.php";

$currentMonth = date('Y-m'); // e.g., 2025-11

$labels = [];
$reservationsData = [];
$acceptedData = [];
$deniedData = [];
$cancelledData = [];
$usersData = [];

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
for($d=1; $d<=$daysInMonth; $d++){
    $labels[] = sprintf("%02d", $d); // Day label
    $reservationsData[$d] = 0;
    $acceptedData[$d] = 0;
    $deniedData[$d] = 0;
    $cancelledData[$d] = 0;
    $usersData[$d] = 0;
}

$sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM reservations 
        WHERE DATE_FORMAT(created_at,'%Y-%m') = '$currentMonth' 
        GROUP BY DATE(created_at)";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $day = (int)date('d', strtotime($row['date']));
    $reservationsData[$day] = $row['count'];
}

$sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM accepted_bookings 
        WHERE DATE_FORMAT(created_at,'%Y-%m') = '$currentMonth' 
        GROUP BY DATE(created_at)";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $day = (int)date('d', strtotime($row['date']));
    $acceptedData[$day] = $row['count'];
}

$sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM denied_bookings 
        WHERE DATE_FORMAT(created_at,'%Y-%m') = '$currentMonth' 
        GROUP BY DATE(created_at)";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $day = (int)date('d', strtotime($row['date']));
    $deniedData[$day] = $row['count'];
}

$sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM cancelled_bookings 
        WHERE DATE_FORMAT(created_at,'%Y-%m') = '$currentMonth' 
        GROUP BY DATE(created_at)";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $day = (int)date('d', strtotime($row['date']));
    $cancelledData[$day] = $row['count'];
}

$sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM users 
        WHERE DATE_FORMAT(created_at,'%Y-%m') = '$currentMonth' 
        GROUP BY DATE(created_at)";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $day = (int)date('d', strtotime($row['date']));
    $usersData[$day] = $row['count'];
}

$labelsJS = json_encode($labels);
$reservationsJS = json_encode(array_values($reservationsData));
$acceptedJS = json_encode(array_values($acceptedData));
$deniedJS = json_encode(array_values($deniedData));
$cancelledJS = json_encode(array_values($cancelledData));
$usersJS = json_encode(array_values($usersData));

