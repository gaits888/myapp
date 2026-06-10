<?php
/**
 * 从 URL 中提取主域名（保留协议）
 * 例如：https://a.baidu.com  =>  https://baidu.com
 *       http://www.b.test.com.cn => http://test.com.cn
 *
 * @param string $url 完整 URL
 * @return string 带协议的主域名
 */
function getMainDomain($url)
{
    // 解析 URL，拿到协议和主机名
    $parts  = parse_url($url);
    $scheme = isset($parts['scheme']) ? $parts['scheme'] : 'http';
    $host   = isset($parts['host']) ? $parts['host'] : $url;

    // 如果是 IP 地址，直接返回原样
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return $scheme . '://' . $host;
    }

    // 常见的二级后缀（如 .com.cn / .org.cn 等），需要保留 3 段
    $secondLevel = array(
        'com.cn', 'net.cn', 'org.cn', 'gov.cn', 'edu.cn',
        'co.uk', 'org.uk', 'co.jp', 'com.hk', 'com.tw',
    );

    $hostParts = explode('.', $host);
    $count     = count($hostParts);

    if ($count <= 2) {
        // 本身就是主域名，如 baidu.com
        $domain = $host;
    } else {
        // 取最后两段，如 baidu.com
        $last2 = $hostParts[$count - 2] . '.' . $hostParts[$count - 1];

        if (in_array($last2, $secondLevel) && $count >= 3) {
            // 命中二级后缀，取最后三段，如 test.com.cn
            $domain = $hostParts[$count - 3] . '.' . $last2;
        } else {
            $domain = $last2;
        }
    }

    return $scheme . '://' . $domain;
}

// 测试
echo getMainDomain('https://a.baidu.com');          // https://baidu.com
echo "\n";
echo getMainDomain('http://www.b.test.com.cn');     // http://test.com.cn
echo "\n";
echo getMainDomain('https://baidu.com');            // https://baidu.com
echo "\n";
echo getMainDomain('https://shop.example.org');     // https://example.org
