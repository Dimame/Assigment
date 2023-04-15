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
# function to retrieve events from the events table.
function getEvents($conn, $eventIds) {
    $events = array();

    $eventIdsString = implode(',', $eventIds);
    $sql = "SELECT * FROM events WHERE event_id IN ($eventIdsString) ORDER BY event_date DESC";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $event = new stdClass();
            $event->type = 'event';
            $event->name = $row["event_name"];
            $event->date = $row["event_date"];
            $event->description = $row["event_description"];
            $events[] = $event;
        }
    }

    return $events;
}

# function to retrieve lectures from the lectures table.
function getLectures($conn, $lectureIds) {
    $lectures = array();

    $lectureIdsString = implode(',', $lectureIds);
    $sql = "SELECT * FROM lectures WHERE lecture_id IN ($lectureIdsString) ORDER BY lecture_date DESC";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $lecture = new stdClass();
            $lecture->type = 'lecture';
            $lecture->name = $row["lecture_name"];
            $lecture->date = $row["lecture_date"];
            $lecture->lecturer_name = ($row["lecturer_name"] ? strtoupper($row["lecturer_name"]) : '') ;
            $lecture->description = $row["lecture_description"];
            $lectures[] = $lecture;
        }
    }

    return $lectures;
}

# function to build events from the "all_events" table.
function buildEvents($conn) {
    $sql = "SELECT * FROM all_events LIMIT 10";
    $result = mysqli_query($conn, $sql);

    if (isset($result) && mysqli_num_rows($result) > 0) {
        $events = array();
        $eventIds = array();
        $lectureIds = array();

        while($row = mysqli_fetch_assoc($result)) {
            if($row['event_type'] == 1){
                $eventIds[] = $row['event_id'];
            }
            elseif($row['event_type'] == 2){
                $lectureIds[] = $row['event_id'];
            }
        }

        # events from the events table.
        if (!empty($eventIds)) {
            $events = array_merge($events, getEvents($conn, $eventIds));
        }

        # lectures from the lectures table.
        if (!empty($lectureIds)) {
            $events = array_merge($events, getLectures($conn, $lectureIds));
        }

        echo json_encode($events);

    } else {
        echo "No events found.";
    }
}

buildEvents($conn);

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
