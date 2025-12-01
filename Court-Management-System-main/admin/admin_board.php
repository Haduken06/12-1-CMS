<?php 
session_start();
include "../db_connect.php";

if(!isset($_SESSION['username'])){
    header("Location: ../index.php");
    exit();
}

// ==================== Prepare Dashboard Counts ====================
// Total Reservations
$sql = "SELECT (SELECT COUNT(*) FROM accepted_bookings) +
               (SELECT COUNT(*) FROM denied_bookings) +
               (SELECT COUNT(*) FROM cancelled_bookings) AS total";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalReservations = $row['total'];

// Total Users
$sql = "SELECT COUNT(*) AS total FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$users = $row['total'];

// Accepted Bookings
$sql = "SELECT COUNT(*) AS total FROM accepted_bookings";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$accepted = $row['total'];

// Denied Bookings
$sql = "SELECT COUNT(*) AS total FROM denied_bookings";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$denied = $row['total'];

// Cancelled Bookings
$sql = "SELECT COUNT(*) AS total FROM cancelled_bookings";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$cancelled = $row['total'];

// ==================== Prepare Data for Line Chart ====================
// Current month and year
$year = date('Y');
$month = date('m');
$monthName = date('F'); // Full month name

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$labels = [];
$acceptedData = $deniedData = $cancelledData = $usersData = [];

for($d = 1; $d <= $daysInMonth; $d++){
    $date = "$year-$month-" . str_pad($d,2,'0',STR_PAD_LEFT);
    $labels[] = str_pad($d, 2, '0', STR_PAD_LEFT); // Only day numbers

    // Accepted
    $sql = "SELECT COUNT(*) AS total FROM accepted_bookings WHERE DATE(created_at) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $acceptedData[] = (int)$row['total'];

    // Denied
    $sql = "SELECT COUNT(*) AS total FROM denied_bookings WHERE DATE(created_at) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $deniedData[] = (int)$row['total'];

    // Cancelled
    $sql = "SELECT COUNT(*) AS total FROM cancelled_bookings WHERE DATE(created_at) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $cancelledData[] = (int)$row['total'];

    // Users
    $sql = "SELECT COUNT(*) AS total FROM users WHERE DATE(created_at) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $usersData[] = (int)$row['total'];
}

// Convert to JSON for JS
$labelsJson = json_encode($labels);
$acceptedJson = json_encode($acceptedData);
$deniedJson = json_encode($deniedData);
$cancelledJson = json_encode($cancelledData);
$usersJson = json_encode($usersData);

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gray-50">
    <div class="flex">
      <!--Sidebar-->
      <div class="fixed top-0 left-0 flex h-screen w-64 flex-col bg-white shadow-lg">
        <div class="flex bg-gray-200 p-3 border-black border-b">
          <img src="assets/imgs/pfp.jpg" alt="pfp" class="w-16 rounded-full border-3 border-green-300">
          <h2 class="text-xl font-semibold text-center text-gray-700 m-auto">Welcome <?php echo $_SESSION['username']; ?></h2>
        </div>

        <!--Links-->
        <div class="p-3 flex flex-col flex-1 overflow-y-auto">
          <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 mb-2 text-gray-700 bg-green-200">
            <i class="fas fa-tachometer-alt"></i> Dashboard
          </a>
          <a href="reservations.php" class="flex items-center gap-3 rounded-lg px-3 py-2 mb-2 text-gray-700 hover:bg-gray-200">
            <i class="fas fa-calendar-check"></i> Reservations 
            <span id="count" class="ml-12 text-red-500 text-l font-bold"> </span>
          </a>

          <!--Dropdown-->
          <div class="relative mb-2">
            <button class="flex items-center gap-3 w-full rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-200 focus:outline-none"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-history"></i>
              <span>History</span>
              <i class="fas fa-caret-down ml-auto"></i>
            </button>
            <ul class="dropdown-menu w-full mt-1 border border-gray-200 rounded-lg shadow-sm">
              <li><a class="dropdown-item" href="accepted.php"><i class="fas fa-check-circle me-2 text-green-500"></i>Accepted</a></li>
              <li><a class="dropdown-item" href="denied.php"><i class="fas fa-ban me-2 text-yellow-500"></i>Denied</a></li>
              <li><a class="dropdown-item" href="cancelled.php"><i class="fas fa-times-circle me-2 text-red-500"></i>Cancelled</a></li>
            </ul>
          </div>

          <a href="users.php" class="flex items-center gap-3 rounded-lg px-3 py-2 mb-2 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-users"></i> Users
          </a>
          <a href="settings.php" class="flex items-center gap-3 rounded-lg px-3 py-2 mb-2 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-gear"></i>Settings
          </a>
          <a href="../logout.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-red-600 hover:bg-red-100 mt-auto">
            <i class="fas fa-right-from-bracket"></i> Logout
          </a>
        </div>
      </div>

      <!--Main-->
      <div class="ml-64 flex-1 bg-blue-50 p-6 min-h-screen">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>

        <!--Boxes-->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
          <!--Total Reservations-->
          <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
            <div>
              <h3 class='text-3xl font-bold text-blue-600'><?php echo $totalReservations; ?></h3>
              <p class="text-gray-600 font-medium">Total All Reservations</p>
            </div>
            <i class="fas fa-calendar-check text-blue-500 text-4xl"></i>
          </div>

          <!--Total Users-->
          <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
            <div>
              <h3 class='text-3xl font-bold text-green-600'><?php echo $users; ?></h3>
              <p class="text-gray-600 font-medium">Total Users</p>
            </div>
            <i class="fas fa-users text-green-500 text-4xl"></i>
          </div>

          <!--Accepted Bookings-->
          <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
            <div>
              <h3 class='text-3xl font-bold text-indigo-600'><?php echo $accepted; ?></h3>
              <p class="text-gray-600 font-medium">Total Accepted</p>
            </div>
            <i class="fas fa-check-circle text-indigo-500 text-4xl"></i>
          </div>

          <!--Denied Bookings-->
          <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
            <div>
              <h3 class='text-3xl font-bold text-yellow-600'><?php echo $denied; ?></h3>
              <p class="text-gray-600 font-medium">Total Denied</p>
            </div>
            <i class="fas fa-ban text-yellow-500 text-4xl"></i>
          </div>

          <!--Cancelled Bookings-->
          <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
            <div>
              <h3 class='text-3xl font-bold text-red-600'><?php echo $cancelled; ?></h3>
              <p class="text-gray-600 font-medium">Total Cancelled</p>
            </div>
            <i class="fas fa-times-circle text-red-500 text-4xl"></i>
          </div>
        </div>

        <!--Charts-->
        <div class="flex flex-col lg:flex-row gap-5 mt-5">
          <!--Left: Line Chart-->
          <div class="bg-white h-80 flex-1 p-5 shadow-lg rounded-lg">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Bookings Status - <?php echo $monthName; ?></h2>
            <canvas id="statusChart"></canvas>
          </div>

          <!--Right: Pie Chart-->
          <div class="bg-white h-80 flex-1 p-5 shadow-lg rounded-lg flex flex-col items-center justify-center">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 text-center">Overall Records</h2>
            <div class="w-64 h-64">
              <canvas id="pieChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
