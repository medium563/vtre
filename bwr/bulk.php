<?php
header('Content-Type: text/html; charset=utf-8');

// ====== কনফিগারেশন ======
$viewer = "https://www.hrr.mlit.go.jp/chokoku/hatizyuuri/progress/pannellum/pannellum.htm";
$config = "https://vempires.github.io/school.edu/ucl/pannellum.php";

// ====== slug সোর্স ======

// অপশন A: slugs.txt ফাইল থেকে (প্রতি লাইনে একটি)
$slugs = [];
$file = __DIR__ . '/slugs.txt';
if (file_exists($file)) {
    $slugs = array_filter(array_map('trim', file($file)));
}

// অপশন B: সরাসরি অ্যারে দিয়ে (দরকার হলে উপরের লাইন কমেন্ট করে নিচেরটা ব্যবহার করুন)
// $slugs = [
//     "video-celtic-celta-vigo-live-J4vG9",
//     "video-rennes-vs-juventus-match-lie-nseuihd",
//     "video-barcelona-vs-real-madrid-live-AB12cd",
// ];

// অপশন C: scrape.php থেকে অটো-লোড
// $slugs = json_decode(@file_get_contents(__DIR__.'/discovered_slugs.json'), true) ?: [];

// ====== URL তৈরি ======
$fullUrls = [];
foreach ($slugs as $slug) {
    $slug = trim($slug);
    if ($slug === '') continue;
    $fullUrls[] = $viewer . "?config=" . urlencode($config) . "?" . urlencode($slug);
}

// ====== UI ======
?><!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="utf-8">
<title>Bulk Post URL Generator</title>
<style>
    body { font-family: system-ui, sans-serif; margin: 20px; background: #f5f5f5; }
    h1 { color: #222; }
    .info { background: #e3f2fd; padding: 12px; border-radius: 6px; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
    th, td { padding: 8px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 13px; }
    th { background: #1976d2; color: #fff; }
    td.url { font-family: monospace; word-break: break-all; }
    button { padding: 4px 10px; cursor: pointer; border: 1px solid #1976d2; background: #fff; color: #1976d2; border-radius: 4px; }
    button:hover { background: #1976d2; color: #fff; }
    .toolbar { margin: 12px 0; }
    .toolbar button { background: #1976d2; color: #fff; padding: 8px 16px; }
    .count { font-weight: bold; color: #1976d2; }
</style>
</head>
<body>

<h1>🚀 Bulk Post URL Generator</h1>

<div class="info">
    মোট <span class="count"><?= count($fullUrls) ?></span> টি post URL তৈরি হয়েছে।
    <?php if (empty($slugs)): ?>
        <br><strong>⚠️ কোনো slug পাওয়া যায়নি।</strong> <code>slugs.txt</code> ফাইল তৈরি করুন (প্রতি লাইনে একটি slug)।
    <?php endif; ?>
</div>

<div class="toolbar">
    <button onclick="copyAll()">📋 সব URL একসাথে কপি</button>
</div>

<table id="urlTable">
    <thead>
        <tr><th>#</th><th>Slug</th><th>Full URL</th><th>Action</th></tr>
    </thead>
    <tbody>
    <?php foreach ($fullUrls as $i => $url):
        $slug = htmlspecialchars($slugs[$i]);
    ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><code><?= $slug ?></code></td>
            <td class="url"><?= htmlspecialchars($url) ?></td>
            <td><button onclick="copyOne(this, <?= $i ?>)">কপি</button></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
const allUrls = <?= json_encode($fullUrls, JSON_UNESCAPED_SLASHES) ?>;

function copyOne(btn, idx) {
    navigator.clipboard.writeText(allUrls[idx]).then(() => {
        btn.textContent = '✓ কপি হয়েছে';
        setTimeout(() => btn.textContent = 'কপি', 1500);
    });
}

function copyAll() {
    navigator.clipboard.writeText(allUrls.join('\n')).then(() => {
        alert(allUrls.length + ' টি URL কপি হয়েছে!');
    });
}
</script>
</body>
</html>