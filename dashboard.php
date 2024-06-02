<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_monitoring";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["parameter"])) {
    $parameter = $_POST["parameter"];
    $value = $_POST["value"];

    $stmt = $conn->prepare("INSERT INTO health_data (user_id, parameter, value) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $parameter, $value);

    if ($stmt->execute()) {
        echo "Data recorded successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$result = $conn->query("SELECT parameter, value, recorded_at FROM health_data WHERE user_id = $user_id");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Health Monitoring Dashboard</h2>
    <form action="dashboard.php" method="post">
        <label for="parameter">Parameter:</label>
        <input type="text" id="parameter" name="parameter" required><br>
        <label for="value">Value:</label>
        <input type="text" id="value" name="value" required><br>
        <button type="submit">Record Data</button>
    </form>
    <h3>Your Health Data:</h3>
    <table>
        <tr>
            <th>Parameter</th>
            <th>Value</th>
            <th>Recorded At</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row["parameter"]); ?></td>
            <td><?php echo htmlspecialchars($row["value"]); ?></td>
            <td><?php echo htmlspecialchars($row["recorded_at"]); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
