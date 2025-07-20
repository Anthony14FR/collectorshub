<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class LogController extends Controller
{
    public const NB_TAIL_LINES = 1000;

    public function index()
    {
        $logPath = storage_path('logs/laravel.log');
        $lines = $this->tailFile($logPath, self::NB_TAIL_LINES);
        return Inertia::render('Admin/Logs', [
            'logs' => $lines,
            'debug' => false,
            'debugLogs' => [],
        ]);
    }

    public function debug()
    {
        $logPath = storage_path('logs/laravel.log');
        $lines = $this->tailFile($logPath, self::NB_TAIL_LINES);
        $debugLogs = [];
        foreach ($lines as $line) {
            if (preg_match('/local\\.(ERROR|DEBUG|INFO)/', $line, $matches)) {
                $type = $matches[1];
                $debugLogs[] = [
                    'type' => $type,
                    'line' => $line,
                ];
            } elseif (strpos($line, 'PDOException') !== false) {
                $debugLogs[] = [
                    'type' => 'PDO',
                    'line' => $line,
                ];
            }
        }
        return Inertia::render('Admin/Logs', [
            'logs' => [],
            'debug' => true,
            'debugLogs' => $debugLogs,
        ]);
    }

    private function tailFile($filepath, $lines = 500)
    {
        if (!file_exists($filepath)) {
            return [];
        }
        $output = [];
        $command = sprintf('tail -n %d %s 2>&1', (int)$lines, escapeshellarg($filepath));
        exec($command, $output);
        return $output;
    }
}
