<?php
/**
 * Gemini API Service for FullStackPartner.com SEO Automation (Single-Call Optimized)
 */
class OpenAIService {
    private string $apiKey;
    private string $model;

    public function __construct(string $apiKey, string $model = 'gemini-2.5-flash') {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    private function sendRequest(string $prompt): string {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->apiKey;
        
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 8192,
                'responseMimeType' => 'application/json' // Forces Gemini to return strict, valid JSON
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Gemini API returned status $httpCode: $response");
        }

        $data = json_decode($response, true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    private function cleanJson(string $text): array {
        $clean = preg_replace('/```(?:json)?\s*([\s\S]*?)\s*```/', '$1', $text);
        $data = json_decode(trim($clean), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to parse JSON from Gemini response: ' . json_last_error_msg() . ' | Raw: ' . $text);
        }
        return $data;
    }

public function generateAllSEOContent(string $keyword): array {
        $prompt = "You are an expert SEO strategist. Target keyword: '{$keyword}'. 
        Correct any spelling/grammar errors if needed and provide the corrected version.
        You MUST follow these strict SEO rules:
        1. Title must be between 50 and 60 characters and contain the exact keyword.
        2. Meta description must be strictly between 120 and 160 characters.
        3. The article HTML must include the exact target keyword inside at least one <h2> heading tag.
        
        Output STRICTLY as valid JSON with no markdown wrapping:
        {
            \"corrected_keyword\": \"The clean keyword\",
            \"lsi_keywords\": [\"kw1\", \"kw2\", \"kw3\", \"kw4\", \"kw5\"],
            \"questions\": [\"Q1?\", \"Q2?\", \"Q3?\"],
            \"search_intent\": \"Informational\",
            \"target_word_count\": 800,
            \"outline\": [
                {\"h2\": \"Introduction to {$keyword}\", \"h3s\": [\"Overview\"]}
            ],
            \"article_html\": \"<h2>Complete Guide to {$keyword}</h2><p>Comprehensive article body text...</p>\",
            \"title\": \"Optimized SEO Title Tag (50-60 chars)\",
            \"meta_description\": \"Compelling summary snippet keeping length strictly between 120 and 160 characters for optimal visibility.\",
            \"landing_page_json\": {
                \"hero_headline\": \"Headline\",
                \"hero_subheadline\": \"Subheadline\",
                \"benefits\": [{\"title\": \"Benefit 1\", \"description\": \"Desc 1\"}],
                \"faqs\": [{\"question\": \"Q1?\", \"answer\": \"A1.\"}],
                \"cta_text\": \"Get Started\"
            }
        }";
        
        $text = $this->sendRequest($prompt);
        return $this->cleanJson($text);
    }
}