<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../includes/audit.php';

$input = json_decode(file_get_contents('php://input'), true);

$briefId = $input['brief_id'] ?? null;
$keyword = $input['target_keyword'] ?? '';
$title = $input['title'] ?? '';
$meta = $input['meta_description'] ?? '';
$body = $input['body_html'] ?? '';
$contentType = $input['content_type'] ?? 'blog';
$landingJson = isset($input['landing_page_json']) ? json_encode($input['landing_page_json']) : null;

if (empty($keyword) || empty($title) || empty($body)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields.']);
    exit;
}

// Generate URL slug
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));

try {
    $stmt = $pdo->prepare("INSERT INTO contents (brief_id, target_keyword, content_type, title, slug, meta_description, body_html, landing_page_json, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'reviewed')");
    $stmt->execute([
        $briefId,
        $keyword,
        $contentType,
        $title,
        $slug,
        $meta,
        $body,
        $landingJson
    ]);

    echo json_encode(['success' => true, 'message' => 'Content saved successfully!', 'slug' => $slug]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database save failed: ' . $e->getMessage()]);
}