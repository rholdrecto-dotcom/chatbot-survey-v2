var step = 0; 
var botQuestions = {}; 
var surveyData = { 
    name: "", 
    age: "", 
    department: "",
    productivity: "",
    environment: "",
    tools: "",
    rating: "", 
    comment: "" 
};

// 1. LOAD QUESTIONS FROM DATABASE
async function loadQuestions() {
    try {
        const response = await fetch('get_questions.php');
        botQuestions = await response.json();
        addBotMessage(botQuestions['greeting'] || "Hello! State your full name to begin.");
    } catch (error) {
        console.error("Error:", error);
        addBotMessage("System Error: Unable to connect to ESSS database.");
    }
}

loadQuestions();

function addBotMessage(text) {
    var chatBox = document.getElementById("chat-box");
    var typingDiv = document.createElement("div");
    typingDiv.className = "bot-msg";
    typingDiv.innerHTML = "<i>System is processing...</i>";
    chatBox.appendChild(typingDiv);
    chatBox.scrollTop = chatBox.scrollHeight;

    setTimeout(function() {
        typingDiv.innerHTML = text;
        chatBox.scrollTop = chatBox.scrollHeight;
    }, 800);
}

function sendMessage() {
    var input = document.getElementById("user-input");
    var chatBox = document.getElementById("chat-box");
    var userTextRaw = input.value.trim();
    
    if (userTextRaw === "") return;

    // Display user message
    var userDiv = document.createElement("div");
    userDiv.className = "user-msg";
    userDiv.innerHTML = userTextRaw;
    chatBox.appendChild(userDiv);

    var userText = userTextRaw.toLowerCase();

    // --- PURE SURVEY LOGIC (NO AI) ---
    switch(step) {
        case 0: 
            surveyData.name = userTextRaw;
            addBotMessage("Identity Verified: **" + surveyData.name + "**. " + (botQuestions['department'] || "What department?"));
            step = 1;
            break;

        case 1: 
            surveyData.department = userTextRaw;
            addBotMessage(botQuestions['productivity'] || "Rate your productivity (1-5):");
            step = 2;
            break;

        case 2: 
            var prodMatch = userText.match(/[1-5]/);
            if (prodMatch) {
                surveyData.productivity = prodMatch[0];
                addBotMessage(botQuestions['environment'] || "Describe your environment:");
                step = 3;
            } else {
                addBotMessage("Please provide a rating from 1 to 5.");
            }
            break;

        case 3: 
            surveyData.environment = userTextRaw;
            addBotMessage(botQuestions['tools'] || "Do you have the tools you need?");
            step = 4;
            break;

        case 4: 
            surveyData.tools = userTextRaw;
            addBotMessage(botQuestions['rating'] || "Overall management rating (1-5):");
            step = 5;
            break;

        case 5: 
            var rateMatch = userText.match(/[1-5]/);
            if (rateMatch) {
                surveyData.rating = rateMatch[0];
                addBotMessage(botQuestions['comment'] || "Any additional feedback?");
                step = 6;
            } else {
                addBotMessage("Please enter a rating between 1 and 5.");
            }
            break;

        case 6: 
            surveyData.comment = userTextRaw;
            addBotMessage("Finalizing Report... Syncing all data to the ESSS database.");
            
            fetch('save_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(surveyData)
            }).then(() => {
                setTimeout(() => { 
                    addBotMessage("ESSS Synchronization Complete. Thank you, " + surveyData.name + "!"); 
                    addBotMessage("The session has ended. Refresh the page to start a new entry.");
                }, 1500);
            });
            step = 7; 
            break;

        default:
            addBotMessage("Survey finished. Thank you for your time.");
    }

    input.value = "";
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Enter Key Support
document.getElementById("user-input").addEventListener("keypress", function(e) {
    if (e.key === "Enter") sendMessage();
});