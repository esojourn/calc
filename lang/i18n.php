<?php
/**
 * 国际化核心文件
 * 处理语言切换和翻译功能
 */

// 启动会话以保存语言选择
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 获取当前语言
function getCurrentLanguage() {
    // 优先使用 GET 参数
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['zh', 'zh-TW', 'en', 'de'])) {
        $_SESSION['lang'] = $_GET['lang'];
        return $_GET['lang'];
    }

    // 其次使用 SESSION
    if (isset($_SESSION['lang'])) {
        return $_SESSION['lang'];
    }

    // 默认中文
    return 'zh';
}

// 加载语言文件
function loadLanguage($lang) {
    $langFile = __DIR__ . "/{$lang}.php";
    if (file_exists($langFile)) {
        return include $langFile;
    }
    // 如果语言文件不存在，加载中文作为后备
    return include __DIR__ . '/zh.php';
}

// 翻译函数
function t($key) {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : $key;
}

// 初始化
$currentLang = getCurrentLanguage();
$translations = loadLanguage($currentLang);
