<?php

ignore_user_abort(true);    // 客户端断开不影响
set_time_limit(0);          // 无执行时间限制

/**
 * 图片和视频上传API
 * PHP 5.4+ 兼容
 * 
 * 图片存储: /pics/年月日/年月日时分秒_随机5位数字.jpeg
 * 视频存储: /videos/年月日/年月日时分秒_随机5位数字.mp4
 */

header('Content-Type: application/json; charset=utf-8');

// 配置
$config = array(
    'image' => array(
        'base_dir' => __DIR__ . '/pics',
        'url_prefix' => '/pics',
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => array('image/jpeg', 'image/png', 'image/gif', 'image/heic', 'image/heif', 'image/webp'),
        'allowed_ext' => array('jpg', 'jpeg', 'png', 'gif', 'heic', 'heif', 'webp'),
    ),
    'video' => array(
        'base_dir' => __DIR__ . '/videos',
        'url_prefix' => '/videos',
        'max_size' => 50 * 1024 * 1024, // 50MB
        'allowed_types' => array('video/mp4', 'video/avi', 'video/quicktime', 'video/x-msvideo', 'video/webm'),
        'allowed_ext' => array('mp4', 'avi', 'mov', 'webm'),
    ),
    'ffmpeg_path' => 'ffmpeg6', // 使用服务器安装的 ffmpeg6
);

/**
 * 返回JSON响应
 */
