<?php
// 1. DATABASE CONNECTION
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "harold_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. FETCH RANDOM QUESTIONS PARA SA BOT FLOW
$query = "SELECT * FROM bot_questions ORDER BY RAND()";
$result = $conn->query($query);

$questions = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $questions[] = [
            'id' => $row['id'],
            'text' => $row['question_text']
        ];
    }
}

$json_questions = json_encode($questions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harold's Survey Bot | ESSS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div id="chat-container">
        <div id="chat-header">
            <h3>Survey AI Chatbot</h3>
        </div>
        
        <div id="chat-box">
            <div class="bot-msg">Hi! I'm your Survey Assistant. Ready to answer some random office questions?</div>
        </div>

        <div class="input-area">
            <input type="text" id="user-input" placeholder="Type your answer here..." onkeydown="if (event.key === 'Enter') sendMessage()">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        // Data mula sa PHP
        const surveyQuestions = <?php echo $json_questions; ?>;
        let currentStep = 0;
        const chatBox = document.getElementById('chat-box');

        function sendMessage() {
            const inputField = document.getElementById('user-input');
            const userText = inputField.value.trim();

            if (userText === "") return;

            // Ipakita ang text ng user
            appendMessage(userText, 'user-msg');
            inputField.value = "";

            // Bot Response Logic
            setTimeout(() => {
                if (currentStep < surveyQuestions.length) {
                    const nextQ = surveyQuestions[currentStep].text;
                    appendMessage(nextQ, 'bot-msg');
                    currentStep++;
                } else {
                    appendMessage("Thank you for your feedback! Your answers have been recorded. Have a great day!", 'bot-msg');
                    inputField.disabled = true;
                    inputField.placeholder = "Survey Completed.";
                }
            }, 800);
        }

        function appendMessage(text, className) {
            const msgDiv = document.createElement('div');
            msgDiv.className = className;
            msgDiv.textContent = text;
            chatBox.appendChild(msgDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
</body>
</html>