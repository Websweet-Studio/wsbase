<?php
defined('ABSPATH') || exit;

$repoUrl = 'https://github.com/Websweet-Studio/wsbase/';
$repoApiLatestRelease = 'https://api.github.com/repos/Websweet-Studio/wsbase/releases/latest';

$themeDir = get_template_directory();
$themeSlug = get_template();

$autoload = $themeDir . '/vendor/autoload.php';
$pucFile = $themeDir . '/vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';

$pucLoaded = false;
if (file_exists($autoload)) {
    require_once $autoload;
    $pucLoaded = true;
} elseif (file_exists($pucFile)) {
    require_once $pucFile;
    $pucLoaded = true;
}

if ($pucLoaded) {
    if (class_exists('\\YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory')) {
        $updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
            $repoUrl,
            $themeDir,
            $themeSlug
        );
    } elseif (class_exists('Puc_v5_Factory')) {
        $updateChecker = Puc_v5_Factory::buildUpdateChecker(
            $repoUrl,
            $themeDir,
            $themeSlug
        );
    } else {
        $updateChecker = null;
    }

    if ($updateChecker) {
        $api = $updateChecker->getVcsApi();
        if ($api) {
            $api->enableReleaseAssets();
            $updateChecker->setBranch('master');
        }

        add_action('admin_init', function () use ($updateChecker) {
            $updateChecker->checkForUpdates();
        });
        return;
    }
}

if (!function_exists('wsbase_github_theme_http_get')) {
    function wsbase_github_theme_http_get($url)
    {
        $response = wp_remote_get(
            $url,
            array(
                'timeout' => 15,
                'headers' => array(
                    'Accept' => 'application/vnd.github+json',
                    'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url('/'),
                ),
            )
        );

        if (is_wp_error($response)) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            return new WP_Error('wsbase_github_http_error', 'GitHub API request failed.', array('status' => $code));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (!is_array($data)) {
            return new WP_Error('wsbase_github_json_error', 'Invalid GitHub API response.');
        }

        return $data;
    }
}