function response($code, $msg, $data = array()) {
    echo json_encode(array(
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 生成文件名
 * 格式: 年月日时分秒_随机8位字符（包含字母数字，降低并发冲突风险）
 */
function generateFileName() {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    $random = '';
    for ($i = 0; $i < 8; $i++) {
        $random .= $chars[mt_rand(0, 35)];
    }
    return date('YmdHis') . '_' . $random;
}

/**
 * 获取文件扩展名（带安全检查）
 */
function getFileExtension($filename) {
    // 安全处理：只取文件名部分，防止路径遍历
    $filename = basename($filename);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    // 只允许字母数字，防止特殊字符注入
    $ext = preg_replace('/[^a-z0-9]/', '', $ext);
    return $ext;
}

/**
 * 严格验证图片文件（不仅检查扩展名，还检查文件头和实际内容）
 * @param string $filePath 临时文件路径
 * @return array ['valid' => bool, 'type' => string, 'error' => string]
 */
function validateImage($filePath) {
    $result = array('valid' => false, 'type' => '', 'error' => '');
    
    // 1. 检查文件是否存在且可读
    if (!file_exists($filePath) || !is_readable($filePath)) {
        $result['error'] = '文件不存在或无法读取';
        return $result;
    }
    
    // 2. 检查文件大小（至少要有一些字节）
    if (filesize($filePath) < 8) {
        $result['error'] = '文件太小，不是有效图片';
        return $result;
    }
    
    // 3. 读取文件头（magic bytes）
    $fp = fopen($filePath, 'rb');
    if (!$fp) {
        $result['error'] = '无法打开文件';
        return $result;
    }
    $header = fread($fp, 16);
    fclose($fp);
    
    // 4. 检查 magic bytes（支持iOS/Android拍照格式）
    $magicBytes = array(
        'jpeg' => array("\xFF\xD8\xFF"),
        'png'  => array("\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"),
        'gif'  => array("GIF87a", "GIF89a"),
        'webp' => array("RIFF"),  // WebP: RIFF....WEBP
    );
    
    $detectedType = '';
    
    // 检查常规格式
    foreach ($magicBytes as $type => $signatures) {
        foreach ($signatures as $sig) {
            if (substr($header, 0, strlen($sig)) === $sig) {
                // WebP 需要额外验证 WEBP 标识
                if ($type === 'webp') {
                    if (substr($header, 8, 4) === 'WEBP') {
                        $detectedType = $type;
                        break 2;
                    }
                } else {
                    $detectedType = $type;
                    break 2;
                }
            }
        }
    }
    
    // 检查 HEIC/HEIF 格式（iOS 拍照默认格式）
    // HEIC/HEIF 文件头在第4字节开始是 "ftyp"，后面是具体类型标识
    if (empty($detectedType) && substr($header, 4, 4) === 'ftyp') {
        $brandStart = 8;
        $brand = substr($header, $brandStart, 4);
        $heicBrands = array('heic', 'heix', 'hevc', 'hevx', 'heim', 'heis', 'hevm', 'hevs', 'mif1', 'msf1', 'avif');
        if (in_array(strtolower($brand), $heicBrands)) {
            $detectedType = 'heic';
        }
    }
    
    if (empty($detectedType)) {
        $result['error'] = '文件头不是有效的图片格式（支持: jpg, png, gif, heic, heif, webp）';
        return $result;
    }
    
    // 5. HEIC/HEIF 需要特殊处理（PHP GD 不支持）
    if ($detectedType === 'heic') {
        // 使用 ffmpeg 验证文件是否完整可解析
        $tempFile = '/tmp/ffprobe_img_' . uniqid() . '.txt';
        $probeCmd = 'ffmpeg6 -v error -i ' . escapeshellarg($filePath) . ' -f null - 2>' . escapeshellarg($tempFile);
        $cmdOutput = array();
        exec($probeCmd, $cmdOutput, $returnCode);
        
        $probeOutput = '';
        if (file_exists($tempFile)) {
            $probeOutput = file_get_contents($tempFile);
            @unlink($tempFile);
        }
        
        // 如果有严重错误，说明文件损坏
        if (!empty($probeOutput) && (stripos($probeOutput, 'Invalid') !== false || 
            stripos($probeOutput, 'Error') !== false ||
            stripos($probeOutput, 'corrupt') !== false)) {
            $result['error'] = 'HEIC 图片文件损坏或不完整';
            return $result;
        }
        
        $result['valid'] = true;
        $result['type'] = $detectedType;
        $result['needConvert'] = true; // 标记需要转换
        return $result;
    }
    
    // 6. WebP 需要特殊处理（PHP 5.4 的 GD 库不支持 WebP，使用 ffmpeg 验证）
    if ($detectedType === 'webp') {
        // 使用 ffmpeg 验证 WebP 文件是否完整可解析
        $tempFile = '/tmp/ffprobe_webp_' . uniqid() . '.txt';
        $probeCmd = 'ffmpeg6 -v error -i ' . escapeshellarg($filePath) . ' -f null - 2>' . escapeshellarg($tempFile);
        $cmdOutput = array();
        exec($probeCmd, $cmdOutput, $returnCode);
        
        $probeOutput = '';
        if (file_exists($tempFile)) {
            $probeOutput = file_get_contents($tempFile);
            @unlink($tempFile);
        }
        
        // 如果有严重错误，说明文件损坏
        if (!empty($probeOutput) && (stripos($probeOutput, 'Invalid') !== false || 
            stripos($probeOutput, 'Error') !== false ||
            stripos($probeOutput, 'corrupt') !== false)) {
            $result['error'] = 'WebP 图片文件损坏或不完整';
            return $result;
        }
        
        $result['valid'] = true;
        $result['type'] = 'webp';
        $result['needConvert'] = true; // 标记需要转换为 JPEG
        return $result;
    }
    
    // 7. 使用 getimagesize() 进一步验证其他格式（jpg, png, gif）
    $imageInfo = @getimagesize($filePath);
    if ($imageInfo === false) {
        $result['error'] = '无法解析图片信息，文件可能已损坏或伪造';
        return $result;
    }
    
    // 8. 验证图片类型是否匹配
    $allowedImageTypes = array(
        IMAGETYPE_JPEG => 'jpeg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_GIF  => 'gif',
    );
    
    if (!isset($allowedImageTypes[$imageInfo[2]])) {
        $result['error'] = '不支持的图片格式';
        return $result;
    }
    
    // 9. 检查图片尺寸是否合理（防止超大图片攻击）
    if ($imageInfo[0] > 10000 || $imageInfo[1] > 10000) {
        $result['error'] = '图片尺寸过大';
        return $result;
    }
    
    // 10. 尝试用 GD 库加载图片（最终验证）
    $gdLoadFunctions = array(
        IMAGETYPE_JPEG => 'imagecreatefromjpeg',
        IMAGETYPE_PNG  => 'imagecreatefrompng',
        IMAGETYPE_GIF  => 'imagecreatefromgif',
    );
    
    if (isset($gdLoadFunctions[$imageInfo[2]]) && function_exists($gdLoadFunctions[$imageInfo[2]])) {
        $img = @call_user_func($gdLoadFunctions[$imageInfo[2]], $filePath);
        if ($img === false) {
            $result['error'] = '图片数据无效，无法加载';
            return $result;
        }
        imagedestroy($img);
    }
    
    $result['valid'] = true;
    $result['type'] = $allowedImageTypes[$imageInfo[2]];
    return $result;
}

/**
 * 严格验证视频文件（检查文件头和使用 ffmpeg 验证）
 * @param string $filePath 临时文件路径
 * @return array ['valid' => bool, 'type' => string, 'error' => string]
 */
function validateVideo($filePath) {
    $result = array('valid' => false, 'type' => '', 'error' => '');
    
    // 1. 检查文件是否存在且可读
    if (!file_exists($filePath) || !is_readable($filePath)) {
        $result['error'] = '文件不存在或无法读取';
        return $result;
    }
    
    // 2. 检查文件大小（视频至少要有一定字节）
    if (filesize($filePath) < 1024) {
        $result['error'] = '文件太小，不是有效视频';
        return $result;
    }
    
    // 3. 读取文件头（magic bytes）
    $fp = fopen($filePath, 'rb');
    if (!$fp) {
        $result['error'] = '无法打开文件';
        return $result;
    }
    $header = fread($fp, 32);
    fclose($fp);
    
    // 4. 检查常见视频格式的 magic bytes
    $isValidHeader = false;
    $detectedType = '';
    
    // MP4/MOV: ftyp 标识（位于第4-8字节）
    if (substr($header, 4, 4) === 'ftyp') {
        $isValidHeader = true;
        $detectedType = 'mp4';
    }
    // AVI: RIFF....AVI 
    elseif (substr($header, 0, 4) === 'RIFF' && substr($header, 8, 4) === 'AVI ') {
        $isValidHeader = true;
        $detectedType = 'avi';
    }
    // MOV (旧格式): moov 或 mdat
    elseif (substr($header, 4, 4) === 'moov' || substr($header, 4, 4) === 'mdat' || substr($header, 4, 4) === 'wide' || substr($header, 4, 4) === 'free') {
        $isValidHeader = true;
        $detectedType = 'mov';
    }
    // WebM/MKV: 0x1A45DFA3
    elseif (substr($header, 0, 4) === "\x1A\x45\xDF\xA3") {
        $isValidHeader = true;
        $detectedType = 'webm';
    }
    
    if (!$isValidHeader) {
        $result['error'] = '文件头不是有效的视频格式';
        return $result;
    }
    
    // 5. 使用 ffmpeg 验证视频是否可以解析（只读取前几秒，避免大文件超时）
    $tempFile = '/tmp/ffmpeg_validate_' . uniqid() . '.txt';
    // 只解码前3秒，足以验证文件是否有效
    $validateCmd = 'ffmpeg6 -v error -t 3 -i ' . escapeshellarg($filePath) . ' -f null - 2>' . escapeshellarg($tempFile);
    $cmdOutput = array();
    exec($validateCmd, $cmdOutput, $returnCode);
    
    $ffmpegOutput = '';
    if (file_exists($tempFile)) {
        $ffmpegOutput = file_get_contents($tempFile);
        @unlink($tempFile);
    }
    
    // 如果 ffmpeg 输出包含严重错误，则视频无效
    if (!empty($ffmpegOutput) && (stripos($ffmpegOutput, 'Invalid data') !== false || 
        stripos($ffmpegOutput, 'Invalid NAL') !== false ||
        stripos($ffmpegOutput, 'no frame') !== false ||
        stripos($ffmpegOutput, 'Error while decoding') !== false)) {
        $result['error'] = '视频文件损坏或格式无效';
        return $result;
    }
    
    $result['valid'] = true;
    $result['type'] = $detectedType;
    return $result;
}

/**
 * 将 HEIC/HEIF/WebP 图片转换为 JPEG（使用 ffmpeg6）
 * @param string $filePath 原图片文件路径
 * @param string $outputDir 输出目录
 * @param string $baseName 基础文件名（不含扩展名）
 * @return array ['converted' => bool, 'filename' => string, 'error' => string|null]
 */
function convertImageToJpeg($filePath, $outputDir, $baseName) {
    $result = array('converted' => false, 'filename' => '', 'error' => null);
    
    // 生成输出文件名
    $outputFileName = $baseName . '.jpeg';
    $outputDir = rtrim($outputDir, '/') . '/';
    $outputPath = $outputDir . $outputFileName;
    
    // 使用 ffmpeg6 转换为 JPEG
    $tempFile = '/tmp/ffmpeg_img_' . uniqid() . '.txt';
    $convertCommand = 'ffmpeg6 -y -i ' . escapeshellarg($filePath) . ' -qscale:v 2 ' . escapeshellarg($outputPath) . ' 2>' . escapeshellarg($tempFile);
    $cmdOutput = array();
    exec($convertCommand, $cmdOutput, $returnCode);
    
    // 读取转换输出
    $ffmpegOutputStr = '';
    if (file_exists($tempFile)) {
        $ffmpegOutputStr = file_get_contents($tempFile);
        @unlink($tempFile);
    }
    
    // 检查转换是否成功
    if (file_exists($outputPath) && filesize($outputPath) > 0) {
        // 验证转换后的图片是否真的能打开
        $checkInfo = @getimagesize($outputPath);
        if ($checkInfo === false) {
            @unlink($outputPath);
            $result['error'] = '图片转换后验证失败，文件可能损坏';
            return $result;
        }
        
        // 转码成功，删除原文件
        @unlink($filePath);
        $result['converted'] = true;
        $result['filename'] = $outputFileName;
    } else {
        $result['error'] = '图片转换失败: ' . substr($ffmpegOutputStr, 0, 500);
    }
    
    return $result;
}

/**
 * 检测视频编码并将 HEVC/H.265 转码为 H.264
 * @param string $filePath 原视频文件路径
 * @param string $outputDir 输出目录
 * @param string $baseName 基础文件名（不含扩展名）
 * @return array ['converted' => bool, 'filename' => string, 'error' => string|null]
 */
function convertHevcToH264($filePath, $outputDir, $baseName) {
    $result = array('converted' => false, 'filename' => '', 'error' => null);
    
    // 使用临时文件捕获 ffmpeg 输出（因为 popen/shell_exec 被禁用）
    $tempFile = '/tmp/ffmpeg_output_' . uniqid() . '.txt';
    
    // 检测视频编码
    $probeCommand = 'ffmpeg6 -i ' . escapeshellarg($filePath) . ' 2>' . escapeshellarg($tempFile);
    $cmdOutput = array();
    exec($probeCommand, $cmdOutput, $returnCode);
    
    // 读取 ffmpeg 输出
    $probeOutputStr = '';
    if (file_exists($tempFile)) {
        $probeOutputStr = file_get_contents($tempFile);
        @unlink($tempFile);
    }
    
    if (empty($probeOutputStr)) {
        $result['error'] = 'ffmpeg 命令执行失败，请检查 ffmpeg6 是否可用';
        return $result;
    }
    
    // 检查是否为 HEVC/H.265 编码（在 Video: 行中检测，避免误判）
    $isHevc = false;
    if (preg_match('/Video:.*\b(hevc|h265|h\.265)\b/i', $probeOutputStr)) {
        $isHevc = true;
    }
    
    if (!$isHevc) {
        // 不是 HEVC，无需转码
        return $result;
    }
    
    // 生成输出文件名（转码后统一为mp4）
    // 如果原文件已经是mp4，需要使用临时文件名避免冲突
    $outputDir = rtrim($outputDir, '/') . '/';
    $originalExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if ($originalExt === 'mp4') {
        $outputFileName = $baseName . '_converted.mp4';
    } else {
        $outputFileName = $baseName . '.mp4';
    }
    $outputPath = $outputDir . $outputFileName;
    
    // 使用 ffmpeg6 转码为 H.264
    $tempFile2 = '/tmp/ffmpeg_convert_' . uniqid() . '.txt';
    $convertCommand = 'ffmpeg6 -y -i ' . escapeshellarg($filePath) . ' -c:v libx264 -preset fast -crf 23 -c:a aac -movflags +faststart ' . escapeshellarg($outputPath) . ' 2>' . escapeshellarg($tempFile2);
    $cmdOutput2 = array();
    exec($convertCommand, $cmdOutput2, $returnCode2);
    
    // 读取转码输出
    $ffmpegOutputStr = '';
    if (file_exists($tempFile2)) {
        $ffmpegOutputStr = file_get_contents($tempFile2);
        @unlink($tempFile2);
    }
    
    // 检查转码是否成功
    if (file_exists($outputPath) && filesize($outputPath) > 0) {
        // 验证转码后的视频是否能正常解析
        $verifyFile = '/tmp/ffmpeg_verify_' . uniqid() . '.txt';
        $verifyCmd = 'ffmpeg6 -v error -t 1 -i ' . escapeshellarg($outputPath) . ' -f null - 2>' . escapeshellarg($verifyFile);
        $verifyOutput = array();
        exec($verifyCmd, $verifyOutput, $verifyCode);
        
        $verifyResult = '';
        if (file_exists($verifyFile)) {
            $verifyResult = file_get_contents($verifyFile);
            @unlink($verifyFile);
        }
        
        // 如果验证有严重错误，说明转码后的视频也有问题
        if (!empty($verifyResult) && (stripos($verifyResult, 'Invalid') !== false || 
            stripos($verifyResult, 'Error while decoding') !== false)) {
            @unlink($outputPath);
            $result['error'] = '视频转码后验证失败，文件可能损坏';
            return $result;
        }
        
        // 转码成功，删除原文件
        @unlink($filePath);
        $result['converted'] = true;
        $result['filename'] = $outputFileName;
    } else {
        $result['error'] = '转码失败: ' . substr($ffmpegOutputStr, 0, 500);
    }
    
    return $result;
}

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(400, '请求方法错误');
}

// 获取上传类型
$type = isset($_POST['type']) ? $_POST['type'] : '';
if (!in_array($type, array('image', 'video'))) {
    response(400, '上传类型错误');
}

// 检查文件
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errorMsg = '文件上传失败';
    if (isset($_FILES['file'])) {
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errorMsg = '文件大小超出限制';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errorMsg = '文件上传不完整';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errorMsg = '没有选择文件';
                break;
        }
    }
    response(400, $errorMsg);
}

