<?php
// Emergency error probe for production when Laravel returns 500 everywhere.
// SECURITY: Protected by a token stored at storage/app/probe.key. Delete this file after use.

$APP_ROOT = realpath(__DIR__ . '/..');
$storage = $APP_ROOT . '/storage';
$keyFile = $storage . '/app/probe.key';

header('Content-Type: text/html; charset=utf-8');

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function exists($p){ return file_exists($p) ? 'YES' : 'NO'; }
function readable($p){ return is_readable($p) ? 'YES' : 'NO'; }
function writable($p){ return is_writable($p) ? 'YES' : 'NO'; }

// Auth check
$provided = isset($_GET['k']) ? (string)$_GET['k'] : '';
$expected = is_readable($keyFile) ? trim((string)@file_get_contents($keyFile)) : '';
if ($expected === '' || !hash_equals($expected, $provided)) {
    http_response_code(403);
    echo '<!doctype html><meta charset="utf-8"><title>403</title><div style="font-family:ui-sans-serif;max-width:680px;margin:40px auto;padding:20px;border:1px solid #ddd;border-radius:12px">';
    echo '<h2>Forbidden</h2><p>Missing or invalid token. Create token file at <code>storage/app/probe.key</code> and access <code>/error-probe.php?k=YOUR_TOKEN</code>.</p>';
    echo '</div>';
    exit;
}

$logPath = $storage . '/logs/laravel.log';
$bootstrapCache = $APP_ROOT . '/bootstrap/cache';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$messages = [];

if ($action === 'clear_caches') {
    $targets = glob($bootstrapCache . '/{config.php,packages.php,services.php,routes-*.php}', GLOB_BRACE) ?: [];
    foreach ($targets as $t) {
        if (is_file($t)) {
            if (@unlink($t)) {
                $messages[] = 'Deleted: ' . basename($t);
            } else {
                $messages[] = 'Failed to delete: ' . basename($t);
            }
        }
    }
}

// Read last ~200KB of log
$logTail = '';
if (is_readable($logPath)) {
    $size = filesize($logPath);
    $bytes = 200 * 1024;
    $fh = @fopen($logPath, 'r');
    if ($fh) {
        if ($size > $bytes) { fseek($fh, -$bytes, SEEK_END); }
        $logTail = stream_get_contents($fh) ?: '';
        fclose($fh);
    } else {
        $logTail = (string)@file_get_contents($logPath);
    }
} else {
    $logTail = "Log not readable: $logPath";
}

// Simple parse to find last ERROR block
$lastError = '';
$lines = preg_split("/\r?\n/", (string)$logTail);
for ($i = count($lines) - 1; $i >= 0; $i--) {
    if (preg_match('/\[(\d{4}-\d{2}-\d{2} [^\]]+)\] (\w+)\.(ERROR|CRITICAL)/i', $lines[$i])) {
        // collect up to 60 lines backward
        $start = max(0, $i - 60);
        $chunk = array_slice($lines, $start, $i - $start + 1);
        $lastError = implode("\n", $chunk);
        break;
    }
}

?><!doctype html>
<meta charset="utf-8">
<title>Laravel Error Probe</title>
<style>
 body{font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial; background:#f8fafc; color:#0f172a}
 .card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px;margin:16px auto;max-width:980px}
 .mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;}
 pre{white-space:pre-wrap;overflow:auto;background:#0b1020;color:#e2e8f0;padding:12px;border-radius:8px}
 .muted{color:#64748b}
 .row{display:flex;gap:12px;flex-wrap:wrap}
 .row > div{flex:1 1 240px}
 .btn{display:inline-block;padding:8px 12px;border-radius:8px;border:1px solid #cbd5e1;background:#0ea5e9;color:#fff;text-decoration:none}
 .btn.secondary{background:#64748b}
 .badge{display:inline-block;padding:2px 8px;border-radius:999px;font-size:12px;border:1px solid #cbd5e1}
</style>
<div class="card">
  <h2>Laravel Error Probe</h2>
  <p class="muted">App root: <span class="mono"><?php echo h($APP_ROOT); ?></span></p>
  <?php if ($messages): ?><div><?php foreach($messages as $m){ echo '<div class="badge">'.h($m).'</div> '; } ?></div><?php endif; ?>
  <div class="row">
    <div>
      <h3>Bootstrap Cache</h3>
      <ul class="mono">
        <li>config.php: <?php echo h(exists($bootstrapCache.'/config.php')); ?> / readable: <?php echo h(readable($bootstrapCache.'/config.php')); ?></li>
        <li>packages.php: <?php echo h(exists($bootstrapCache.'/packages.php')); ?> / readable: <?php echo h(readable($bootstrapCache.'/packages.php')); ?></li>
        <li>services.php: <?php echo h(exists($bootstrapCache.'/services.php')); ?> / readable: <?php echo h(readable($bootstrapCache.'/services.php')); ?></li>
        <li>routes-*.php exists: <?php echo h((bool)glob($bootstrapCache.'/routes-*.php') ? 'YES' : 'NO'); ?></li>
      </ul>
      <p><a class="btn" href="?k=<?php echo urlencode($provided); ?>&action=clear_caches">Delete cache files (config/packages/services/routes)</a></p>
    </div>
    <div>
      <h3>Storage Permissions</h3>
      <ul class="mono">
        <li>storage: writable=<?php echo h(writable($storage)); ?></li>
        <li>storage/logs: writable=<?php echo h(writable($storage.'/logs')); ?></li>
        <li>storage/framework: writable=<?php echo h(writable($storage.'/framework')); ?></li>
      </ul>
    </div>
  </div>
</div>

<div class="card">
  <h3>Last ERROR (approx)</h3>
  <pre class="mono"><?php echo h($lastError ?: 'No recent ERROR found in tail'); ?></pre>
</div>

<div class="card">
  <h3>Log tail (last ~200KB)</h3>
  <pre class="mono"><?php echo h($logTail); ?></pre>
</div>

