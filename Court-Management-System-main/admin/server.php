<?php
include "../db_connect.php";

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');

// Prevent buffering on Apache / Nginx
while (true) {
    $query = "SELECT * FROM reservations ORDER BY reservation_id ASC";
    $result = $conn->query($query);

    $rows = "";

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows .= "
            <tr>
                <td>" . htmlspecialchars($row['fullname']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>" . htmlspecialchars($row['phonenumber']) . "</td>
                <td>" . htmlspecialchars($row['court_type']) . "</td>
                <td>" . date("m/d/y", strtotime($row["date"])) . "</td>
                <td>" . htmlspecialchars($row['time_slot']) . "</td>
                <td>" . date("m/d/y", strtotime($row["created_at"])) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td>
                    <a href='accept_process.php?id=" . $row['reservation_id'] . "' class='btn'>
                        <i class='fas fa-check-square text-green-500 text-2xl'></i>
                    </a>
                    <a href='deny_process.php?id=" . $row['reservation_id'] . "'
                       class='btn'
                       onclick=\"return confirm('Are you sure you want to deny this booking?');\">
                        <i class='fas fa-times-square text-red-500 text-2xl'></i>
                    </a>
                </td>
            </tr>";
        }
    } else {
        $rows .= "<tr><td colspan='9'>No reservations found</td></tr>";
    }

    // send SSE event as JSON-safe string
    echo "data: " . json_encode($rows) . "\n\n";
    ob_flush();
    flush();

    sleep(2); // update every 2 seconds
}
?>