$file = $_FILES['file'];
$typeConfig = $config[$type];

// 检查文件大小
if ($file['size'] > $typeConfig['max_size']) {
    $maxMB = $typeConfig['max_size'] / 1024 / 1024;
    response(400, '文件大小不能超过' . $maxMB . 'MB');
}

// 严格验证文件真实性（不仅检查扩展名，还检查文件头和实际内容）
$needImageConvert = false;
if ($type === 'image') {
    $validation = validateImage($file['tmp_name']);
    if (!$validation['valid']) {
        response(400, '图片验证失败: ' . $validation['error']);
    }
    // 检查是否需要转换（HEIC/WebP）
    if (isset($validation['needConvert']) && $validation['needConvert']) {
        $needImageConvert = true;
    }
} elseif ($type === 'video') {
    $validation = validateVideo($file['tmp_name']);
    if (!$validation['valid']) {
        response(400, '视频验证失败: ' . $validation['error']);
    }
}

// 生成存储路径
$dateFolder = date('Ymd');
$saveDir = $typeConfig['base_dir'] . '/' . $dateFolder;

// 创建目录
if (!is_dir($saveDir)) {
    if (!mkdir($saveDir, 0755, true)) {
        response(500, '创建目录失败');
    }
}

// 生成文件名
$baseName = generateFileName();
$ext = getFileExtension($file['name']);
$originalExt = $ext; // 保存原始扩展名

