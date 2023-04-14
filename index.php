<?php

#DB
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "all_events"; 

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

# Select all types of events from the "all_events" table.
$sql = "SELECT * FROM all_events LIMIT 10";
$result = mysqli_query($conn, $sql);

if (isset($result) && mysqli_num_rows($result) > 0) {
    $events = array(); 

    while($row = mysqli_fetch_assoc($result)) {
        # events.
        if($row['event_type'] == 1){
            $get_only_events = "SELECT * FROM events where event_id=$row[event_id] ORDER BY event_date DESC";
            $only_events_result = mysqli_query($conn, $get_only_events);
            if ($only_events_result && mysqli_num_rows($only_events_result) > 0) {
                $event_row = mysqli_fetch_assoc($only_events_result);
                $event = new stdClass();
                $event->type = 'event';
                $event->name = $event_row["event_name"];
                $event->date = $event_row["event_date"];
                $event->description = $event_row["event_description"];
                $events[] = $event;
            }
        }
        #lectures
        elseif($row['event_type'] == 2){
            $get_only_lecturers = "SELECT * FROM lectures where lecture_id=$row[event_id] ORDER BY lecture_date DESC";
            $only_lectures_result = mysqli_query($conn, $get_only_lecturers);
            if ($only_lectures_result && mysqli_num_rows($only_lectures_result) > 0) {
                $lecture_row = mysqli_fetch_assoc($only_lectures_result);
                $lecture = new stdClass();
                $lecture->type = 'lecture';
                $lecture->name = $lecture_row["lecture_name"];
                $lecture->date = $lecture_row["lecture_date"];
                $lecture->lecturer_name = ($lecture_row["lecturer_name"] ? strtoupper($lecture_row["lecturer_name"]) : '') ;
                $lecture->description = $lecture_row["lecture_description"];
                $events[] = $lecture;
            }
        }
    
    }
    echo json_encode($events);

} else {
    echo "No events found.";
}

mysqli_close($conn);


require_once __DIR__ . '/vendor/autoload.php';

// function get_all_users_handler(){
//     echo "success";
// }
// $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
//     $r->addRoute('GET', '/users', 'get_all_users_handler');
//     // {id} must be a number (\d+)
//     $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//     // The /{title} suffix is optional
//     $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
// });

// // Fetch method and URI from somewhere
// $httpMethod = $_SERVER['REQUEST_METHOD'];
// $uri = $_SERVER['REQUEST_URI'];
// // echo $httpMethod;

// // Strip query string (?foo=bar) and decode URI
// if (false !== $pos = strpos($uri, '?')) {
//     $uri = substr($uri, 0, $pos);
// }
// $uri = rawurldecode($uri);
// echo $uri;
// $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
// // print_r($routeInfo);
// switch ($routeInfo[0]) {
//     case FastRoute\Dispatcher::NOT_FOUND:
//         // ... 404 Not Found
//         break;
//     case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
//         $allowedMethods = $routeInfo[1];
//         // ... 405 Method Not Allowed
//         break;
//     case FastRoute\Dispatcher::FOUND:
//         $handler = $routeInfo[1];
//         $vars = $routeInfo[2];
//         // ... call $handler with $vars
//         break;
// }
