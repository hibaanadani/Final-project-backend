<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once 'connection.php';
require_once 'UsersController.php';
require_once 'QuizzesController.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($request_uri, PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));

// Assuming the first segment after /quiz_api/ is the endpoint
$endpoint = $segments[1] ?? '';
$action = $segments[2] ?? '';
$id = $segments[3] ?? '';

$userController = new UsersController($conn);
$quizController = new QuizzesController($conn); // Instantiate the QuizzesController

if ($endpoint === 'users') {
    switch ($action) {
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
    switch ($request_method) {
        case 'GET':
            // Handle getting all or a single quiz (we'll add single later)
            if (isset($id) && is_numeric($id)) {
                // $quizController->getQuiz($id); // Implement this later
            } else {
                $quizController->getAllQuizzes();
            }
            break;
        case 'POST':
            error_log("Action value: " . $action); 
            $quizController->createQuiz();
            break;
        case 'PUT': // Or case 'PATCH':
            if ($action && is_numeric($action)) {
                $quizController->updateQuiz($action);
            } else {
                http_response_code(400); // Bad Request
                echo json_encode(['message' => 'Missing or invalid quiz ID for update']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method ' . $request_method . ' not allowed for this endpoint']);
            break;
    }
} elseif ($endpoint === 'questions') {
    // ... handle questions later
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Endpoint Not Found']);
}
?>