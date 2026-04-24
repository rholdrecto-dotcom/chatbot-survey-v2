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

// --- 2. LOGIC ---
if (isset($_POST['update_questions'])) {
    foreach ($_POST['questions'] as $id => $text) {
        $id = intval($id);
        $text = $conn->real_escape_string($text);
        $conn->query("UPDATE bot_questions SET question_text = '$text' WHERE id = $id");
    }
    header("Location: admin.php?msg=updated");
    exit();
}

if (isset($_POST['add_new_question'])) {
    $new_step = $conn->real_escape_string($_POST['new_step_name']);
    $new_text = $conn->real_escape_string($_POST['new_question_text']);
    // Default high sort_order para sa mga bagong survey questions para randomized sila
    if (!empty($new_step) && !empty($new_text)) {
        $conn->query("INSERT INTO bot_questions (step_name, question_text, sort_order) VALUES ('$new_step', '$new_text', 99)");
        header("Location: admin.php?msg=added");
        exit();
    }
}

if (isset($_GET['delete_id'])) {
    $id_to_delete = intval($_GET['delete_id']); 
    $conn->query("DELETE FROM survey_responses WHERE id = $id_to_delete");
    header("Location: admin.php?msg=deleted");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'reset_all') {
    $conn->query("TRUNCATE TABLE survey_responses");
    header("Location: admin.php?msg=reset");
    exit();
}

// --- 3. DATA FETCHING ---
$result = $conn->query("SELECT * FROM survey_responses ORDER BY date_submitted DESC");

/** * LOGIC: I-sort muna sa sort_order (1, 2, 3 mauuna). 
 * Kapag pareho silang 99 (survey proper), doon sila magra-random.
 */
$q_result = $conn->query("SELECT * FROM bot_questions ORDER BY sort_order ASC, RAND()");