// 视频：使用检测到的真实格式（转码后会变成mp4）
if ($type === 'video') {
    $ext = $validation['type']; // mp4, avi, mov, webm
}

// 图片：如果需要转换（HEIC/WebP），先用原始扩展名保存，转换后再改为 jpeg
// 如果不需要转换，使用验证后检测到的真实格式
if ($type === 'image') {
    if ($needImageConvert) {
        // 需要转换的格式，先用原始扩展名保存
        $ext = $originalExt;
    } else {
        // 不需要转换，使用检测到的真实格式（防止扩展名欺骗）
        $ext = $validation['type']; // jpeg, png, gif
    }
}

$fileName = $baseName . '.' . $ext;
$savePath = $saveDir . '/' . $fileName;

// 移动上传的文件
if (!move_uploaded_file($file['tmp_name'], $savePath)) {
    response(500, '保存文件失败');
}

// 设置文件权限
chmod($savePath, 0644);

$converted = false;

// 图片格式转换处理（HEIC/WebP 转为 JPEG）
if ($type === 'image' && $needImageConvert) {
    $convertResult = convertImageToJpeg($savePath, $saveDir, $baseName);
    if ($convertResult['converted']) {
        $fileName = $convertResult['filename'];
        $savePath = $saveDir . '/' . $fileName; // 更新文件路径
        $converted = true;
    } else {
        // 转换失败，删除已上传的文件并返回错误
        @unlink($savePath);
        response(500, '图片格式转换失败: ' . $convertResult['error']);
    }
}

