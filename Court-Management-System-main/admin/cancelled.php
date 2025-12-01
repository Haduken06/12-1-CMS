<?php
session_start();
include "../db_connect.php";

$sql = "SELECT * FROM cancelled_bookings";
$result = $conn -> query($sql);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reservations</title>

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
    <a href="admin_board.php" class="flex items-center gap-3 rounded-lg px-3 py-2 mb-2 text-gray-700 hover:bg-gray-100">
      <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <a href="reservations.php" class="flex items-center gap-3 rounded-lg px-3 py-2 mb-2 text-gray-700 hover:bg-gray-100">
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
        <li class="bg-green-200"><a class="dropdown-item" href="cancelled.php"><i class="fas fa-times-circle me-2 text-red-500"></i>Cancelled</a></li>
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
        <h1 class="text-3xl font-bold text-gray-800">Cancelled</h1>
            <!--Table-->  
        <div class="mt-4">
  <?php
  if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          echo '<div class="bg-white shadow-sm rounded-lg p-4 mb-4 sm:table sm:table-fixed sm:min-w-full">';
          echo '<div class="sm:table-row">';
          echo '<div class="sm:table-cell"><span class="font-semibold">Fullname:</span> '.htmlspecialchars($row["fullname"]).'</div>';
          echo '<div class="sm:table-cell"><span class="font-semibold">Email:</span> '.htmlspecialchars($row["email"]).'</div>';
          echo '<div class="sm:table-cell"><span class="font-semibold">Phone:</span> '.htmlspecialchars($row["phonenumber"]).'</div>';
          echo '<div class="sm:table-cell"><span class="font-semibold">Court Type:</span> '.htmlspecialchars($row["court_type"]).'</div>';
          echo '<div class="sm:table-cell"><span class="font-semibold">Book Date:</span> '.date("m/d/Y", strtotime($row["date"])).'</div>';
          echo '<div class="sm:table-cell"><span class="font-semibold">Time:</span> '.htmlspecialchars($row["time_slot"]).'</div>';
          echo '<div class="sm:table-cell"><span class="font-semibold">Created:</span> '.date("m/d/Y", strtotime($row["created_at"])).'</div>';
          echo '<div class="sm:table-cell"><span class="font-semibold">Status:</span> '.htmlspecialchars($row["status"]).'</div>';
          echo '<div class="sm:table-cell"><span class="font-semibold">Date Cancelled:</span> '.date("m/d/Y", strtotime($row["cancelled_date"])).'</div>';
          echo '</div>'; // row
          echo '</div>'; // card
      }
  } else {
      echo "<div class='text-center text-gray-500 mt-4'>No results found.</div>";
  }
  ?>
</div>
   
      </div>
    </div>

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
