<?php
class UsersController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registerUser() {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->username) || !isset($data->email) || !isset($data->password)) {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Missing required fields: username, email, password']);
            return;
        }

        $username = trim($data->username);
        $email = filter_var(trim($data->email), FILTER_VALIDATE_EMAIL);
        $password = trim($data->password);

        if (empty($username) || empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['message' => 'All fields must be filled']);
            return;
        }

        if (!$email) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid email format']);
            return;
        }

        if (strlen($password) < 6) {
            http_response_code(400);
            echo json_encode(['message' => 'Password must be at least 6 characters long']);
            return;
        }

        // Check if username or email already exists
        $query_check = "SELECT user_id FROM users WHERE username = :username OR email = :email";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(':username', $username);
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            http_response_code(409); // Conflict
            echo json_encode(['message' => 'Username or email already exists']);
            return;
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user
        $query_insert = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt_insert = $this->conn->prepare($query_insert);
        $stmt_insert->bindParam(':username', $username);
        $stmt_insert->bindParam(':email', $email);
        $stmt_insert->bindParam(':password', $hashed_password);

        if ($stmt_insert->execute()) {
            http_response_code(201); // Created
            echo json_encode(['message' => 'User registered successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['message' => 'Failed to register user']);
        }
    }

    public function loginUser() {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->username) && !isset($data->email)) {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Please provide either username or email']);
            return;
        }

        if (!isset($data->password)) {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Please provide a password']);
            return;
        }

        $identifier = isset($data->username) ? trim($data->username) : filter_var(trim($data->email), FILTER_VALIDATE_EMAIL);
        $password = trim($data->password);

        if (empty($identifier) || empty($password)) {
            http_response_code(400);
            echo json_encode(['message' => 'Please provide username/email and password']);
            return;
        }

        $query = "SELECT user_id, username, email, password FROM users WHERE username = :identifier OR email = :identifier";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':identifier', $identifier);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Authentication successful
                http_response_code(200); // OK
                echo json_encode(['message' => 'Login successful', 'user_id' => $user['user_id'], 'username' => $user['username'], 'email' => $user['email']]);
            } else {
                // Incorrect password
                http_response_code(401); // Unauthorized
                echo json_encode(['message' => 'Invalid credentials']);
            }
        } else {
            // User not found
            http_response_code(401); // Unauthorized
            echo json_encode(['message' => 'Invalid credentials']);
        }
    }
}
?>