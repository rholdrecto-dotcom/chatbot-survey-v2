<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "harold_db");

$result = $conn->query("SELECT step_name, question_text FROM bot_questions");
$questions = [];

while($row = $result->fetch_assoc()) {
    $questions[$row['step_name']] = $row['question_text'];
}

echo json_encode($questions);
$conn->close();
?>