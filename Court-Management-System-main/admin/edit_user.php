<?php
include "../db_connect.php";

if (!isset($_GET['id'])) {
    die("User ID is missing.");
}

$id = intval($_GET['id']);

// Fetch user data
$sql = "SELECT * FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash password only if it was changed
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET username=?, email=?, password=? WHERE users_id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $username, $email, $password, $id);
    } else {
        $update_sql = "UPDATE users SET username=?, email=? WHERE users_id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $username, $email, $id);
    }

    if ($update_stmt->execute()) {
        header("Location: users.php?success=User+updated+successfully");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error updating user.</div>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md">
    <h2 class="text-2xl font-semibold mb-4 text-center">Edit User</h2>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password (leave blank to keep current)</label>
        <input type="password" name="password" class="form-control">
      </div>

      <div class="flex justify-between">
        <a href="users.php" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save Changes</button>
      </div>
    </form>
  </div>
</body>
</html>
