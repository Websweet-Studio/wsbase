<?php
defined('ABSPATH') || exit;

if (!is_admin() && !(defined('DOING_CRON') && DOING_CRON) && !(defined('WP_CLI') && WP_CLI)) {
    return;
}

$repoUrl = 'https://github.com/Websweet-Studio/wsbase/';
$repoApiLatestRelease = 'https://api.github.com/repos/Websweet-Studio/wsbase/releases/latest';

$themeDir = get_template_directory();
$themeSlug = get_template();

$autoload = $themeDir . '/vendor/autoload.php';
$pucFile = $themeDir . '/vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';

$wsbase_use_puc = false;
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
        $wsbase_use_puc = true;
    }
}

if (!function_exists('wsbase_admin_page_url')) {
    function wsbase_admin_page_url($args = [])
    {
        $url = admin_url('themes.php?page=wsbase');
        if (!is_array($args) || empty($args)) {
            return $url;
        }
        return add_query_arg($args, $url);
    }
}

if (!function_exists('wsbase_admin_page_assets')) {
    function wsbase_admin_page_assets($hook)
    {
        if (!is_string($hook) || $hook !== 'appearance_page_wsbase') {
            return;
        }

        wp_register_style('wsbase-admin-page', false);
        wp_enqueue_style('wsbase-admin-page');
        wp_add_inline_style(
            'wsbase-admin-page',
            '.wp-store-wrapper{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;max-width:1000px;margin:20px auto}.wp-store-header{margin-bottom:20px}.wp-store-title{font-size:24px;font-weight:600;margin:0 0 5px 0;color:#1d2327}.wp-store-helper{color:#646970;font-size:14px;margin:0}.wp-store-card{background:#fff;border:1px solid #c3c4c7;border-radius:6px;padding:16px}.wp-store-dashboard-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}@media (max-width:960px){.wp-store-dashboard-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (max-width:640px){.wp-store-dashboard-grid{grid-template-columns:1fr}}.wp-store-card-title{font-size:13px;color:#646970}.wp-store-card-value{font-size:24px;font-weight:700;color:#1d2327;margin-top:6px}.wp-store-card-desc{font-size:12px;color:#7a7f86;margin-top:4px}.wp-store-dashboard-sections{margin-top:16px;display:grid;grid-template-columns:300px 1fr;gap:16px}@media (max-width:960px){.wp-store-dashboard-sections{grid-template-columns:1fr}}.wp-store-box{background:#fff;border:1px solid #c3c4c7;border-radius:6px}.wp-store-box-header{padding:12px 16px;border-bottom:1px solid #e5e7eb;font-weight:600;color:#1d2327}.wp-store-status{gap:10px;padding:16px;display:flex;flex-direction:column}.wp-store-status-item{display:flex;align-items:center;justify-content:space-between;background:#f6f7f7;border:1px solid #dcdcde;border-radius:6px;padding:10px 12px}.wp-store-badge{display:inline-block;padding:2px 10px;border-radius:9999px;font-size:12px;font-weight:600;line-height:1.4;border:1px solid transparent}.wp-store-badge-yellow{background:#fef3c7;color:#92400e;border-color:#fde68a}.wp-store-badge-green{background:#dcfce7;color:#065f46;border-color:#86efac}.wp-store-badge-blue{background:#dbeafe;color:#1e40af;border-color:#93c5fd}.wp-store-badge-red{background:#fee2e2;color:#991b1b;border-color:#fca5a5}.wp-store-btn{display:inline-flex;align-items:center;gap:5px;padding:8px 16px;border-radius:4px;font-weight:600;cursor:pointer;border:1px solid transparent;text-decoration:none;font-size:13px;transition:background-color .2s ease,border-color .2s ease,box-shadow .2s ease,color .2s ease}.wp-store-btn-primary{background:#2271b1;color:#fff;border-color:#2271b1}.wp-store-btn-primary:hover{background:#135e96;border-color:#135e96}.wp-store-btn-secondary{background:#f6f7f7;color:#2271b1;border-color:#2271b1}.wp-store-btn-secondary:hover{background:#f0f0f1;border-color:#135e96;color:#135e96}.wp-store-btn:disabled{opacity:.7;cursor:not-allowed}.wsbase-wps-header{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap}.wsbase-wps-badges{display:flex;gap:8px;align-items:center;flex-wrap:wrap}.wsbase-wps-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center;padding:16px}.wsbase-wps-body{padding:16px}.wsbase-wps-prose{white-space:pre-wrap;color:#3c434a;font-size:13px;line-height:1.6}'
        );
    }
}
add_action('admin_enqueue_scripts', 'wsbase_admin_page_assets');