// 视频转码处理
if ($type === 'video') {
    $needVideoConvert = false;
    
    // 1. 检测是否为 HEVC/H.265 编码，需要转为 H.264
    $convertResult = convertHevcToH264($savePath, $saveDir, $baseName);
    if ($convertResult['converted']) {
        $fileName = $convertResult['filename'];
        $savePath = $saveDir . '/' . $fileName; // 更新文件路径
        $converted = true;
        $needVideoConvert = false; // 已经转码完成
    } elseif (!empty($convertResult['error'])) {
        // 转码失败，删除已上传的文件并返回错误
        @unlink($savePath);
        response(500, '视频转码失败: ' . $convertResult['error']);
    } else {
        // 不是 HEVC，检查是否需要格式转换（AVI/MOV/WebM 转 MP4）
        if (in_array($ext, array('avi', 'mov', 'webm'))) {
            $needVideoConvert = true;
        }
    }
    
    // 2. 非 MP4 格式转为 MP4（提高浏览器兼容性）
    if ($needVideoConvert) {
        $outputFileName = $baseName . '.mp4';
        $outputPath = $saveDir . '/' . $outputFileName;
        
        $tempFile = '/tmp/ffmpeg_format_' . uniqid() . '.txt';
        $convertCmd = 'ffmpeg6 -y -i ' . escapeshellarg($savePath) . ' -c:v libx264 -preset fast -crf 23 -c:a aac -movflags +faststart ' . escapeshellarg($outputPath) . ' 2>' . escapeshellarg($tempFile);
        $cmdOutput = array();
        exec($convertCmd, $cmdOutput, $returnCode);
        
        $ffmpegOutput = '';
        if (file_exists($tempFile)) {
            $ffmpegOutput = file_get_contents($tempFile);
            @unlink($tempFile);
        }
        
        if (file_exists($outputPath) && filesize($outputPath) > 0) {
            // 验证转码后的视频是否能正常解析
            $verifyFile = '/tmp/ffmpeg_verify_format_' . uniqid() . '.txt';
            $verifyCmd = 'ffmpeg6 -v error -t 1 -i ' . escapeshellarg($outputPath) . ' -f null - 2>' . escapeshellarg($verifyFile);
            $verifyOutput = array();
            exec($verifyCmd, $verifyOutput, $verifyCode);
            
            $verifyResult = '';
            if (file_exists($verifyFile)) {
                $verifyResult = file_get_contents($verifyFile);
                @unlink($verifyFile);
            }
            
            // 如果验证有严重错误，说明转码后的视频有问题
            if (!empty($verifyResult) && (stripos($verifyResult, 'Invalid') !== false || 
                stripos($verifyResult, 'Error while decoding') !== false)) {
                @unlink($outputPath);
                @unlink($savePath);
                response(500, '视频格式转换后验证失败，文件可能损坏');
            }
            
            // 转换成功，删除原文件
            @unlink($savePath);
            $fileName = $outputFileName;
            $savePath = $outputPath; // 更新文件路径
            $converted = true;
        } else {
            // 转换失败，删除已上传的文件并返回错误
            @unlink($savePath);
            response(500, '视频格式转换失败: ' . substr($ffmpegOutput, 0, 500));
        }
    }
}

// 返回结果
$url = $typeConfig['url_prefix'] . '/' . $dateFolder . '/' . $fileName;

response(200, '上传成功', array(
    'url' => $url,
    'converted' => $converted
));