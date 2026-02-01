<?php

    session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

require 'connection.php';

$successMsg = '';
$errorMsg = '';
$eventsFromDb = []; // to store fetched events from db

// Handle Add Appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $courseName = trim($_POST['course_name'] ?? '');
    $instructorName = trim($_POST['instructor_name'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $startTime   = $_POST["start_time"] ?? '';
    $endTime     = $_POST["end_time"] ?? '';

    $userId = $_SESSION['user_id'];

    if ($courseName && $instructorName && $startDate && $endDate) {
        $stmt = $connection->prepare(
            "INSERT INTO appointment (user_id,course_name, instructor_name, start_date, end_date, start_time, end_time) 
             VALUES (?,?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("issssss", $userId,$courseName, $instructorName, $startDate, $endDate, $startTime, $endTime);

        $stmt->execute();

        $stmt->close();

        header("Location: " . $_SERVER["PHP_SELF"] . "?success=1");
        exit();
    } else {
        header("Location: " . $_SERVER["PHP_SELF"] . "?error=1");
        exit();
    }
}

// Handle Edit Appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $courseId = $_POST['event_id'] ?? null;
    $courseName = trim($_POST['course_name'] ?? '');
    $instructorName = trim($_POST['instructor_name'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $startTime   = $_POST["start_time"] ?? '';
    $endTime     = $_POST["end_time"] ?? '';

    if ($courseId && $courseName && $instructorName && $startDate && $endDate && $startTime && $endTime && $startTime && $endTime) {
        $stmt = $connection->prepare(
            "UPDATE appointment
    SET course_name=?, instructor_name=?, start_date=?, end_date=?, start_time=?, end_time=?
    WHERE id=? AND user_id=?"
        );

        $stmt->bind_param("ssssssii", $courseName, $instructorName, $startDate, $endDate, $startTime, $endTime, $courseId,$userId);

        $stmt->execute();

        $stmt->close();

        header("Location: " . $_SERVER["PHP_SELF"] . "?success=2");
        exit();
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=2");
        exit();
    }
}

// Handle Delete Appointment

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $courseId = $_POST['event_id'] ?? null;

    if ($courseId) {
        $stmt = $connection->prepare(
            "DELETE FROM appointment WHERE id=? AND user_id=?"
        );

        $stmt->bind_param("ii", $courseId,$userId);

        $stmt->execute();

        $stmt->close();

        header("Location: " . $_SERVER["PHP_SELF"] . '?success=3');
        exit();
    } else {
        header("Location: " . $_SERVER["PHP_SELF"] . '?error=3');
    }
}

// Handle Success & Error Massages
if(isset($_GET['success'])){
    $successMsg = match ($_GET['success']) {
        '1' => 'Appoinment Added Successfully',
        '2' => 'Appoinment Updated Successfully',
        '3' => 'Appoinment Deleted Successfully',
        default => ''
    };
}

if (isset($_GET['error'])) {
    $errorMsg = 'sorry error has been ocured!. please try again and check you inputs';
}

// Fetch All Appinments and spread it over date range
$userId = $_SESSION['user_id'];
$result = $connection->query("SELECT * FROM `appointment` WHERE user_id = $userId");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (empty($row['start_date']) || empty($row['end_date'])) {
    continue; 
    }

        $start = new DateTime($row['start_date']);
        $end = new DateTime($row['end_date']);

        while ($start <= $end) {
            $eventsFromDb[] = [
                'id' => $row['id'],
                'title' => "{$row['course_name']} - {$row['instructor_name']}",
                'date' => $start->format('Y-m-d'),
                'start' => $row['start_date'],
                'end' => $row['end_date'],
                'start_time'  => $row['start_time'],
                'end_time'    => $row['end_time'],
            ]; 

            $start->modify('+1 day');
        }
    }

}

$connection->close();

?>