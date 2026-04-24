<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "harold_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $name = $conn->real_escape_string($data['name']);
    $age = $conn->real_escape_string($data['age']);
    $rating = $conn->real_escape_string($data['rating']);
    $comment = $conn->real_escape_string($data['comment']);

    $sql = "INSERT INTO survey_responses (name, age, rating, comment) 
            VALUES ('$name', '$age', '$rating', '$comment')";

    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>