//Line Chart 
// ==================== Line Chart ====================
const lineCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(lineCtx, {
    type: 'line',
    data: {
        labels: <?php echo $labelsJson; ?>,
        datasets: [
            {
                label: 'Accepted',
                data: <?php echo $acceptedJson; ?>,
                borderColor: 'rgba(99,102,241,1)',
                backgroundColor: 'rgba(99,102,241,0.2)',
                tension: 0.3,
                fill: true,
                pointRadius: 3
            },
            {
                label: 'Denied',
                data: <?php echo $deniedJson; ?>,
                borderColor: 'rgba(202,138,4,1)',
                backgroundColor: 'rgba(202,138,4,0.2)',
                tension: 0.3,
                fill: true,
                pointRadius: 3
            },
            {
                label: 'Cancelled',
                data: <?php echo $cancelledJson; ?>,
                borderColor: 'rgba(239,68,68,1)',
                backgroundColor: 'rgba(239,68,68,0.2)',
                tension: 0.3,
                fill: true,
                pointRadius: 3
            },
            {
                label: 'New Users',
                data: <?php echo $usersJson; ?>,
                borderColor: 'rgba(22,163,74,1)',
                backgroundColor: 'rgba(22,163,74,0.2)',
                tension: 0.3,
                fill: true,
                pointRadius: 3
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'rectRounded',
                    boxWidth: 20,
                    padding: 15,
                    color: '#374151',
                    font: { size: 13 }
                }
            }
        },
        scales: {
            y: { beginAtZero: true, ticks: { color: '#374151' } },
            x: { ticks: { color: '#374151', maxRotation: 90, minRotation: 0 } }
        }
    }
});

// ==================== Pie Chart ====================
const pieCtx = document.getElementById('pieChart').getContext('2d');
const pieChart = new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: ['Reservations', 'Users', 'Accepted', 'Denied', 'Cancelled'],
        datasets: [{
            data: [<?php echo $totalReservations; ?>, <?php echo $users; ?>, <?php echo $accepted; ?>, <?php echo $denied; ?>, <?php echo $cancelled; ?>],
            backgroundColor: [
                'rgba(37, 99, 235, 1)',
                'rgba(22, 163, 74, 1)',
                'rgba(99,102,241,0.8)',
                'rgba(202,138,4,0.8)',
                'rgba(239,68,68,0.8)'
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'rectRounded',
                    boxWidth: 20,
                    padding: 10,
                    color: '#374151',
                    font: { size: 13 }
                }
            }
        }
    }
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadCount() {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("count").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "count_reservations.php", true);
    xhttp.send();
}
setInterval(loadCount, 2000);
loadCount();
</script>
</body>
</html>
