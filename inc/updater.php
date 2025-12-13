<?php
defined('ABSPATH') || exit;

$autoload = get_template_directory() . '/vendor/autoload.php';
$pucFile = get_template_directory() . '/vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';

if (file_exists($autoload)) {
    require_once $autoload;
} elseif (file_exists($pucFile)) {
    require_once $pucFile;
} else {
    return;
}

$themeDir = get_template_directory();
$themeSlug = wp_get_theme()->get_stylesheet();

if (class_exists('\\YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory')) {
    $updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/Websweet-Studio/wsbase/',
        $themeDir,
        $themeSlug
    );
} elseif (class_exists('Puc_v5_Factory')) {
    $updateChecker = Puc_v5_Factory::buildUpdateChecker(
        'https://github.com/Websweet-Studio/wsbase/',
        $themeDir,
        $themeSlug
    );
} else {
    return;
}

$api = $updateChecker->getVcsApi();
if ($api) {
    $api->enableReleaseAssets();
    $updateChecker->setBranch('main');
}
