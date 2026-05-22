<?php
header('Content-Type: application/json');

// 1. Strict DSN Connection String linking to testdb
$dsn = "mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// 2. Handle Add Student (POST)
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $admNo = htmlspecialchars($input['adm_no'] ?? '');
    $name = htmlspecialchars($input['name'] ?? '');
    $className = htmlspecialchars($input['class_name'] ?? '');

    if ($admNo && $name && $className) {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (admission_number, full_name, class_name) VALUES (:adm, :name, :cls)");
            $stmt->execute(['adm' => $admNo, 'name' => $name, 'cls' => $className]);
            echo json_encode(["status" => "success", "message" => "Student successfully admitted."]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Admission number must be unique."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Please fill all fields."]);
    }
} 
// 3. Handle View Roster (GET)
else if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
    echo json_encode(["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}
?>
