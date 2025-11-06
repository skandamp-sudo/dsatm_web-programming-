<?php
$servername = "127.0.0.1";
$username = "root";
$password = "skanda75"; // your actual MySQL password
$dbname = "librarys";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create DB if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
$conn->select_db($dbname);

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS book (
    Acc_no VARCHAR(20) PRIMARY KEY,
    Title VARCHAR(100),
    Author VARCHAR(100),
    Publisher VARCHAR(100),
    Edition VARCHAR(20)
)");

$Acc_no = $_POST['Acc_no'] ?? '';
$Title = $_POST['Title'] ?? '';
$Author = $_POST['Author'] ?? '';
$Edition = $_POST['Edition'] ?? '';
$Publisher = $_POST['Publisher'] ?? '';

if ($Acc_no && $Title && $Author) {
    $stmt = $conn->prepare("INSERT INTO book (Acc_no, Title, Author, Publisher, Edition) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $Acc_no, $Title, $Author, $Publisher, $Edition);
    
    if ($stmt->execute()) {
        echo "<h2>✅ Record Insertion Successful!</h2>";
    } else {
        echo "<h2>❌ Error: " . $stmt->error . "</h2>";
    }
    
    $stmt->close();
} else {
    echo "<h3>⚠️ Please fill all required fields.</h3>";
}

$conn->close();
?>
