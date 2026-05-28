<?php
header('Content-Type: application/json');

// 1. Strict DSN Connection
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

// 2. Handle POST requests (Login, Add Student, OR Update Password)
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    // -- LOGIN LOGIC --
    if ($action === 'login') {
        $user = htmlspecialchars($input['username'] ?? '');
        $pass = htmlspecialchars($input['password'] ?? '');

        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE username = :user AND password = :pass");
        $stmt->execute(['user' => $user, 'pass' => $pass]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "success", "token" => "SYS_AUTH_TOKEN", "username" => $user]);
        } else {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Invalid credentials."]);
        }
        exit;
    }

    // -- UPDATE PASSWORD LOGIC --
    if ($action === 'update_password') {
        $user = htmlspecialchars($input['username'] ?? '');
        $currentPass = htmlspecialchars($input['current_password'] ?? '');
        $newPass = htmlspecialchars($input['new_password'] ?? '');

        // First, verify the current password is correct
        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE username = :user AND password = :pass");
        $stmt->execute(['user' => $user, 'pass' => $currentPass]);

        if ($stmt->rowCount() > 0) {
            // If correct, update to the new password
            $updateStmt = $pdo->prepare("UPDATE teachers SET password = :newPass WHERE username = :user");
            $updateStmt->execute(['newPass' => $newPass, 'user' => $user]);
            echo json_encode(["status" => "success", "message" => "Password updated securely."]);
        } else {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Current password is incorrect."]);
        }
        exit;
    }

    // -- ADD STUDENT LOGIC --
    if ($action === 'admit') {
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
        exit;
    }
} 
// 3. Handle View Roster (GET)
else if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
    echo json_encode(["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}
// 4. Handle Delete Student (DELETE)
else if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $admNo = htmlspecialchars($input['adm_no'] ?? '');

    if ($admNo) {
        $stmt = $pdo->prepare("DELETE FROM students WHERE admission_number = :adm");
        $stmt->execute(['adm' => $admNo]);
        echo json_encode(["status" => "success"]);
    }
}
?>