if (!function_exists('wsbase_github_theme_get_latest_release')) {
    function wsbase_github_theme_get_latest_release($apiUrl)
    {
        $cacheKey = 'wsbase_github_latest_release';
        $cached = get_site_transient($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $data = wsbase_github_theme_http_get($apiUrl);
        $release = array();

        if (!is_wp_error($data) && is_array($data) && !empty($data['tag_name'])) {
            $release = array(
                'tag_name' => isset($data['tag_name']) ? (string) $data['tag_name'] : '',
                'html_url' => isset($data['html_url']) ? (string) $data['html_url'] : '',
                'zipball_url' => isset($data['zipball_url']) ? (string) $data['zipball_url'] : '',
                'published_at' => isset($data['published_at']) ? (string) $data['published_at'] : '',
                'body' => isset($data['body']) ? (string) $data['body'] : '',
            );
        } else {
            $tags = wsbase_github_theme_http_get('https://api.github.com/repos/Websweet-Studio/wsbase/tags?per_page=1');
            if (!is_wp_error($tags) && is_array($tags) && !empty($tags[0]) && is_array($tags[0]) && !empty($tags[0]['name'])) {
                $tag = (string) $tags[0]['name'];
                $release = array(
                    'tag_name' => $tag,
                    'html_url' => 'https://github.com/Websweet-Studio/wsbase/tree/' . rawurlencode($tag),
                    'zipball_url' => isset($tags[0]['zipball_url']) ? (string) $tags[0]['zipball_url'] : '',
                    'published_at' => '',
                    'body' => '',
                );
            }
        }

        if (empty($release)) {
            set_site_transient($cacheKey, array(), 15 * MINUTE_IN_SECONDS);
            return array();
        }

        set_site_transient($cacheKey, $release, 12 * HOUR_IN_SECONDS);
        return $release;
    }
}

if (!function_exists('wsbase_github_theme_pre_set_update_themes')) {
    function wsbase_github_theme_pre_set_update_themes($transient)
    {
        if (!is_object($transient)) {
            $transient = new stdClass();
        }
        if (!isset($transient->response) || !is_array($transient->response)) {
            $transient->response = array();
        }

        $themeSlug = get_template();
        $theme = wp_get_theme($themeSlug);
        if (!$theme || !$theme->exists()) {
            return $transient;
        }

        $currentVersion = (string) $theme->get('Version');
        if ($currentVersion === '') {
            return $transient;
        }

        $release = wsbase_github_theme_get_latest_release('https://api.github.com/repos/Websweet-Studio/wsbase/releases/latest');
        if (!is_array($release) || empty($release['tag_name'])) {
            return $transient;
        }

        $tag = (string) $release['tag_name'];
        $newVersion = ltrim($tag, "vV \t\n\r\0\x0B");
        if ($newVersion === '' || !version_compare($newVersion, $currentVersion, '>')) {
            return $transient;
        }

        $package = isset($release['zipball_url']) ? (string) $release['zipball_url'] : '';
        $url = isset($release['html_url']) ? (string) $release['html_url'] : '';
        if ($package === '') {
            return $transient;
        }

        $transient->response[$themeSlug] = array(
            'theme' => $themeSlug,
            'new_version' => $newVersion,
            'url' => $url,
            'package' => $package,
        );

        return $transient;
    }
}
add_filter('pre_set_site_transient_update_themes', 'wsbase_github_theme_pre_set_update_themes');

if (!function_exists('wsbase_github_theme_upgrader_source_selection')) {
    function wsbase_github_theme_upgrader_source_selection($source, $remote_source, $upgrader, $hook_extra)
    {
        $themeSlug = get_template();

        $isThemeUpdate = false;
        if (isset($hook_extra['theme']) && $hook_extra['theme'] === $themeSlug) {
            $isThemeUpdate = true;
        }
        if (!$isThemeUpdate && isset($hook_extra['themes']) && is_array($hook_extra['themes']) && in_array($themeSlug, $hook_extra['themes'], true)) {
            $isThemeUpdate = true;
        }
        if (!$isThemeUpdate) {
            return $source;
        }

        $sourceName = basename(untrailingslashit($source));
        if ($sourceName === $themeSlug) {
            return $source;
        }

        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        WP_Filesystem();
        global $wp_filesystem;

        if (!$wp_filesystem) {
            return $source;
        }

        $desiredSource = trailingslashit($remote_source) . $themeSlug;
        if ($wp_filesystem->is_dir($desiredSource)) {
            $wp_filesystem->delete($desiredSource, true);
        }

        $moved = $wp_filesystem->move($source, $desiredSource, true);
        if ($moved) {
            return trailingslashit($desiredSource);
        }

        return $source;
    }
}
add_filter('upgrader_source_selection', 'wsbase_github_theme_upgrader_source_selection', 10, 4);

if (!function_exists('wsbase_github_theme_themes_api')) {
    function wsbase_github_theme_themes_api($result, $action, $args)
    {
        if ($action !== 'theme_information') {
            return $result;
        }
        if (!is_object($args) || empty($args->slug) || $args->slug !== get_template()) {
            return $result;
        }

        $release = wsbase_github_theme_get_latest_release('https://api.github.com/repos/Websweet-Studio/wsbase/releases/latest');
        $tag = isset($release['tag_name']) ? (string) $release['tag_name'] : '';
        $newVersion = $tag !== '' ? ltrim($tag, "vV \t\n\r\0\x0B") : '';

        $info = new stdClass();
        $info->name = 'WsBase';
        $info->slug = get_template();
        $info->version = $newVersion !== '' ? $newVersion : (string) wp_get_theme(get_template())->get('Version');
        $info->homepage = $repoUrl;
        $info->download_link = isset($release['zipball_url']) ? (string) $release['zipball_url'] : '';
        $info->sections = array(
            'description' => isset($release['body']) && $release['body'] !== '' ? (string) $release['body'] : '',
        );

        return $info;
    }
}
add_filter('themes_api', 'wsbase_github_theme_themes_api', 10, 3);
