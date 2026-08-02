<?php
defined('ABSPATH') || exit;

/**
 * Integrasi plugin WSCRM (app.websweetstudio.com):
 * - Cek update plugin dari metadata WSCRM, inject ke daftar update WP.
 * - Halaman WsBase: install/aktifkan plugin WSCRM yang belum terpasang.
 */

if (!function_exists('wsbase_wscrm_base_url')) {
    function wsbase_wscrm_base_url()
    {
        if (defined('WSBASE_WSCRM_BASE_URL')) {
            return untrailingslashit(WSBASE_WSCRM_BASE_URL);
        }
        return untrailingslashit('http://app.websweetstudio.com');
    }
}

if (!function_exists('wsbase_wscrm_fetch_plugins')) {
    function wsbase_wscrm_fetch_plugins()
    {
        $cacheKey = 'wsbase_wscrm_plugins';
        $cached = get_site_transient($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $response = wp_remote_get(wsbase_wscrm_base_url() . '/api/plugins', array(
            'timeout' => 15,
            'headers' => array('Accept' => 'application/json'),
        ));

        if (is_wp_error($response)) {
            set_site_transient($cacheKey, array(), 5 * MINUTE_IN_SECONDS);
            return array();
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            set_site_transient($cacheKey, array(), 5 * MINUTE_IN_SECONDS);
            return array();
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $plugins = (isset($data['plugins']) && is_array($data['plugins'])) ? $data['plugins'] : array();

        set_site_transient($cacheKey, $plugins, 12 * HOUR_IN_SECONDS);
        return $plugins;
    }
}

// Inject info update plugin WSCRM ke daftar update WP (halaman Plugins > Update).
if (!function_exists('wsbase_wscrm_pre_set_update_plugins')) {
    function wsbase_wscrm_pre_set_update_plugins($transient)
    {
        if (!is_object($transient)) {
            $transient = new stdClass();
        }
        if (!isset($transient->response) || !is_array($transient->response)) {
            $transient->response = array();
        }

        $remote = wsbase_wscrm_fetch_plugins();
        if (empty($remote)) {
            return $transient;
        }

        $bySlug = array();
        foreach ($remote as $p) {
            if (!empty($p['slug'])) {
                $bySlug[$p['slug']] = $p;
            }
        }

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        foreach (get_plugins() as $file => $data) {
            $folder = dirname($file);
            if (!isset($bySlug[$folder])) {
                continue;
            }
            $rp = $bySlug[$folder];
            $current = isset($data['Version']) ? (string) $data['Version'] : '0';
            $latest = isset($rp['version']) ? (string) $rp['version'] : '';
            if ($latest === '' || !version_compare($latest, $current, '>')) {
                continue;
            }

            $transient->response[$file] = array(
                'slug'        => $folder,
                'plugin'      => $file,
                'new_version' => $latest,
                'url'         => wsbase_wscrm_base_url(),
                'package'     => isset($rp['file_url']) ? $rp['file_url'] : '',
            );
        }

        return $transient;
    }
}
add_filter('pre_set_site_transient_update_plugins', 'wsbase_wscrm_pre_set_update_plugins', 20);

// Cek status plugin terpasang berdasarkan folder (slug).
if (!function_exists('wsbase_plugin_status_by_dir')) {
    function wsbase_plugin_status_by_dir($slug)
    {
        $status = array('installed' => false, 'active' => false, 'version' => '', 'file' => '');

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        foreach (get_plugins() as $file => $data) {
            if (dirname($file) === $slug) {
                $status = array(
                    'installed' => true,
                    'active'    => is_plugin_active($file),
                    'version'   => isset($data['Version']) ? (string) $data['Version'] : '',
                    'file'      => $file,
                );
                break;
            }
        }

        return $status;
    }
}

// Section "Plugin dari WSCRM" di halaman WsBase.
if (!function_exists('wsbase_render_wscrm_plugins_section')) {
    function wsbase_render_wscrm_plugins_section()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $remote = wsbase_wscrm_fetch_plugins();
        if (empty($remote)) {
            return;
        }

        echo '<div class="wp-store-box" style="margin-top:16px;">';
        echo '<div class="wp-store-box-header">Plugin dari WSCRM</div>';
        echo '<div class="wsbase-wps-body">';
        echo '<div style="display:flex;flex-direction:column;gap:16px;">';

        foreach ($remote as $p) {
            $slug = isset($p['slug']) ? sanitize_key($p['slug']) : '';
            if ($slug === '') {
                continue;
            }

            $status = wsbase_plugin_status_by_dir($slug);
            $latest = isset($p['version']) ? (string) $p['version'] : '';
            $name = isset($p['name']) ? $p['name'] : $slug;

            echo '<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">';
            echo '<div>';
            echo '<div style="font-weight:700;color:#1d2327;">' . esc_html($name) . '</div>';
            if (!empty($p['description'])) {
                echo '<div class="wp-store-helper">' . esc_html($p['description']) . '</div>';
            }
            echo '</div>';
            echo '<div class="wsbase-wps-badges">';
            echo '<span class="wp-store-badge wp-store-badge-blue">v' . esc_html($latest !== '' ? $latest : '-') . '</span>';

            if (!$status['installed']) {
                echo '<span class="wp-store-badge wp-store-badge-red">Belum terpasang</span>';
            } elseif ($status['active']) {
                echo '<span class="wp-store-badge wp-store-badge-green">Terpasang & aktif</span>';
            } else {
                echo '<span class="wp-store-badge wp-store-badge-yellow">Terpasang (nonaktif)</span>';
            }
            echo '</div>';
            echo '</div>';

            echo '<div class="wsbase-wps-actions" style="padding:0;">';
            if (!$status['installed']) {
                echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
                echo '<input type="hidden" name="action" value="wsbase_install_wscrm_plugin">';
                echo '<input type="hidden" name="plugin_slug" value="' . esc_attr($slug) . '">';
                wp_nonce_field('wsbase_install_wscrm_plugin');
                echo '<button class="wp-store-btn wp-store-btn-primary" type="submit">Install</button>';
                echo '</form>';
            } elseif (!$status['active']) {
                echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
                echo '<input type="hidden" name="action" value="wsbase_activate_wscrm_plugin">';
                echo '<input type="hidden" name="plugin_slug" value="' . esc_attr($slug) . '">';
                wp_nonce_field('wsbase_activate_wscrm_plugin');
                echo '<button class="wp-store-btn wp-store-btn-primary" type="submit">Aktifkan</button>';
                echo '</form>';
            } else {
                $ver = $status['version'];
                $outdated = ($latest !== '' && $ver !== '' && version_compare($latest, $ver, '>'));
                if ($outdated) {
                    echo '<span class="wp-store-badge wp-store-badge-yellow">Update tersedia: v' . esc_html($latest) . '</span>';
                    echo '<a class="wp-store-btn wp-store-btn-primary" href="' . esc_url(admin_url('plugins.php?plugin_status=upgrade')) . '">Update di Plugins</a>';
                } else {
                    echo '<span class="wp-store-badge wp-store-badge-green">Sudah terbaru</span>';
                }
                echo '<a class="wp-store-btn wp-store-btn-secondary" href="' . esc_url(admin_url('plugins.php')) . '">Kelola Plugin</a>';
            }
            echo '</div>';

            echo '<div style="height:1px;background:#e5e7eb;"></div>';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('wsbase_admin_post_install_wscrm_plugin')) {
    function wsbase_admin_post_install_wscrm_plugin()
    {
        if (!current_user_can('install_plugins')) {
            wp_die('Forbidden', 403);
        }
        check_admin_referer('wsbase_install_wscrm_plugin');

        $slug = isset($_POST['plugin_slug']) ? sanitize_key((string) $_POST['plugin_slug']) : '';
        $remote = wsbase_wscrm_fetch_plugins();
        $package = '';
        foreach ($remote as $p) {
            if (isset($p['slug']) && $p['slug'] === $slug && !empty($p['file_url'])) {
                $package = $p['file_url'];
                break;
            }
        }

        if ($package === '') {
            wsbase_admin_set_notice('error', 'Gagal mengambil paket plugin dari WSCRM.');
            wp_safe_redirect(wsbase_admin_page_url());
            exit;
        }

        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        $skin     = new Automatic_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader($skin);
        $result   = $upgrader->install($package);

        if (is_wp_error($result)) {
            wsbase_admin_set_notice('error', $result->get_error_message());
        } elseif ($result === false) {
            wsbase_admin_set_notice('error', 'Install plugin gagal.');
        } else {
            $status = wsbase_plugin_status_by_dir($slug);
            if (!empty($status['file'])) {
                $act = activate_plugin($status['file']);
                if (is_wp_error($act)) {
                    wsbase_admin_set_notice('warning', 'Plugin terinstall, tapi gagal diaktifkan: ' . $act->get_error_message());
                    wp_safe_redirect(wsbase_admin_page_url());
                    exit;
                }
            }
            delete_site_transient('update_plugins');
            delete_site_transient('wsbase_wscrm_plugins');
            wsbase_admin_set_notice('success', 'Plugin berhasil diinstall & diaktifkan.');
        }

        wp_safe_redirect(wsbase_admin_page_url());
        exit;
    }
}
add_action('admin_post_wsbase_install_wscrm_plugin', 'wsbase_admin_post_install_wscrm_plugin');

if (!function_exists('wsbase_admin_post_activate_wscrm_plugin')) {
    function wsbase_admin_post_activate_wscrm_plugin()
    {
        if (!current_user_can('activate_plugins')) {
            wp_die('Forbidden', 403);
        }
        check_admin_referer('wsbase_activate_wscrm_plugin');

        $slug = isset($_POST['plugin_slug']) ? sanitize_key((string) $_POST['plugin_slug']) : '';
        $status = wsbase_plugin_status_by_dir($slug);
        if (empty($status['file'])) {
            wsbase_admin_set_notice('error', 'Plugin belum terpasang.');
            wp_safe_redirect(wsbase_admin_page_url());
            exit;
        }

        $result = activate_plugin($status['file']);
        if (is_wp_error($result)) {
            wsbase_admin_set_notice('error', $result->get_error_message());
        } else {
            wsbase_admin_set_notice('success', 'Plugin berhasil diaktifkan.');
        }

        wp_safe_redirect(wsbase_admin_page_url());
        exit;
    }
}
add_action('admin_post_wsbase_activate_wscrm_plugin', 'wsbase_admin_post_activate_wscrm_plugin');