if (!function_exists('wsbase_admin_set_notice')) {
    function wsbase_admin_set_notice($type, $message)
    {
        $uid = get_current_user_id();
        if ($uid <= 0) {
            return;
        }
        set_transient('wsbase_admin_notice_' . $uid, [
            'type' => sanitize_key((string) $type),
            'message' => sanitize_text_field((string) $message),
        ], MINUTE_IN_SECONDS);
    }
}

if (!function_exists('wsbase_admin_get_notice')) {
    function wsbase_admin_get_notice()
    {
        $uid = get_current_user_id();
        if ($uid <= 0) {
            return null;
        }
        $key = 'wsbase_admin_notice_' . $uid;
        $notice = get_transient($key);
        if (!is_array($notice)) {
            return null;
        }
        delete_transient($key);
        return $notice;
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
            $tag = isset($data['tag_name']) ? (string) $data['tag_name'] : '';
            $version = ltrim($tag, "vV \t\n\r\0\x0B");
            $assetUrl = '';
            if (!empty($data['assets']) && is_array($data['assets']) && $version !== '') {
                foreach ($data['assets'] as $asset) {
                    if (!is_array($asset)) {
                        continue;
                    }
                    $name = isset($asset['name']) ? (string) $asset['name'] : '';
                    $url = isset($asset['browser_download_url']) ? (string) $asset['browser_download_url'] : '';
                    if ($name === '' || $url === '') {
                        continue;
                    }
                    $expected = 'wsbase-v' . $version . '.zip';
                    if (strcasecmp($name, $expected) === 0) {
                        $assetUrl = $url;
                        break;
                    }
                    if ($assetUrl === '' && preg_match('/^wsbase-v[0-9]+\.[0-9]+\.[0-9]+\.zip$/i', $name)) {
                        $assetUrl = $url;
                    }
                }
            }
            $release = array(
                'tag_name' => $tag,
                'html_url' => isset($data['html_url']) ? (string) $data['html_url'] : '',
                'zipball_url' => isset($data['zipball_url']) ? (string) $data['zipball_url'] : '',
                'asset_url' => $assetUrl,
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
                    'asset_url' => '',
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

if (!function_exists('wsbase_github_repo_get_latest_release')) {
    function wsbase_github_repo_get_latest_release($ownerRepo, $preferNamePrefix = '')
    {
        $ownerRepo = is_string($ownerRepo) ? trim($ownerRepo) : '';
        if ($ownerRepo === '') {
            return array();
        }
        $cacheKey = 'wsbase_github_latest_release_' . md5(strtolower($ownerRepo));
        $cached = get_site_transient($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $apiUrl = 'https://api.github.com/repos/' . $ownerRepo . '/releases/latest';
        $data = wsbase_github_theme_http_get($apiUrl);
        $release = array();

        if (!is_wp_error($data) && is_array($data) && !empty($data['tag_name'])) {
            $tag = isset($data['tag_name']) ? (string) $data['tag_name'] : '';
            $version = ltrim($tag, "vV \t\n\r\0\x0B");
            $assetUrl = '';
            if (!empty($data['assets']) && is_array($data['assets'])) {
                foreach ($data['assets'] as $asset) {
                    if (!is_array($asset)) {
                        continue;
                    }
                    $name = isset($asset['name']) ? (string) $asset['name'] : '';
                    $url = isset($asset['browser_download_url']) ? (string) $asset['browser_download_url'] : '';
                    if ($name === '' || $url === '') {
                        continue;
                    }
                    if (!preg_match('/\.zip$/i', $name)) {
                        continue;
                    }
                    if ($preferNamePrefix !== '' && stripos($name, $preferNamePrefix) !== false) {
                        $assetUrl = $url;
                        break;
                    }
                    if ($assetUrl === '') {
                        $assetUrl = $url;
                    }
                }
            }
            $release = array(
                'tag_name' => $tag,
                'html_url' => isset($data['html_url']) ? (string) $data['html_url'] : '',
                'zipball_url' => isset($data['zipball_url']) ? (string) $data['zipball_url'] : '',
                'asset_url' => $assetUrl,
                'published_at' => isset($data['published_at']) ? (string) $data['published_at'] : '',
                'body' => isset($data['body']) ? (string) $data['body'] : '',
            );
        }

        if (empty($release)) {
            set_site_transient($cacheKey, array(), 15 * MINUTE_IN_SECONDS);
            return array();
        }

        set_site_transient($cacheKey, $release, 12 * HOUR_IN_SECONDS);
        return $release;
    }
}

if (!function_exists('wsbase_plugin_status_by_dir')) {
    function wsbase_plugin_status_by_dir($pluginDir)
    {
        $pluginDir = is_string($pluginDir) ? sanitize_key($pluginDir) : '';
        if ($pluginDir === '') {
            return array(
                'installed' => false,
                'active' => false,
                'version' => '',
                'file' => '',
                'name' => '',
            );
        }

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins('/' . $pluginDir);
        if (!is_array($plugins) || empty($plugins)) {
            return array(
                'installed' => false,
                'active' => false,
                'version' => '',
                'file' => '',
                'name' => '',
            );
        }

        $mainFile = '';
        $mainName = '';
        $mainVersion = '';
        foreach ($plugins as $file => $data) {
            $mainFile = $pluginDir . '/' . $file;
            $mainName = isset($data['Name']) ? (string) $data['Name'] : '';
            $mainVersion = isset($data['Version']) ? (string) $data['Version'] : '';
            break;
        }

        $active = $mainFile !== '' ? is_plugin_active($mainFile) : false;
        return array(
            'installed' => true,
            'active' => (bool) $active,
            'version' => $mainVersion,
            'file' => $mainFile,
            'name' => $mainName,
        );
    }
}

if (!$wsbase_use_puc && !function_exists('wsbase_github_theme_pre_set_update_themes')) {
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

        $package = isset($release['asset_url']) && is_string($release['asset_url']) && $release['asset_url'] !== '' ? (string) $release['asset_url'] : (isset($release['zipball_url']) ? (string) $release['zipball_url'] : '');
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
if (!$wsbase_use_puc) {
    add_filter('pre_set_site_transient_update_themes', 'wsbase_github_theme_pre_set_update_themes');
}

if (!$wsbase_use_puc && !function_exists('wsbase_github_theme_upgrader_source_selection')) {
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
if (!$wsbase_use_puc) {
    add_filter('upgrader_source_selection', 'wsbase_github_theme_upgrader_source_selection', 10, 4);
}

if (!$wsbase_use_puc && !function_exists('wsbase_github_theme_themes_api')) {
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
        $info->download_link = isset($release['asset_url']) && is_string($release['asset_url']) && $release['asset_url'] !== '' ? (string) $release['asset_url'] : (isset($release['zipball_url']) ? (string) $release['zipball_url'] : '');
        $info->sections = array(
            'description' => isset($release['body']) && $release['body'] !== '' ? (string) $release['body'] : '',
        );

        return $info;
    }
}
if (!$wsbase_use_puc) {
    add_filter('themes_api', 'wsbase_github_theme_themes_api', 10, 3);
}

if (!function_exists('wsbase_add_admin_page')) {
    function wsbase_add_admin_page()
    {
        add_theme_page(
            'WsBase',
            'WsBase',
            'manage_options',
            'wsbase',
            'wsbase_render_admin_page'
        );
    }
}
add_action('admin_menu', 'wsbase_add_admin_page');

if (!function_exists('wsbase_handle_admin_refresh')) {
    function wsbase_handle_admin_refresh()
    {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }

        $page = isset($_GET['page']) ? sanitize_key((string) $_GET['page']) : '';
        $refresh = isset($_GET['refresh']) ? sanitize_key((string) $_GET['refresh']) : '';
        if ($page !== 'wsbase' || $refresh !== '1') {
            return;
        }

        check_admin_referer('wsbase_refresh_dashboard');
        delete_site_transient('wsbase_github_latest_release');
        delete_site_transient('update_themes');
        delete_site_transient('wsbase_github_latest_release_' . md5(strtolower('Websweet-Studio/sweetaddons')));
        wsbase_admin_set_notice('success', 'Data update diperbarui.');
        wp_safe_redirect(wsbase_admin_page_url());
        exit;
    }
}
add_action('admin_init', 'wsbase_handle_admin_refresh');

if (!function_exists('wsbase_render_admin_page')) {
    function wsbase_render_admin_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $theme = wp_get_theme(get_template());
        $currentVersion = $theme && $theme->exists() ? (string) $theme->get('Version') : '';

        $release = wsbase_github_theme_get_latest_release('https://api.github.com/repos/Websweet-Studio/wsbase/releases/latest');
        $latestTag = isset($release['tag_name']) ? (string) $release['tag_name'] : '';
        $latestVersion = $latestTag !== '' ? ltrim($latestTag, "vV \t\n\r\0\x0B") : '';

        $hasUpdate = ($currentVersion !== '' && $latestVersion !== '' && version_compare($latestVersion, $currentVersion, '>'));
        $releaseUrl = isset($release['html_url']) ? (string) $release['html_url'] : 'https://github.com/Websweet-Studio/wsbase/releases';
        $publishedAt = isset($release['published_at']) ? (string) $release['published_at'] : '';
        $body = isset($release['body']) ? (string) $release['body'] : '';

        $notice = wsbase_admin_get_notice();
        $welcome = isset($_GET['welcome']) ? sanitize_key((string) $_GET['welcome']) : '';
        $updated = isset($_GET['updated']) ? sanitize_key((string) $_GET['updated']) : '';

        $sweetRepo = 'Websweet-Studio/sweetaddons';
        $sweetRelease = wsbase_github_repo_get_latest_release($sweetRepo, 'sweetaddons');
        $sweetTag = isset($sweetRelease['tag_name']) ? (string) $sweetRelease['tag_name'] : '';
        $sweetLatest = $sweetTag !== '' ? ltrim($sweetTag, "vV \t\n\r\0\x0B") : '';
        $sweetUrl = isset($sweetRelease['html_url']) && is_string($sweetRelease['html_url']) && $sweetRelease['html_url'] !== '' ? (string) $sweetRelease['html_url'] : 'https://github.com/Websweet-Studio/sweetaddons/releases';
        $sweetStatus = wsbase_plugin_status_by_dir('sweetaddons');
        $sweetInstalled = isset($sweetStatus['installed']) ? (bool) $sweetStatus['installed'] : false;
        $sweetActive = isset($sweetStatus['active']) ? (bool) $sweetStatus['active'] : false;
        $sweetInstalledVer = isset($sweetStatus['version']) ? (string) $sweetStatus['version'] : '';

        $storeStatus = wsbase_plugin_status_by_dir('wp-store');
        $storeInstalled = isset($storeStatus['installed']) ? (bool) $storeStatus['installed'] : false;
        $storeActive = isset($storeStatus['active']) ? (bool) $storeStatus['active'] : false;
        $storeInstalledVer = isset($storeStatus['version']) ? (string) $storeStatus['version'] : '';
        $storeFile = isset($storeStatus['file']) ? (string) $storeStatus['file'] : '';

        $statusTxt = $hasUpdate ? 'Update tersedia' : 'Sudah terbaru';
        $statusBadge = $hasUpdate ? 'wp-store-badge-yellow' : 'wp-store-badge-green';

        $sweetBadge = !$sweetInstalled ? 'wp-store-badge-red' : ($sweetActive ? 'wp-store-badge-green' : 'wp-store-badge-yellow');
        $sweetState = !$sweetInstalled ? 'Belum terpasang' : ($sweetActive ? 'Terpasang & aktif' : 'Terpasang (nonaktif)');

        $storeBadge = !$storeInstalled ? 'wp-store-badge-red' : ($storeActive ? 'wp-store-badge-green' : 'wp-store-badge-yellow');
        $storeState = !$storeInstalled ? 'Belum terpasang' : ($storeActive ? 'Terpasang & aktif' : 'Terpasang (nonaktif)');

        echo '<div class="wrap wp-store-wrapper">';
        echo '<div class="wp-store-header wsbase-wps-header">';
        echo '<div>';
        echo '<h1 class="wp-store-title">WsBase</h1>';
        echo '<p class="wp-store-helper">Starter theme WordPress berbasis Bootstrap 5 dengan Customizer dan builder sederhana.</p>';
        echo '</div>';
        echo '<div class="wsbase-wps-badges">';
        if ($currentVersion !== '') {
            echo '<span class="wp-store-badge wp-store-badge-blue">v' . esc_html($currentVersion) . '</span>';
        }
        echo '<span class="wp-store-badge ' . esc_attr($statusBadge) . '">' . esc_html($statusTxt) . '</span>';
        $refreshUrl = wp_nonce_url(wsbase_admin_page_url(['refresh' => 1]), 'wsbase_refresh_dashboard');
        echo '<a class="wp-store-btn wp-store-btn-secondary" href="' . esc_url($refreshUrl) . '">Refresh</a>';
        echo '</div>';
        echo '</div>';

        if ($welcome === '1') {
            echo '<div class="notice notice-success is-dismissible"><p>WsBase berhasil diaktifkan.</p></div>';
        }
        if ($updated === '1') {
            echo '<div class="notice notice-success is-dismissible"><p>Update WsBase berhasil.</p></div>';
        }
        if (is_array($notice) && !empty($notice['message'])) {
            $type = isset($notice['type']) ? (string) $notice['type'] : 'info';
            $cls = 'notice notice-info';
            if ($type === 'success') $cls = 'notice notice-success';
            if ($type === 'warning') $cls = 'notice notice-warning';
            if ($type === 'error') $cls = 'notice notice-error';
            echo '<div class="' . esc_attr($cls) . ' is-dismissible"><p>' . esc_html($notice['message']) . '</p></div>';
        }

        echo '<div class="wp-store-card">';
        echo '<div class="wp-store-dashboard-grid">';

        echo '<div class="wp-store-card">';
        echo '<div class="wp-store-card-title">Versi Terpasang</div>';
        echo '<div class="wp-store-card-value">' . esc_html($currentVersion ?: '-') . '</div>';
        echo '<div class="wp-store-card-desc">Versi theme saat ini</div>';
        echo '</div>';

        echo '<div class="wp-store-card">';
        echo '<div class="wp-store-card-title">Versi Terbaru</div>';
        echo '<div class="wp-store-card-value">' . esc_html($latestVersion ?: '-') . '</div>';
        echo '<div class="wp-store-card-desc">Dari GitHub Release</div>';
        echo '</div>';

        echo '<div class="wp-store-card">';
        echo '<div class="wp-store-card-title">WP Store</div>';
        echo '<div class="wp-store-card-value">' . esc_html($storeInstalled ? ($storeActive ? 'Aktif' : 'Nonaktif') : '-') . '</div>';
        $storeDesc = $storeInstalledVer !== '' ? ('Terpasang: ' . $storeInstalledVer) : ($storeInstalled ? 'Terpasang' : 'Belum terpasang');
        echo '<div class="wp-store-card-desc">' . esc_html($storeDesc) . '</div>';
        echo '</div>';

        echo '<div class="wp-store-card">';
        echo '<div class="wp-store-card-title">SweetAddons</div>';
        echo '<div class="wp-store-card-value">' . esc_html($sweetInstalled ? ($sweetActive ? 'Aktif' : 'Nonaktif') : '-') . '</div>';
        echo '<div class="wp-store-card-desc">';
        echo esc_html($sweetLatest ? ('Terbaru: ' . $sweetLatest) : 'Cek rilis terbaru');
        if ($hasUpdate && $latestVersion !== '') {
            echo '<br>';
            echo esc_html('Update WsBase: ' . $latestVersion);
        }
        echo '</div>';
        if ($hasUpdate) {
            $disabled = $hasUpdate ? '' : 'disabled';
            echo '<div style="margin-top:10px;display:flex;gap:10px;flex-wrap:wrap;">';
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            echo '<input type="hidden" name="action" value="wsbase_run_update">';
            wp_nonce_field('wsbase_run_update');
            echo '<button class="wp-store-btn wp-store-btn-primary" type="submit" ' . $disabled . '>Update</button>';
            echo '</form>';
            echo '<a class="wp-store-btn wp-store-btn-secondary" href="' . esc_url($releaseUrl) . '" target="_blank" rel="noopener">Rilis</a>';
            echo '</div>';
        }
        echo '</div>';

        echo '</div>';

        echo '<div style="margin-top:16px;">';
        echo '<div class="wp-store-box">';
        echo '<div class="wp-store-box-header">Rekomendasi Plugin</div>';
        echo '<div class="wsbase-wps-body">';
        echo '<div style="display:flex;flex-direction:column;gap:16px;">';

        echo '<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">';
        echo '<div>';
        echo '<div style="font-weight:700;color:#1d2327;">WP Store</div>';
        echo '<div class="wp-store-helper">Plugin toko online (catalog, cart, checkout) untuk WsBase.</div>';
        echo '</div>';
        echo '<div class="wsbase-wps-badges">';
        echo '<span class="wp-store-badge ' . esc_attr($storeBadge) . '">' . esc_html($storeState) . '</span>';
        echo '</div>';
        echo '</div>';

        echo '<div class="wsbase-wps-actions" style="padding:0;">';
        if (!$storeInstalled) {
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            echo '<input type="hidden" name="action" value="wsbase_install_wp_store">';
            wp_nonce_field('wsbase_install_wp_store');
            echo '<button class="wp-store-btn wp-store-btn-primary" type="submit">Install</button>';
            echo '</form>';
            echo '<a class="wp-store-btn wp-store-btn-secondary" href="' . esc_url(admin_url('plugins.php')) . '">Kelola Plugin</a>';
        } elseif ($storeInstalled && !$storeActive && $storeFile !== '') {
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            echo '<input type="hidden" name="action" value="wsbase_activate_wp_store">';
            wp_nonce_field('wsbase_activate_wp_store');
            echo '<button class="wp-store-btn wp-store-btn-primary" type="submit">Aktifkan</button>';
            echo '</form>';
        } elseif ($storeInstalled && $storeActive) {
            echo '<button class="wp-store-btn wp-store-btn-secondary" type="button" disabled>Aktif</button>';
            echo '<a class="wp-store-btn wp-store-btn-secondary" href="' . esc_url(admin_url('admin.php?page=wp-store-settings')) . '">Pengaturan</a>';
        } else {
            echo '<a class="wp-store-btn wp-store-btn-secondary" href="' . esc_url(admin_url('plugins.php')) . '">Kelola Plugin</a>';
        }
        echo '</div>';

        echo '<div style="height:1px;background:#e5e7eb;"></div>';

        echo '<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">';
        echo '<div>';
        echo '<div style="font-weight:700;color:#1d2327;">SweetAddons</div>';
        echo '<div class="wp-store-helper">Addons & utilitas pendukung WsBase.</div>';
        echo '</div>';
        echo '<div class="wsbase-wps-badges">';
        if ($sweetLatest !== '') {
            echo '<span class="wp-store-badge wp-store-badge-blue">v' . esc_html($sweetLatest) . '</span>';
        }
        echo '<span class="wp-store-badge ' . esc_attr($sweetBadge) . '">' . esc_html($sweetState) . '</span>';
        echo '</div>';
        echo '</div>';

        echo '<div class="wsbase-wps-actions" style="padding:0;">';
        if (!$sweetInstalled) {
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            echo '<input type="hidden" name="action" value="wsbase_install_sweetaddons">';
            wp_nonce_field('wsbase_install_sweetaddons');
            echo '<button class="wp-store-btn wp-store-btn-primary" type="submit">Install</button>';
            echo '</form>';
            echo '<a class="wp-store-btn wp-store-btn-secondary" href="' . esc_url(admin_url('plugins.php')) . '">Kelola Plugin</a>';
        } elseif (!$sweetActive) {
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            echo '<input type="hidden" name="action" value="wsbase_activate_sweetaddons">';
            wp_nonce_field('wsbase_activate_sweetaddons');
            echo '<button class="wp-store-btn wp-store-btn-primary" type="submit">Aktifkan</button>';
            echo '</form>';
        } else {
            echo '<button class="wp-store-btn wp-store-btn-secondary" type="button" disabled>Aktif</button>';
        }
        echo '<a class="wp-store-btn wp-store-btn-secondary" href="' . esc_url($sweetUrl) . '" target="_blank" rel="noopener">Lihat Rilis</a>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('wsbase_admin_post_activate_wp_store')) {
    function wsbase_admin_post_activate_wp_store()
    {
        if (!current_user_can('activate_plugins')) {
            wp_die('Forbidden', 403);
        }
        check_admin_referer('wsbase_activate_wp_store');

        $status = wsbase_plugin_status_by_dir('wp-store');
        $file = isset($status['file']) ? (string) $status['file'] : '';
        if ($file === '') {
            wsbase_admin_set_notice('error', 'WP Store belum terpasang.');
            wp_safe_redirect(wsbase_admin_page_url());
            exit;
        }

        if (!function_exists('activate_plugin')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $result = activate_plugin($file);
        if (is_wp_error($result)) {
            wsbase_admin_set_notice('error', $result->get_error_message());
        } else {
            wsbase_admin_set_notice('success', 'WP Store berhasil diaktifkan.');
        }

        wp_safe_redirect(wsbase_admin_page_url());
        exit;
    }
}
add_action('admin_post_wsbase_activate_wp_store', 'wsbase_admin_post_activate_wp_store');

if (!function_exists('wsbase_admin_post_check_updates')) {
    function wsbase_admin_post_check_updates()
    {
        if (!current_user_can('update_themes')) {
            wp_die('Forbidden', 403);
        }
        check_admin_referer('wsbase_check_updates');
        wp_update_themes();
        wsbase_admin_set_notice('success', 'Cek update selesai.');
        wp_safe_redirect(wsbase_admin_page_url(['checked' => 1]));
        exit;
    }
}
add_action('admin_post_wsbase_check_updates', 'wsbase_admin_post_check_updates');

if (!function_exists('wsbase_admin_post_run_update')) {
    function wsbase_admin_post_run_update()
    {
        if (!current_user_can('update_themes')) {
            wp_die('Forbidden', 403);
        }
        check_admin_referer('wsbase_run_update');

        wp_update_themes();
        $themeSlug = get_template();
        $updates = get_site_transient('update_themes');
        $has = is_object($updates) && isset($updates->response) && is_array($updates->response) && isset($updates->response[$themeSlug]);
        if (!$has) {
            wsbase_admin_set_notice('info', 'Tidak ada update untuk WsBase.');
            wp_safe_redirect(wsbase_admin_page_url());
            exit;
        }

        if (!function_exists('request_filesystem_credentials')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        if (!class_exists('Theme_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }

        $skin = new Automatic_Upgrader_Skin();
        $upgrader = new Theme_Upgrader($skin);
        $result = $upgrader->upgrade($themeSlug);

        if (is_wp_error($result)) {
            wsbase_admin_set_notice('error', $result->get_error_message());
        } elseif ($result === false) {
            wsbase_admin_set_notice('error', 'Update gagal. Coba jalankan update dari halaman Themes atau Update Core.');
        } else {
            wsbase_admin_set_notice('success', 'Update sedang diproses.');
        }

        wp_safe_redirect(wsbase_admin_page_url());
        exit;
    }
}
add_action('admin_post_wsbase_run_update', 'wsbase_admin_post_run_update');

if (!function_exists('wsbase_admin_post_install_sweetaddons')) {
    function wsbase_admin_post_install_sweetaddons()
    {
        if (!current_user_can('install_plugins')) {
            wp_die('Forbidden', 403);
        }
        check_admin_referer('wsbase_install_sweetaddons');

        $status = wsbase_plugin_status_by_dir('sweetaddons');
        if (isset($status['installed']) && $status['installed']) {
            wsbase_admin_set_notice('info', 'SweetAddons sudah terpasang.');
            wp_safe_redirect(wsbase_admin_page_url());
            exit;
        }

        $release = wsbase_github_repo_get_latest_release('Websweet-Studio/sweetaddons', 'sweetaddons');
        $package = isset($release['asset_url']) && is_string($release['asset_url']) && $release['asset_url'] !== '' ? (string) $release['asset_url'] : (isset($release['zipball_url']) ? (string) $release['zipball_url'] : '');
        if ($package === '') {
            wsbase_admin_set_notice('error', 'Gagal mengambil paket instalasi SweetAddons.');
            wp_safe_redirect(wsbase_admin_page_url());
            exit;
        }

        if (!class_exists('Plugin_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }
        if (!function_exists('wp_clean_plugins_cache')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $skin = new Automatic_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader($skin);
        $result = $upgrader->install($package);

        if (is_wp_error($result)) {
            wsbase_admin_set_notice('error', $result->get_error_message());
        } elseif ($result === false) {
            wsbase_admin_set_notice('error', 'Install SweetAddons gagal.');
        } else {
            wsbase_admin_set_notice('success', 'SweetAddons berhasil diinstall. Silakan aktifkan.');
        }

        wp_safe_redirect(wsbase_admin_page_url());
        exit;
    }
}
add_action('admin_post_wsbase_install_sweetaddons', 'wsbase_admin_post_install_sweetaddons');

if (!function_exists('wsbase_admin_post_install_wp_store')) {
    function wsbase_admin_post_install_wp_store()
    {
        if (!current_user_can('install_plugins')) {
            wp_die('Forbidden', 403);
        }
        check_admin_referer('wsbase_install_wp_store');

        $status = wsbase_plugin_status_by_dir('wp-store');
        if (isset($status['installed']) && $status['installed']) {
            wsbase_admin_set_notice('info', 'WP Store sudah terpasang.');
            wp_safe_redirect(wsbase_admin_page_url());
            exit;
        }

        $release = wsbase_github_repo_get_latest_release('Websweet-Studio/wp-store', 'wp-store');
        $package = isset($release['asset_url']) && is_string($release['asset_url']) && $release['asset_url'] !== '' ? (string) $release['asset_url'] : (isset($release['zipball_url']) ? (string) $release['zipball_url'] : '');
        if ($package === '') {
            wsbase_admin_set_notice('error', 'Gagal mengambil paket instalasi WP Store.');
            wp_safe_redirect(wsbase_admin_page_url());
            exit;
        }

        if (!class_exists('Plugin_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }
        if (!function_exists('wp_clean_plugins_cache')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $skin = new Automatic_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader($skin);
        $result = $upgrader->install($package);

        if (is_wp_error($result)) {
            wsbase_admin_set_notice('error', $result->get_error_message());
        } elseif ($result === false) {
            wsbase_admin_set_notice('error', 'Install WP Store gagal.');
        } else {
            wsbase_admin_set_notice('success', 'WP Store berhasil diinstall. Silakan aktifkan.');
        }

        wp_safe_redirect(wsbase_admin_page_url());
        exit;
    }
}
add_action('admin_post_wsbase_install_wp_store', 'wsbase_admin_post_install_wp_store');

if (!function_exists('wsbase_admin_post_activate_sweetaddons')) {
    function wsbase_admin_post_activate_sweetaddons()
    {
        if (!current_user_can('activate_plugins')) {
            wp_die('Forbidden', 403);
        }
        check_admin_referer('wsbase_activate_sweetaddons');

        $status = wsbase_plugin_status_by_dir('sweetaddons');
        $file = isset($status['file']) ? (string) $status['file'] : '';
        if ($file === '') {
            wsbase_admin_set_notice('error', 'SweetAddons belum terpasang.');
            wp_safe_redirect(wsbase_admin_page_url());
            exit;
        }

        if (!function_exists('activate_plugin')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $result = activate_plugin($file);
        if (is_wp_error($result)) {
            wsbase_admin_set_notice('error', $result->get_error_message());
        } else {
            wsbase_admin_set_notice('success', 'SweetAddons berhasil diaktifkan.');
        }

        wp_safe_redirect(wsbase_admin_page_url());
        exit;
    }
}
add_action('admin_post_wsbase_activate_sweetaddons', 'wsbase_admin_post_activate_sweetaddons');

if (!function_exists('wsbase_after_switch_theme')) {
    function wsbase_after_switch_theme()
    {
        add_option('wsbase_activation_redirect', 1);
    }
}
add_action('after_switch_theme', 'wsbase_after_switch_theme');

if (!function_exists('wsbase_upgrader_process_complete_redirect')) {
    function wsbase_upgrader_process_complete_redirect($upgrader, $hook_extra)
    {
        if (!is_array($hook_extra)) {
            return;
        }
        $themeSlug = get_template();
        $updated = false;
        if (isset($hook_extra['theme']) && $hook_extra['theme'] === $themeSlug) {
            $updated = true;
        }
        if (!$updated && isset($hook_extra['themes']) && is_array($hook_extra['themes']) && in_array($themeSlug, $hook_extra['themes'], true)) {
            $updated = true;
        }
        if ($updated) {
            set_transient('wsbase_update_redirect', 1, 10 * MINUTE_IN_SECONDS);
        }
    }
}
add_action('upgrader_process_complete', 'wsbase_upgrader_process_complete_redirect', 10, 2);

if (!function_exists('wsbase_admin_redirects')) {
    function wsbase_admin_redirects()
    {
        if (!is_admin() || wp_doing_ajax()) {
            return;
        }
        if (!current_user_can('manage_options')) {
            return;
        }
        if (get_option('wsbase_activation_redirect')) {
            delete_option('wsbase_activation_redirect');
            wp_safe_redirect(wsbase_admin_page_url(['welcome' => 1]));
            exit;
        }
        if (get_transient('wsbase_update_redirect')) {
            delete_transient('wsbase_update_redirect');
            wp_safe_redirect(wsbase_admin_page_url(['updated' => 1]));
            exit;
        }
    }
}
add_action('admin_init', 'wsbase_admin_redirects');
