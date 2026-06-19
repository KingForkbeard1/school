<?php
header('Content-Type: application/json');


$host = '127.0.0.1';
$db   = 'testdb';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];


if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    // -- LOGIN LOGIC --
    if ($action === 'login') {
        $username = htmlspecialchars($input['username'] ?? '');
        $password = htmlspecialchars($input['password'] ?? '');

        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE username = :user AND password = :pass");
        $stmt->execute(['user' => $username, 'pass' => $password]);
        
        if ($stmt->rowCount() > 0) {
            $user_data = $stmt->fetch();
            echo json_encode([
                "status" => "success", 
                "token" => "SYS_AUTH_TOKEN_" . bin2hex(random_bytes(8)),
                "role" => $user_data['role']
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Invalid credentials."]);
        }
        exit;
    }

    // -- CRUD: CREATE OPERATION (Enroll Student) --
    if ($action === 'enroll') {
        $admNo = htmlspecialchars($input['admNo'] ?? '');
        $fullName = htmlspecialchars($input['fullName'] ?? '');
        $className = htmlspecialchars($input['className'] ?? '');

        if ($admNo && $fullName && $className) {
            try {
                $stmt = $pdo->prepare("INSERT INTO students (admission_number, full_name, class_name) VALUES (:adm, :name, :class)");
                $stmt->execute(['adm' => $admNo, 'name' => $fullName, 'class' => $className]);
                echo json_encode(["status" => "success"]);
            } catch (PDOException $e) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Admission number might already exist."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing fields."]);
        }
        exit;
    }

    
    if ($action === 'update') {
        $admNo = htmlspecialchars($input['admNo'] ?? '');
        $newClass = htmlspecialchars($input['newClass'] ?? '');

        if ($admNo && $newClass) {
            $stmt = $pdo->prepare("UPDATE students SET class_name = :class WHERE admission_number = :adm");
            $stmt->execute(['class' => $newClass, 'adm' => $admNo]);
            echo json_encode(["status" => "success", "message" => "Student record updated."]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing update data."]);
        }
        exit;
    }

    
    if ($action === 'delete') {
        $admNo = htmlspecialchars($input['admNo'] ?? '');

        if ($admNo) {
            $stmt = $pdo->prepare("DELETE FROM students WHERE admission_number = :adm");
            $stmt->execute(['adm' => $admNo]);
            echo json_encode(["status" => "success", "message" => "Student record deleted."]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing admission number."]);
        }
        exit;
    }
}

if ($method === 'GET' && isset($_GET['action'])) {
    
    if ($_GET['action'] === 'fetch_students') {
        $stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $students]);
        exit;
    }
}
?>