<?php
$host = '127.0.0.1';
$user = 'root';
$pass = 'skanda75';
$dbname = 'librarys'; // match what you actually use in MySQL

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die('❌ Database connection failed: ' . $conn->connect_error);
}

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
$conn->select_db($dbname);

// Create table if it doesn't exist
$conn->query("CREATE TABLE IF NOT EXISTS contact (
    name VARCHAR(100),
    add1 VARCHAR(255),
    add2 VARCHAR(255),
    email VARCHAR(255)
)");

$insertMsg = '';
$searchResults = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'insert') {
        $n = trim($_POST['name'] ?? '');
        $a1 = trim($_POST['add1'] ?? '');
        $a2 = trim($_POST['add2'] ?? '');
        $em = trim($_POST['email'] ?? '');

        if ($n !== '' && $a1 !== '') {
            $stmt = $conn->prepare("INSERT INTO contact (name, add1, add2, email) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $n, $a1, $a2, $em);
            $ok = $stmt->execute();
            $stmt->close();
            $insertMsg = $ok ? '✅ Record inserted successfully!' : '❌ Insert failed.';
        } else {
            $insertMsg = '⚠️ Name and Address 1 cannot be empty.';
        }
    } elseif ($action === 'search') {
        $q = trim($_POST['search_name'] ?? '');
        $stmt = $conn->prepare("SELECT name, add1, add2, email FROM contact WHERE name LIKE ?");
        $like = "%{$q}%";
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $searchResults[] = $row;
        }
        $stmt->close();
    }
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Contact Manager</title>
<style>
body {
  font-family: Arial, sans-serif;
  background: #f7f8ff;
  padding: 20px;
  color: #222;
}
.container {
  max-width: 900px;
  margin: auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
.card {
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
h2 { margin-top: 0; color: #333; }
label { display: block; margin-top: 10px; font-weight: bold; }
input[type="text"], input[type="email"] {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 5px;
}
button {
  margin-top: 15px;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  background: #0078ff;
  color: white;
  cursor: pointer;
  font-weight: bold;
}
button:hover { background: #005fd1; }
.message { margin-top: 10px; font-weight: bold; color: #007800; }
.table-wrap { margin-top: 20px; overflow-x: auto; }
table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}
th, td {
  border: 1px solid #ddd;
  padding: 8px;
}
th { background: #0078ff; color: white; }
tr:nth-child(even) { background: #f2f2f2; }
</style>
</head>
<body>
<div class="container">
  <div class="card">
    <h2>Insert Contact</h2>
    <form method="post" action="">
      <input type="hidden" name="action" value="insert" />
      <label>Name</label>
      <input type="text" name="name" required />
      <label>Address 1</label>
      <input type="text" name="add1" required />
      <label>Address 2</label>
      <input type="text" name="add2" />
      <label>Email</label>
      <input type="email" name="email" />
      <button type="submit">Insert</button>
    </form>
    <?php if ($insertMsg): ?>
      <div class="message"><?= e($insertMsg) ?></div>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2>Search Contact</h2>
    <form method="post" action="">
      <input type="hidden" name="action" value="search" />
      <label>Enter Name</label>
      <input type="text" name="search_name" />
      <button type="submit">Search</button>
    </form>

    <?php if (!empty($searchResults)): ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Address 1</th>
              <th>Address 2</th>
              <th>Email</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($searchResults as $r): ?>
              <tr>
                <td><?= e($r['name']) ?></td>
                <td><?= e($r['add1']) ?></td>
                <td><?= e($r['add2']) ?></td>
                <td><?= e($r['email']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'search'): ?>
      <div class="message">No results found.</div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
