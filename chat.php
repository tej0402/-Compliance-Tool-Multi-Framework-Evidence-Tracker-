<?php
require 'config.php';
require 'openai.php'; // Contains getOpenAIResponse()

$response = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userPrompt = trim($_POST['prompt'] ?? '');

    if ($userPrompt) {
        // 1. Fetch all controls
        $controls = $pdo->query("
            SELECT requirement, title, guidance, status, owner, company_comments, client_comments, updated_at 
            FROM controls
            ORDER BY requirement ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // 2. Fetch recent evidence
        $evidence = $pdo->query("
            SELECT e.file_name, e.note, e.uploaded_by, e.uploaded_at, c.requirement 
            FROM evidence e
            JOIN controls c ON e.control_id = c.id
            ORDER BY e.uploaded_at DESC
            LIMIT 100
        ")->fetchAll(PDO::FETCH_ASSOC);

        // 3. Build strict context
        $context = "You are an AI Assistant for a Compliance Tool. Answer only based on the below data from the tool. "
                 . "If the user asks anything unrelated (like writing jokes, poems, leave letters, or general advice), say: "
                 . "\"I'm sorry, I can only answer questions related to the project compliance data.\"\n\n";

        $context .= "CONTROLS DATA:\n";
        foreach ($controls as $ctrl) {
            $context .= "Requirement: {$ctrl['requirement']}\n";
            $context .= "Title: {$ctrl['title']}\n";
            $context .= "Status: {$ctrl['status']}\n";
            $context .= "Guidance: " . ($ctrl['guidance'] ?: 'N/A') . "\n";
            $context .= "Owner: " . ($ctrl['owner'] ?: 'N/A') . "\n";
            $context .= "Company Comments: " . ($ctrl['company_comments'] ?: 'None') . "\n";
            $context .= "Client Comments: " . ($ctrl['client_comments'] ?: 'None') . "\n";
            $context .= "Last Updated: " . $ctrl['updated_at'] . "\n";
            $context .= str_repeat('-', 50) . "\n";
        }

        $context .= "\nEVIDENCE DATA:\n";
        foreach ($evidence as $ev) {
            $context .= "Requirement: {$ev['requirement']}, File: {$ev['file_name']}, Note: " . ($ev['note'] ?: 'N/A')
                      . ", Uploaded By: {$ev['uploaded_by']} at {$ev['uploaded_at']}\n";
        }

        // 4. Final prompt
       $finalPrompt = $context . "\n\nUSER QUESTION: " . $userPrompt;

        // 5. Call OpenAI
        $response = getOpenAIResponse($finalPrompt);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AI Assistant – Compliance Tool</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #6a11cb;
      --secondary: #2575fc;
      --white: #fff;
      --text-dark: #333;
      --glass-bg: rgba(255, 255, 255, 0.15);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(270deg, var(--primary), var(--secondary), var(--primary));
      background-size: 600% 600%;
      animation: bgMove 10s ease infinite;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    @keyframes bgMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .container {
      background: var(--glass-bg);
      backdrop-filter: blur(12px);
      padding: 30px;
      width: 90%;
      max-width: 600px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.2);
      color: var(--white);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    textarea {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      resize: vertical;
      margin-bottom: 15px;
    }

    button {
      padding: 10px 20px;
      background: var(--secondary);
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: var(--primary);
    }

    .response {
      background: rgba(255, 255, 255, 0.1);
      padding: 15px;
      border-radius: 10px;
      margin-top: 15px;
      white-space: pre-wrap;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>💬 Ask AI Assistant</h2>
    <form method="post">
      <textarea name="prompt" rows="4" placeholder="Ask about a compliance control, policy, or remediation..." required><?= htmlspecialchars($_POST['prompt'] ?? '') ?></textarea>
      <button type="submit">Ask</button>
    </form>

    <?php if ($response): ?>
      <div class="response"><?= nl2br(htmlspecialchars($response)) ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
