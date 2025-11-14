<?php
function getOpenAIResponse($prompt, $model = "gpt-3.5-turbo") {
    $apiKey = 'sk-proj-YOUR_API_KEY_HERE';

    $postData = [
        "model" => $model,
        "messages" => [
            ["role" => "system", "content" => "You are a helpful assistant."],
            ["role" => "user", "content" => $prompt]
        ],
        "temperature" => 0.7
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return "Curl error: " . curl_error($ch);
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return "API error (HTTP $httpCode): " . $response;
    }

    $result = json_decode($response, true);
    return $result['choices'][0]['message']['content'] ?? "No response from API.";
}
