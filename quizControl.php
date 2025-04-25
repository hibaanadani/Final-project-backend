<?php
class QuizzesController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createQuiz() {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->name) || empty(trim($data->name))) {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Please provide a name for the quiz']);
            return;
        }

        $name = trim($data->name);

        $query = "INSERT INTO quizzes (name) VALUES (:name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            http_response_code(201); // Created
            $quiz_id = $this->conn->lastInsertId();
            echo json_encode(['message' => 'Quiz created successfully', 'quiz_id' => $quiz_id]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['message' => 'Failed to create quiz']);
        }
    }
}
?>