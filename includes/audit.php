<?php
function auditContentQuality(string $title, string $metaDesc, string $bodyHtml, string $keyword): array {
    $audit = [];
    
    $titleLen = mb_strlen($title);
    $audit['title_length'] = [
        'passed' => ($titleLen >= 50 && $titleLen <= 60),
        'message' => "Title length is {$titleLen} chars (Recommended: 50-60)."
    ];

    $metaLen = mb_strlen($metaDesc);
    $audit['meta_length'] = [
        'passed' => ($metaLen >= 150 && $metaLen <= 160),
        'message' => "Meta description length is {$metaLen} chars (Recommended: 150-160)."
    ];

    $kwLower = mb_strtolower($keyword);
    $bodyLower = mb_strtolower($bodyHtml);
    
    $audit['keyword_in_title'] = [
        'passed' => str_contains(mb_strtolower($title), $kwLower),
        'message' => str_contains(mb_strtolower($title), $kwLower) ? "Keyword found in Title." : "Target keyword missing from Title."
    ];

    preg_match('/<p>(.*?)<\/p>/i', $bodyHtml, $matches);
    $firstParagraph = $matches[1] ?? '';
    $audit['keyword_in_first_p'] = [
        'passed' => str_contains(mb_strtolower($firstParagraph), $kwLower),
        'message' => str_contains(mb_strtolower($firstParagraph), $kwLower) ? "Keyword found in first paragraph." : "Keyword missing from introduction."
    ];

    preg_match_all('/<h2[^>]*>(.*?)<\/h2>/i', $bodyHtml, $h2Matches);
    $h2Combined = implode(' ', $h2Matches[1] ?? []);
    $audit['keyword_in_h2'] = [
        'passed' => str_contains(mb_strtolower($h2Combined), $kwLower),
        'message' => str_contains(mb_strtolower($h2Combined), $kwLower) ? "Keyword found in H2 headings." : "Target keyword missing from all H2 headings."
    ];

    $audit['cta_presence'] = [
        'passed' => (str_contains($bodyLower, 'button') || str_contains($bodyLower, 'cta') || str_contains($bodyLower, 'href=')),
        'message' => "CTA / Link elements detected in body."
    ];

    return $audit;
}