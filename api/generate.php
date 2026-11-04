<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../db_connect.php';
    require_once __DIR__ . '/../includes/OpenAIService.php';
    require_once __DIR__ . '/../includes/audit.php';

    $input = json_decode(file_get_contents('php://input'), true);
    $keyword = trim($input['keyword'] ?? '');

    if (empty($keyword)) {
        throw new Exception('Target keyword is required.');
    }

    // PASTE YOUR ACTUAL GEMINI API KEY HERE:
    $apiKey = ''GEMINI_API_KEY;

    // Initialize Gemini AI Service with version 2.5 Flash
    $ai = new OpenAIService($apiKey, 'gemini-2.5-flash');

    // Fetch ALL components in ONE single API call (prevents HTTP 429 rate limits)
    $seoData = $ai->generateAllSEOContent($keyword);

    // Persist Content Brief to MySQL
    $stmt = $pdo->prepare("INSERT INTO content_briefs (target_keyword, search_intent, h2_h3_outline, keyword_variations) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $keyword,
        $seoData['search_intent'] ?? 'Informational',
        json_encode($seoData['outline'] ?? []),
        json_encode([
            'lsi_keywords' => $seoData['lsi_keywords'] ?? [],
            'questions' => $seoData['questions'] ?? []
        ])
    ]);
    $briefId = $pdo->lastInsertId();

    // Return successful JSON payload back to the dashboard UI
    echo json_encode([
        'success' => true,
        'brief_id' => $briefId,
        'corrected_keyword' => $seoData['corrected_keyword'] ?? $keyword,
        'title' => $seoData['title'] ?? "$keyword | Guide",
        'meta_description' => $seoData['meta_description'] ?? "Learn more about $keyword.",
        'body_html' => $seoData['article_html'] ?? '<p>Content generation failed.</p>',
        'landing_page_json' => $seoData['landing_page_json'] ?? []
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}