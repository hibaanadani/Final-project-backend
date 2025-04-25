<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once 'connection.php';
require_once 'UsersController.php';
require_once 'QuizzesController.php';
require_once 'QuestionsController.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($request_uri, PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));

// Assuming the first segment after /quiz_api/ is the endpoint
$endpoint = $segments[1] ?? '';
$id = $segments[2] ?? '';
$sub_resource = $segments[3] ?? ''; // For sub-resources like /quizzes/{id}/questions

$userController = new UsersController($conn);
$quizController = new QuizzesController($conn);
$questionController = new QuestionsController($conn);

if ($endpoint === 'users') {
    switch ($id) {
        case 'register':
            if ($request_method === 'POST') {
                $userController->registerUser();
            } else {
                http_response_code(405);
                echo json_encode(['message' => 'Method ' . $request_method . ' not allowed for this endpoint']);
            }
            break;
        case 'login':
            if ($request_method === 'POST') {
                $userController->loginUser();
            } else {
                http_response_code(405);
                echo json_encode(['message' => 'Method ' . $request_method . ' not allowed for this endpoint']);
            }
            break;
        default:
            http_response_code(404);
            echo json_encode(['message' => 'Endpoint Not Found']);
            break;
    }
} elseif ($endpoint === 'quizzes') {
    // Handle /quizzes/{id}/questions GET request
    if (isset($id) && is_numeric($id) && $sub_resource === 'questions' && $request_method === 'GET') {
        // Get questions for a specific quiz
        $questionController->getQuestionsByQuiz($id);
    } elseif (isset($id) && is_numeric($id)) {
        // Handle /quizzes/{id} (e.g., get specific quiz, update, delete)
        switch ($request_method) {
            case 'GET':
                // $quizController->getQuiz($id); // Implement this later
                http_response_code(404);
                echo json_encode(['message' => 'Quiz ID endpoint not yet implemented']);
                break;
            case 'PUT': // Or case 'PATCH':
                $quizController->updateQuiz($id);
                break;
            case 'DELETE':
                $quizController->deleteQuiz($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['message' => 'Method ' . $request_method . ' not allowed for this endpoint']);
                break;
        }
    } else {
        // Handle /quizzes (get all or create)
        switch ($request_method) {
            case 'GET':
                $quizController->getAllQuizzes();
                break;
            case 'POST':
                $quizController->createQuiz();
                break;
            default:
                http_response_code(405);
                echo json_encode(['message' => 'Method ' . $request_method . ' not allowed for this endpoint']);
                break;
        }
    }
} elseif ($endpoint === 'questions') {
    switch ($request_method) {
        case 'POST':
            // Expecting the quiz_id as the next segment after /questions/
            if (isset($id) && is_numeric($id)) {
                $questionController->createQuestion($id);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Missing or invalid quiz ID for creating question']);
            }
            break;
        case 'PUT': // Or case 'PATCH':
            if (isset($id) && is_numeric($id)) {
                $questionController->updateQuestion($id);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Missing or invalid question ID for update']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method ' . $request_method . ' not allowed for this endpoint']);
            break;
    }
}else {
    http_response_code(404);
    echo json_encode(['message' => 'Endpoint Not Found']);
}
?>