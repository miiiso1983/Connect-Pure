<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ErrorViewerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:master-admin|top_management']);
    }

    public function index(Request $request)
    {
        $path = storage_path('logs/laravel.log');
        $raw = '';
        if (File::exists($path) && File::isReadable($path)) {
            $size = File::size($path);
            $bytes = 200 * 1024; // read last 200KB to keep it fast
            $fh = @fopen($path, 'r');
            if ($fh) {
                if ($size > $bytes) {
                    fseek($fh, -$bytes, SEEK_END);
                }
                $raw = stream_get_contents($fh) ?: '';
                fclose($fh);
            } else {
                $raw = File::get($path);
            }
        }
        $logs = $this->parseLog($raw);

        // Optional search filter
        $q = trim((string) $request->input('q', ''));
        if ($q !== '') {
            $logs = array_values(array_filter($logs, function ($e) use ($q) {
                return stripos($e['summary'], $q) !== false || stripos($e['body'], $q) !== false;
            }));
        }

        return view('admin.error-viewer.index', [
            'logs' => $logs,
            'path' => $path,
            'query' => $q,
        ]);
    }

    private function parseLog(string $raw): array
    {
        $entries = [];
        if ($raw === '') {
            return $entries;
        }
        $lines = preg_split("/\r?\n/", $raw);
        $current = null;
        foreach ($lines as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} [^\]]+)\] (\w+)\.([A-Za-z]+):\s*(.*)$/', $line, $m)) {
                if ($current) { $entries[] = $current; }
                $current = [
                    'id' => substr(md5(uniqid('', true)), 0, 10),
                    'timestamp' => $m[1],
                    'env' => $m[2],
                    'level' => $m[3],
                    'summary' => $m[4],
                    'body' => '',
                ];
            } else {
                if ($current !== null) {
                    $current['body'] .= $line . "\n";
                }
            }
        }
        if ($current) { $entries[] = $current; }
        // keep only the most recent 50 entries and reverse to show newest first
        if (count($entries) > 50) {
            $entries = array_slice($entries, -50);
        }
        return array_reverse($entries);
    }

    public function download()
    {
        $path = storage_path('logs/laravel.log');
        if (!File::exists($path)) {
            abort(404);
        }
        return response()->download($path, 'laravel.log');
    }

    public function clear()
    {
        $path = storage_path('logs/laravel.log');
        if (File::exists($path) && File::isWritable($path)) {
            File::put($path, '');
            return back()->with('status', __('Logs cleared.'));
        }
        return back()->with('error', __('Cannot clear log file. Check permissions.'));
    }
}

