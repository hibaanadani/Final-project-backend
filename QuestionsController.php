<?php
class QuestionsController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createQuestion($quiz_id) {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->question_text) || empty(trim($data->question_text))) {
            http_response_code(400);
            echo json_encode(['message' => 'Please provide the question text']);
            return;
        }

        if (!isset($data->options) || empty(trim(json_encode($data->options)))) {
            http_response_code(400);
            echo json_encode(['message' => 'Please provide the options as a JSON array']);
            return;
        }

        if (!isset($data->correct_answer) || empty(trim($data->correct_answer))) {
            http_response_code(400);
            echo json_encode(['message' => 'Please provide the correct answer']);
            return;
        }

        $question_text = trim($data->question_text);
        $options = json_encode($data->options); // Encode the array to a JSON string
        $correct_answer = trim($data->correct_answer);

        $query = "INSERT INTO questions (quiz_id, question_text, options, correct_answer) VALUES (:quiz_id, :question_text, :options, :correct_answer)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quiz_id, PDO::PARAM_INT);
        $stmt->bindParam(':question_text', $question_text);
        $stmt->bindParam(':options', $options);
        $stmt->bindParam(':correct_answer', $correct_answer);

        if ($stmt->execute()) {
            $question_id = $this->conn->lastInsertId();
            http_response_code(201); // Created
            echo json_encode(['message' => 'Question created successfully', 'question_id' => $question_id]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['message' => 'Failed to create question']);
        }
    }
}
?>