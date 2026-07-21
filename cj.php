<?php
/**
 * 视频数据采集程序 v4.0 (断点续采优化版)
 * - 自动检测已采集数据，避免重复采集
 * - 支持断点续采，从上次中断位置继续
 * - Web模式自动刷新防止超时
 */

$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    header('Content-Type: text/html; charset=utf-8');
    header('X-Accel-Buffering: no');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>视频采集</title>';
    echo '<style>
        body { background:#1a1a1a; color:#0f0; font-family:Consolas,monospace; padding:20px; font-size:14px; line-height:1.8; }
        .info { color:#0ff; }
        .warn { color:#ff0; }
        .error { color:#f00; }
        .success { color:#0f0; }
        .divider { color:#666; }
        .highlight { color:#fff; background:#333; padding:2px 8px; border-radius:3px; }
    </style>';
    echo '</head><body><pre>';
}

@ini_set('output_buffering', 'Off');
@ini_set('implicit_flush', 1);
@ob_end_clean();
set_time_limit(0);
ini_set('memory_limit', '1024M');

// 数据库配置
$dbConfig = [
    'host' => 'localhost',
    'port' => 3388,
    'dbname' => 'hhzy',
    'username' => 'root',
    'password' => 'hao123ABC',
];

function output($message, $type = 'normal') {
    global $isCli;
    if (!$isCli && $type !== 'normal') {
        $message = "<span class=\"{$type}\">{$message}</span>";
    }
    echo $message;
    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();
}

function outputLine($message, $type = 'normal') {
    output($message . "\n", $type);
}

function outputDivider($char = '-', $length = 70) {
    outputLine(str_repeat($char, $length), 'divider');
}

class VideoCollector {
    private $db;
    private $dbConfig;
    private $apiUrl = 'https://api.heiapi.cc/api.php/provide/vod/?ac=list';
    private $progressFile = 'collection_progress.json';
    private $statsFile = 'collection_stats.json';
    private $maxRetries = 3;
    private $reconnectInterval = 100;
    private $lastReconnectPage = 0;
    private $requestDelay = 200000; // 0.2秒
    private $webRefreshInterval = 55; // Web模式55秒刷新
    private $webRefreshPages = 80; // 或每80页刷新
    private $startTimestamp = 0;
    
    public function __construct($dbConfig) {
        $this->dbConfig = $dbConfig;
        $this->connectDatabase();
    }
    
    private function connectDatabase() {
        try {
            if ($this->db !== null) {
                $this->db = null;
            }
            
            $dsn = "mysql:host={$this->dbConfig['host']};port={$this->dbConfig['port']};dbname={$this->dbConfig['dbname']};charset=utf8mb4";
            $this->db = new PDO($dsn, $this->dbConfig['username'], $this->dbConfig['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                PDO::ATTR_TIMEOUT => 30
            ]);
            return true;
        } catch (PDOException $e) {
            outputLine("[错误] 数据库连接失败: " . $e->getMessage(), 'error');
            return false;
        }
    }
    
    private function checkDatabaseConnection($currentPage) {
        if ($currentPage - $this->lastReconnectPage >= $this->reconnectInterval) {
            outputLine("");
            outputLine("[系统] 第{$currentPage}页 - 重新连接数据库，释放内存...", 'info');
            $this->connectDatabase();
            $this->lastReconnectPage = $currentPage;
            gc_collect_cycles();
            $memUsage = round(memory_get_usage() / 1024 / 1024, 2);
            outputLine("[系统] 当前内存使用: {$memUsage}MB", 'info');
            outputLine("");
        }
    }
    
    /**
     * 获取数据库中已有的最大 vod_id
     */
    public function getMaxVodId() {
        try {
            $stmt = $this->db->query("SELECT MAX(vod_id) as max_id FROM videob");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['max_id'] ? intval($result['max_id']) : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * 获取数据库中已有的记录数
     */
    public function getRecordCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM videob");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($result['cnt']);
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * 检查今日是否已采集过
     */
    public function getTodayStats() {
        if (file_exists($this->statsFile)) {
            $stats = json_decode(file_get_contents($this->statsFile), true);
            if ($stats && isset($stats['date']) && $stats['date'] === date('Y-m-d')) {
                return $stats;
            }
        }
        return null;
    }
    
    /**
     * 保存采集统计
     */
    private function saveStats($totalNew, $totalSkipped, $lastPage) {
        $stats = [
            'date' => date('Y-m-d'),
            'time' => date('H:i:s'),
            'total_new' => $totalNew,
            'total_skipped' => $totalSkipped,
            'last_page' => $lastPage,
            'db_count' => $this->getRecordCount()
        ];
        file_put_contents($this->statsFile, json_encode($stats, JSON_PRETTY_PRINT));
    }
    
    public function fetchData($page = 1) {
        $url = $this->apiUrl . "&pg=" . $page;
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_ENCODING => 'gzip,deflate'
        ]);
        if (!ini_get('open_basedir')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error || $httpCode != 200) {
            return false;
        }
        
        return $response;
    }
    
    public function parseData($jsonData) {
        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['code']) || $data['code'] != 1) {
            return false;
        }
        return $data;
    }
    
    public function saveVideoBatch($videoList) {
        if (empty($videoList)) {
            return ['new' => 0, 'skip' => 0, 'fail' => 0];
        }
        
        try {
            $sql = "INSERT IGNORE INTO videob (
                vod_id, vod_name, type_id, type_name, vod_en,
                vod_time, vod_remarks, vod_play_from,
                created_at, updated_at
            ) VALUES ";
            
            $placeholders = [];
            $values = [];
            $now = date('Y-m-d H:i:s');
            
            foreach ($videoList as $video) {
                $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $values[] = $video['vod_id'];
                $values[] = $video['vod_name'];
                $values[] = $video['type_id'];
                $values[] = $video['type_name'];
                $values[] = isset($video['vod_en']) ? $video['vod_en'] : '';
                $values[] = isset($video['vod_time']) ? $video['vod_time'] : null;
                $values[] = isset($video['vod_remarks']) ? $video['vod_remarks'] : '';
                $values[] = isset($video['vod_play_from']) ? $video['vod_play_from'] : '';
                $values[] = $now;
                $values[] = $now;
            }
            
            $sql .= implode(', ', $placeholders);
            
            $this->db->beginTransaction();
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            $affectedRows = $stmt->rowCount();
            $this->db->commit();
            
            $stmt = null;
            unset($placeholders, $values);
            
            return [
                'new' => $affectedRows,
                'skip' => count($videoList) - $affectedRows,
                'fail' => 0
            ];
            
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['new' => 0, 'skip' => 0, 'fail' => count($videoList)];
        }
    }
    
    private function saveProgress($page, $totalPages, $totalNew, $totalSkipped) {
        $progress = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_new' => $totalNew,
            'total_skipped' => $totalSkipped,
            'last_update' => date('Y-m-d H:i:s')
        ];
        file_put_contents($this->progressFile, json_encode($progress, JSON_PRETTY_PRINT));
    }
    
    private function loadProgress() {
        if (file_exists($this->progressFile)) {
            $content = file_get_contents($this->progressFile);
            $progress = json_decode($content, true);
            // 检查是否是今天的进度
            if ($progress && isset($progress['last_update'])) {
                $lastDate = substr($progress['last_update'], 0, 10);
                if ($lastDate === date('Y-m-d')) {
                    return $progress;
                }
            }
        }
        return null;
    }
    
    private function clearProgress() {
        if (file_exists($this->progressFile)) {
            unlink($this->progressFile);
        }
    }
    
    public function getTotalPages() {
        $jsonData = $this->fetchData(1);
        if (!$jsonData) {
            return false;
        }
        
        $data = $this->parseData($jsonData);
        if (!$data || !isset($data['pagecount'])) {
            return false;
        }
        
        return [
            'pages' => intval($data['pagecount']),
            'total' => intval($data['total'])
        ];
    }
    
    /**
     * 智能采集 - 自动断点续采
     */
    public function collect($startPage = 1, $endPage = -1, $resume = false) {
        $isCli = php_sapi_name() === 'cli';
        
        outputLine("");
        outputDivider('=');
        outputLine("   视频数据采集程序 v4.0 (断点续采优化版)", 'info');
        outputDivider('=');
        outputLine("");
        
        // 检查数据库现有数据
        $dbCount = $this->getRecordCount();
        $maxVodId = $this->getMaxVodId();
        
        outputLine("[数据库] 已有记录: {$dbCount} 条", 'info');
        outputLine("[数据库] 最大vod_id: {$maxVodId}", 'info');
        outputLine("");
        
        // 检查今日是否已采集
        $todayStats = $this->getTodayStats();
        if ($todayStats && !$resume) {
            outputLine("[检测] 今日已采集过:", 'warn');
            outputLine("       时间: {$todayStats['date']} {$todayStats['time']}", 'warn');
            outputLine("       新增: {$todayStats['total_new']} 条", 'warn');
            outputLine("       跳过: {$todayStats['total_skipped']} 条", 'warn');
            outputLine("       最后页: {$todayStats['last_page']}", 'warn');
            outputLine("");
        }
        
        // 检查是否有未完成的进度
        $progress = $this->loadProgress();
        $totalNew = 0;
        $totalSkipped = 0;
        
        if ($progress) {
            outputLine("[续传] 检测到未完成的采集任务:", 'info');
            outputLine("       上次位置: 第 {$progress['current_page']} 页", 'info');
            outputLine("       总页数: {$progress['total_pages']} 页", 'info');
            outputLine("       已新增: {$progress['total_new']} 条", 'info');
            outputLine("       已跳过: {$progress['total_skipped']} 条", 'info');
            outputLine("       更新时间: {$progress['last_update']}", 'info');
            outputLine("");
            
            // 从断点继续
            $startPage = $progress['current_page'] + 1;
            $endPage = $progress['total_pages'];
            $totalNew = $progress['total_new'];
            $totalSkipped = $progress['total_skipped'];
            
            outputLine("[续传] 将从第 {$startPage} 页继续采集", 'success');
            outputLine("");
        }
        
        // 获取总页数
        if ($endPage == -1) {
            outputLine("[API] 正在获取总页数...", 'info');
            $pageInfo = $this->getTotalPages();
            if (!$pageInfo) {
                outputLine("[错误] 无法获取总页数，请检查网络", 'error');
                return;
            }
            $endPage = $pageInfo['pages'];
            outputLine("[API] 总页数: {$endPage} 页，总记录: {$pageInfo['total']} 条", 'info');
            outputLine("");
        }
        
        // 检查是否已全部采集完成
        if ($startPage > $endPage) {
            outputLine("[完成] 所有页面已采集完毕!", 'success');
            outputLine("[统计] 数据库共 {$dbCount} 条记录", 'success');
            $this->clearProgress();
            return;
        }
        
        $totalPageCount = $endPage - $startPage + 1;
        
        outputDivider('-');
        outputLine("[任务] 采集范围: 第 {$startPage} 页 → 第 {$endPage} 页 (共 {$totalPageCount} 页)", 'highlight');
        outputLine("[优化] 批量INSERT + 0.2秒间隔 + 每{$this->reconnectInterval}页重连", 'info');
        if (!$isCli) {
            outputLine("[模式] Web自动刷新: 每{$this->webRefreshInterval}秒 或 每{$this->webRefreshPages}页", 'info');
        }
        outputDivider('-');
        outputLine("");
        
        $startTime = microtime(true);
        $this->startTimestamp = time();
        $pagesProcessed = 0;
        $consecutiveSkips = 0; // 连续跳过计数
        $maxConsecutiveSkips = 5; // 连续5页全跳过则认为已是最新
        
        for ($page = $startPage; $page <= $endPage; $page++) {
            // Web模式下检查是否需要刷新
            if (!$isCli) {
                $elapsed = time() - $this->startTimestamp;
                if ($elapsed >= $this->webRefreshInterval || $pagesProcessed >= $this->webRefreshPages) {
                    $this->saveProgress($page - 1, $endPage, $totalNew, $totalSkipped);
                    outputLine("");
                    outputDivider('-');
                    outputLine("[刷新] 已运行 {$elapsed} 秒，处理 {$pagesProcessed} 页", 'warn');
                    outputLine("[刷新] 当前进度: 新增 {$totalNew} 条，跳过 {$totalSkipped} 条", 'warn');
                    outputLine("[刷新] 1秒后自动继续采集...", 'warn');
                    outputDivider('-');
                    
                    $nextUrl = "?start={$page}&end={$endPage}&resume=1&auto=1";
                    echo "</pre><script>setTimeout(function(){ window.location.href='{$nextUrl}'; }, 1000);</script>";
                    echo '</body></html>';
                    exit;
                }
            }
            
            $this->checkDatabaseConnection($page);
            $pagesProcessed++;
            
            // 获取数据
            $retryCount = 0;
            $success = false;
            $data = null;
            
            while ($retryCount < $this->maxRetries && !$success) {
                if ($retryCount > 0) {
                    usleep(500000);
                }
                
                $jsonData = $this->fetchData($page);
                if ($jsonData) {
                    $data = $this->parseData($jsonData);
                    if ($data) {
                        $success = true;
                    }
                }
                
                if (!$success) {
                    $retryCount++;
                }
            }
            
            if (!$success) {
                outputLine("[失败] 第 {$page} 页 - 请求失败，已跳过", 'error');
                $this->saveProgress($page, $endPage, $totalNew, $totalSkipped);
                usleep($this->requestDelay);
                continue;
            }
            
            if (!isset($data['list']) || !is_array($data['list']) || empty($data['list'])) {
                outputLine("[警告] 第 {$page} 页 - 无数据", 'warn');
                $this->saveProgress($page, $endPage, $totalNew, $totalSkipped);
                usleep($this->requestDelay);
                continue;
            }
            
            // 批量保存
            $result = $this->saveVideoBatch($data['list']);
            
            $totalNew += $result['new'];
            $totalSkipped += $result['skip'];
            
            // 检测是否连续全跳过
            if ($result['new'] == 0 && $result['skip'] > 0) {
                $consecutiveSkips++;
            } else {
                $consecutiveSkips = 0;
            }
            
            unset($data, $jsonData);
            
            // 计算进度
            $percent = round(($page - $startPage + 1) / $totalPageCount * 100, 1);
            $elapsed = microtime(true) - $startTime;
            $pagesPerSecond = ($page - $startPage + 1) / max($elapsed, 0.001);
            $remainingPages = $endPage - $page;
            $eta = $pagesPerSecond > 0 ? round($remainingPages / $pagesPerSecond) : 0;
            $etaMin = floor($eta / 60);
            $etaSec = $eta % 60;
            
            // 格式化输出
            $statusIcon = $result['new'] > 0 ? '+' : '=';
            $statusType = $result['new'] > 0 ? 'success' : 'info';
            
            outputLine(sprintf(
                "[%s] %s %d/%d页 %5.1f%% | 本页:+%d =%d | 累计:+%d =%d | 剩余:%d分%d秒",
                date('H:i:s'),
                $statusIcon,
                $page,
                $endPage,
                $percent,
                $result['new'],
                $result['skip'],
                $totalNew,
                $totalSkipped,
                $etaMin,
                $etaSec
            ), $statusType);
            
            $this->saveProgress($page, $endPage, $totalNew, $totalSkipped);
            
            // 如果连续多页全是重复数据，可提前结束（仅当本轮采集无新增时才终止）
            if ($consecutiveSkips >= $maxConsecutiveSkips) {
                outputLine("");
                outputLine("[智能] 连续 {$consecutiveSkips} 页全为已有数据", 'warn');
                if ($totalNew == 0) {
                    outputLine("[智能] 本轮无新增数据，数据已是最新，提前结束", 'warn');
                    break;
                } else {
                    outputLine("[智能] 本轮已新增 {$totalNew} 条，继续采集剩余页面...", 'info');
                    $consecutiveSkips = 0; // 重置计数器继续采集
                }
            }
            
            if ($page < $endPage) {
                usleep($this->requestDelay);
            }
        }
        
        $this->clearProgress();
        $this->saveStats($totalNew, $totalSkipped, $page);
        
        $totalTime = round(microtime(true) - $startTime, 2);
        $avgSpeed = $pagesProcessed / max($totalTime, 0.001);
        
        outputLine("");
        outputDivider('=');
        outputLine("   采集完成!", 'success');
        outputDivider('=');
        outputLine("");
        outputLine("[统计] 处理页数: {$pagesProcessed} 页", 'info');
        outputLine("[统计] 耗时: {$totalTime} 秒 (平均 " . round($avgSpeed, 2) . " 页/秒)", 'info');
        outputLine("[统计] 新增: {$totalNew} 条", 'success');
        outputLine("[统计] 跳过: {$totalSkipped} 条 (已存在)", 'info');
        outputLine("[统计] 数据库总记录: " . $this->getRecordCount() . " 条", 'info');
        outputLine("");
        outputDivider('=');
    }
}

// ========== 主程序 ==========

outputLine("");
outputLine("程序启动...", 'info');
outputLine("");

if ($isCli) {
    $startPage = isset($argv[1]) ? intval($argv[1]) : 1;
    $endPage = isset($argv[2]) ? intval($argv[2]) : -1;
    $resume = isset($argv[3]) && $argv[3] === 'resume';
} else {
    $startPage = isset($_GET['start']) ? intval($_GET['start']) : 1;
    $endPage = isset($_GET['end']) ? intval($_GET['end']) : -1;
    $resume = isset($_GET['resume']);
}

if ($startPage < 1) $startPage = 1;
if ($endPage < $startPage && $endPage != -1) $endPage = $startPage;

outputLine("[初始化] 正在连接数据库...", 'info');
$collector = new VideoCollector($dbConfig);
outputLine("[初始化] 数据库连接成功", 'success');
outputLine("");

$collector->collect($startPage, $endPage, $resume);

if (!$isCli) {
    echo '</pre></body></html>';
}
?>
