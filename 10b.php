<?php
$servername = "127.0.0.1";
$username = "root";
$password = "skanda75"; // your correct MySQL password
$dbname = "librarys";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$Title = $_POST['Title'] ?? '';

if (!$Title) {
    echo "<h3>⚠️ Please enter a title to search.</h3>";
    exit;
}

$stmt = $conn->prepare("SELECT Acc_no, Title, Author, Publisher, Edition FROM book WHERE Title LIKE ?");
$search = "%$Title%";
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h3>❌ No matching results found.</h3>";
    exit;
}

echo "<h2>📚 Search Results</h2>";
echo "<table border='1' cellspacing='0' cellpadding='8'>
<tr style='background-color:#0078ff; color:white;'>
<th>Access No.</th>
<th>Title</th>
<th>Author</th>
<th>Publisher</th>
<th>Edition</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['Acc_no']}</td>
            <td>{$row['Title']}</td>
            <td>{$row['Author']}</td>
            <td>{$row['Publisher']}</td>
            <td>{$row['Edition']}</td>
          </tr>";
}

echo "</table>";

$stmt->close();
$conn->close();
?>