$stats = $conn->query("SELECT COUNT(*) as total, AVG(rating) as average FROM survey_responses")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESSS | Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f3f4f6;
            --accent: #6366f1;
            --dark: #1f2937;
            --text: #111827;
            --white: #ffffff;
            --border: #e5e7eb;
            --danger: #ef4444;
            --success: #10b981;
            --primary-lite: #eef2ff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            margin: 0;
            color: var(--text);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container { 
            max-width: 100%; 
            margin: 0; 
            padding: 20px 40px; 
            flex: 1; 
            box-sizing: border-box;
        } 

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }
        .page-header h1 { font-size: 32px; font-weight: 800; margin: 0; letter-spacing: -1px; }

        .header-actions { display: flex; gap: 20px; }
        .header-actions a { text-decoration: none; font-size: 13px; font-weight: 700; color: var(--accent); }

        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 40px; }
        .stat-box { background: var(--white); padding: 25px; border-radius: 20px; border: 1px solid var(--border); box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .stat-box label { display: block; font-size: 11px; font-weight: 800; color: #9ca3af; text-transform: uppercase; margin-bottom: 8px; }
        .stat-box .number { font-size: 36px; font-weight: 800; display: block; margin-top: 10px; }

        .grid-layout { display: flex; flex-direction: column; gap: 30px; }
        .panel { background: var(--white); border-radius: 24px; padding: 30px; border: 1px solid var(--border); width: 100%; box-sizing: border-box; }
        .panel h3 { margin: 0 0 20px 0; font-size: 20px; font-weight: 800; }

        .scroll-container {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 10px;
            margin-bottom: 20px;
        }

        .scroll-container::-webkit-scrollbar { width: 6px; }
        .scroll-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .scroll-container::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }

        .input-field { width: 100%; padding: 14px; border: 1.5px solid var(--border); border-radius: 12px; margin-bottom: 20px; box-sizing: border-box; font-family: inherit; }
        .btn-primary { background: var(--accent); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 700; width: 100%; cursor: pointer; transition: 0.2s; }
        .btn-success { background: var(--success); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 700; width: 100%; cursor: pointer; transition: 0.2s; }
        .btn-primary:hover, .btn-success:hover { opacity: 0.9; transform: translateY(-1px); }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; color: #9ca3af; padding: 15px; text-transform: uppercase; border-bottom: 1px solid var(--border); }
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; }

        .msg-pop { background: var(--dark); color: white; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 30px; }

        .bot-questions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        footer { text-align: center; padding: 30px; color: #6b7280; font-size: 13px; border-top: 1px solid var(--border); background: var(--white); margin-top: 40px; }
    </style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <div>
            <h1>ESSS Dashboard</h1>
            <p>Employee Satisfaction Survey System | Admin Panel</p>
        </div>
        <div class="header-actions">
            <a href="index.php" target="_blank">Open Bot</a>
            <a href="admin.php?action=reset_all" style="color: var(--danger);" onclick="return confirm('Erase all records?')">Wipe Data</a>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="msg-pop">✓ Command Successful: <?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <div class="stats-row">
        <div class="stat-box">
            <label>Total Submissions</label>
            <span class="number"><?php echo $stats['total']; ?></span>
        </div>
        <div class="stat-box">
            <label>Average Happiness</label>
            <span class="number"><?php echo number_format($stats['average'], 1); ?>/5.0</span>
        </div>
        <div class="stat-box">
            <label>Active Bot Steps</label>
            <span class="number"><?php echo $q_result->num_rows; ?></span>
        </div>
    </div>

    <div class="grid-layout">
        <div class="panel">
            <h3>Employee Feedback Records</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td style="font-weight: 700;"><?php echo htmlspecialchars($row["name"]); ?></td>
                                    <td style="color: #b45309; font-weight: 800;">★ <?php echo $row["rating"]; ?></td>
                                    <td style="color: #6b7280;"><?php echo htmlspecialchars($row["comment"]); ?></td>
                                    <td><a href="admin.php?delete_id=<?php echo $row['id']; ?>" style="color: var(--danger); text-decoration: none; font-size: 11px; font-weight: 800;" onclick="return confirm('Delete?')">DELETE</a></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; color:#9ca3af; padding: 30px;">No records yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <h3>Bot Flow Configuration <span style="font-size: 12px; color: var(--accent); font-weight: normal;">(Ordered & Randomized)</span></h3>
            <form method="POST">
                <div class="scroll-container">
                    <div class="bot-questions-grid">
                        <?php 
                        $q_result->data_seek(0);
                        while($q = $q_result->fetch_assoc()): 
                            // Highlight natin yung Primary Questions (Intro)
                            $isPrimary = ($q['sort_order'] < 10);
                        ?>
                            <div style="background: <?php echo $isPrimary ? 'var(--primary-lite)' : '#f9fafb'; ?>; 
                                        padding: 15px; border-radius: 12px; 
                                        border: 1px solid <?php echo $isPrimary ? 'var(--accent)' : 'var(--border)'; ?>;">
                                <label style="font-size: 10px; font-weight: 800; color: #9ca3af; margin-bottom: 5px; display: block;">
                                    <?php echo strtoupper($q['step_name']); ?>
                                    <?php if($isPrimary) echo " <span style='color:var(--accent)'>(PRIMARY)</span>"; ?>
                                </label>
                                <input type="text" name="questions[<?php echo $q['id']; ?>]" class="input-field" style="margin-bottom:0;" value="<?php echo htmlspecialchars($q['question_text']); ?>">
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <button type="submit" name="update_questions" class="btn-primary">Sync Bot Content</button>
            </form>
        </div>

        <div class="panel">
            <h3>+ Add New Question</h3>
            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                    <div>
                        <label style="font-size: 11px; font-weight: 800; color: var(--dark); display: block; margin-bottom: 8px;">Step Identifier</label>
                        <input type="text" name="new_step_name" class="input-field" placeholder="e.g., Lighting" required>
                    </div>
                    <div>
                        <label style="font-size: 11px; font-weight: 800; color: var(--dark); display: block; margin-bottom: 8px;">Question Text</label>
                        <input type="text" name="new_question_text" class="input-field" placeholder="What will the bot ask?" required>
                    </div>
                </div>
                <button type="submit" name="add_new_question" class="btn-success">Finish & Publish 🚀</button>
            </form>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2026 Survey AI Chatbot</p>
    <p>Developed by: <strong>Harold Recto</strong> | BSIT - RSU-SFC</p>
</footer>

</body>
</html>