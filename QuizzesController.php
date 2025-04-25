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
    public function getAllQuizzes() {
        $query = "SELECT quiz_id, name FROM quizzes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($quizzes) {
            http_response_code(200); // OK
            echo json_encode($quizzes);
        } else {
            http_response_code(200); // OK (no quizzes found is still a successful request)
            echo json_encode([]); // Return an empty array if no quizzes exist
        }
    }
    public function updateQuiz($id) {
        $data = json_decode(file_get_contents("php://input"));
    
        if (!isset($data->name) || empty(trim($data->name))) {
            http_response_code(400);
            echo json_encode(['message' => 'Please provide a name for the quiz']);
            return;
        }
    
        $name = trim($data->name);
    
        $query = "UPDATE quizzes SET name = :name WHERE quiz_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                echo json_encode(['message' => 'Quiz updated successfully', 'quiz_id' => $id]);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Quiz not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update quiz']);
        }
    }
    public function deleteQuiz($id) {
        $query = "DELETE FROM quizzes WHERE quiz_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                http_response_code(200); // OK
                echo json_encode(['message' => 'Quiz deleted successfully', 'quiz_id' => $id]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['message' => 'Quiz not found']);
            }
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['message' => 'Failed to delete quiz']);
        }
    }
}
?>