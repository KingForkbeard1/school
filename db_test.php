<?php
$dsn = "mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4";

try {
    // Attempt the connection
    $pdo = new PDO($dsn, "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // If it gets here, it worked!
    echo "<h2 style='color: green;'>Success!</h2>";
    echo "<p>Connected to the <strong>testdb</strong> database successfully via PDO.</p>";

} catch (PDOException $e) {
    // If it fails, show the error
    echo "<h2 style='color: red;'>Connection Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>