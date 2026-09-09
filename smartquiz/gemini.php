<?php

function generateQuiz($documentText, $questionCount, $difficulty)
{
    $apiKey = "AQ.Ab8RN6JmkK5OjLL-u-wR393qfSGqpY1zRLkNKh0fIBMI86X8dw";

    if(empty(trim($documentText)))
    {
        return [
            "success" => false,
            "error" => "No text was extracted from the document."
        ];
    }

    // Prevent sending huge documents
    if(strlen($documentText) > 8000)
    {
        $documentText = substr($documentText, 0, 8000);
    }

    $prompt = "

You are an expert university lecturer.

Generate EXACTLY {$questionCount} questions.

Difficulty:
{$difficulty}

Requirements:

- Use only information found in the study material.
- Mix Multiple Choice, True/False and Short Answer.
- Every question must have an explanation.
- Return ONLY JSON.
- No markdown.
- No comments.
- No extra text.

JSON format:

[
{
    \"type\":\"MCQ\",
    \"question\":\"\",
    \"optionA\":\"\",
    \"optionB\":\"\",
    \"optionC\":\"\",
    \"optionD\":\"\",
    \"answer\":\"A\",
    \"explanation\":\"\"
}
]

Study Material:

{$documentText}

";

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=".$apiKey;

    $payload = [

        "contents" => [

            [

                "parts" => [

                    [

                        "text" => $prompt

                    ]

                ]

            ]

        ]

    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [

        "Content-Type: application/json"

    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);

    if(curl_errno($ch))
    {
        return [

            "success" => false,

            "error" => curl_error($ch)

        ];
    }

    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if($http != 200)
    {
        return [

            "success" => false,

            "error" => "Gemini HTTP Error ".$http."<br><pre>".htmlspecialchars($response)."</pre>"

        ];
    }

    $response = json_decode($response, true);

    if(!isset($response['candidates'][0]['content']['parts'][0]['text']))
    {
        return [

            "success" => false,

            "error" => "Gemini returned an unexpected response."

        ];
    }

    $json = $response['candidates'][0]['content']['parts'][0]['text'];

    $json = preg_replace('/```json/i', '', $json);
    $json = preg_replace('/```/', '', $json);

    $json = trim($json);

    $decoded = json_decode($json, true);

    if(json_last_error() !== JSON_ERROR_NONE)
    {
        return [

            "success" => false,

            "error" => "Gemini returned invalid JSON.<br><br><pre>".htmlspecialchars($json)."</pre>"

        ];
    }

    return [

        "success" => true,

        "questions" => $decoded

    ];
}

?>