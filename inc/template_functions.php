<?php
/**
 * Custom hooks
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wsbase_site_info' ) ) {
	/**
	 * Add site info hook to WP hook library.
	 */
	function wsbase_site_info() {
		do_action( 'wsbase_site_info' );
	}
}

if ( ! function_exists( 'wsbase_add_site_info' ) ) {
	/**
	 * Add site info content.
	 */
	function wsbase_add_site_info() {
		$the_theme = wp_get_theme();
		$year 	= date( 'Y' );
		$site_title = get_bloginfo( 'name' );
		$site_info =  "Copyright $year &copy; $site_title. All rights reserved | Powered by <a href='https://websweetstudio.com/'>websweetstudio.com</a>";

		// Check if customizer site info has value.
		if ( get_theme_mod( 'wsbase_site_info_override' ) ) {
			$site_info = get_theme_mod( 'wsbase_site_info_override' );
		}

		$site_info = '<div class="text-center">'.$site_info.'</div>';

		echo apply_filters( 'wsbase_site_info_content', $site_info ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
}

if( ! function_exists( 'wsbase_add_navbar' ) ) {
	/**
	 * Add navbar.
	 */
	function wsbase_add_navbar() {
		$header_position   = get_theme_mod( 'wsbase_header_position', 'position-relative' );
		?>

		<header id="wrapper-navbar" class="<?php echo $header_position; ?> bg-white shadow-light">

			<a class="visually-hidden-focusable" href="#content"><?php esc_html_e( 'Skip to content', 'wsbase' ); ?></a>

			<?php 
				do_action( 'wsbase_navbar' );
			?>

		</header><!-- #wrapper-navbar end -->

		<?php
	}
}

if( ! function_exists( 'wsbase_add_footer' ) ) {
	/**
	 * Add footer.
	 */
	function wsbase_add_footer() {
		$container = get_theme_mod( 'wsbase_container_type' );
		?>
		<div class="wrapper-footer" id="wrapper-footer">
			<footer class="site-footer" id="colophon">
				<div class="<?php echo esc_attr( $container ); ?> py-3">
					<div class="site-info">
						<?php wsbase_site_info(); ?>
					</div><!-- .site-info -->
				</div><!-- container end -->
			</footer><!-- #colophon -->
		</div><!-- wrapper end -->
		<?php
	}
}

if (!function_exists('wsbase_color_scheme')) {
	/**
	 * Membuat color scheme.
	 *
	 * @return array
	 */
	function wsbase_color_scheme()
	{
		$color_scheme = isset($_COOKIE["color_scheme"]) ? $_COOKIE["color_scheme"] : 'light';
		echo 'data-bs-theme="' . $color_scheme . '"';
	}
}

if (!function_exists('wsbase_navbar_collapse')) {
	/**
	 * Navbar Collapse
	 *
	 * @return array
	 */
	function wsbase_navbar_collapse()
	{
		$container = get_theme_mod( 'wsbase_container_type' );
		?>
		
		<nav id="main-nav" class="navbar navbar-expand-md navbar-light py-3" aria-labelledby="main-nav-label">

			<h2 id="main-nav-label" class="screen-reader-text">
				<?php esc_html_e( 'Main Navigation', 'wsbase' ); ?>
			</h2>

			<div class="<?php echo esc_attr( $container ); ?>">

				<!-- Your site title as branding in the menu -->
				<?php if ( ! has_custom_logo() ) { ?>

					<?php if ( is_front_page() && is_home() ) : ?>

						<h1 class="navbar-brand mb-0"><a rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a></h1>

					<?php else : ?>

						<h2 class="navbar-brand mb-0"><a rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a></h2>

					<?php endif; ?>

					<?php
				} else {
					the_custom_logo();
				}
				?>
				<!-- end custom logo -->

				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'wsbase' ); ?>">
					<span class="navbar-toggler-icon"></span>
				</button>

				<!-- The WordPress Menu goes here -->
				<?php
				wp_nav_menu(
					array(
						'theme_location'  => 'primary',
						'container_class' => 'collapse navbar-collapse',
						'container_id'    => 'navbarNavDropdown',
						'menu_class'      => 'navbar-nav ms-auto',
						'fallback_cb'     => '',
						'menu_id'         => 'main-menu',
						'depth'           => 2,
						'walker'          => new wsbase_WP_Bootstrap_Navwalker(),
					)
				);
				?>

			</div><!-- .container(-fluid) -->

		</nav><!-- .site-navigation -->
		<?php
	}
}

if (!function_exists('wsbase_navbar_offcanvas')) {
	/**
	 * Navbar Off Canvas
	 *
	 * @return array
	 */
	function wsbase_navbar_offcanvas()
	{
		$container = get_theme_mod('wsbase_container_type');
		?>
		
		<nav id="main-nav" class="navbar navbar-expand-md navbar-light py-3" aria-labelledby="main-nav-label">
		
			<h2 id="main-nav-label" class="screen-reader-text">
				<?php esc_html_e('Main Navigation', 'wsbase'); ?>
			</h2>
		
		
			<div class="<?php echo esc_attr($container); ?>">
		
				<!-- Your site title as branding in the menu -->
				<?php if (!has_custom_logo()) { ?>
		
					<?php if (is_front_page() && is_home()) : ?>
		
						<h1 class="navbar-brand mb-0"><a rel="home" href="<?php echo esc_url(home_url('/')); ?>" itemprop="url"><?php bloginfo('name'); ?></a></h1>
		
					<?php else : ?>
		
						<h2 class="navbar-brand mb-0"><a rel="home" href="<?php echo esc_url(home_url('/')); ?>" itemprop="url"><?php bloginfo('name'); ?></a></h2>
		
					<?php endif; ?>
		
				<?php
				} else {
					the_custom_logo();
				}
				?>
				<!-- end custom logo -->
		
				<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarNavOffcanvas" aria-controls="navbarNavOffcanvas" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'wsbase'); ?>">
					<span class="navbar-toggler-icon"></span>
				</button>
		
				<div class="offcanvas offcanvas-end" tabindex="-1" id="navbarNavOffcanvas">
		
					<div class="offcanvas-header justify-content-end">
						<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
					</div><!-- .offcancas-header -->
		
					<!-- The WordPress Menu goes here -->
					<?php
					wp_nav_menu(
						array(
							'theme_location'  => 'primary',
							'container_class' => 'offcanvas-body justify-content-end',
							'container_id'    => '',
							'menu_class'      => 'navbar-nav justify-content-end d-flex flex-wrap justify-content-end',
							'fallback_cb'     => '',
							'menu_id'         => 'main-menu',
							'depth'           => 2,
							'walker'          => new wsbase_WP_Bootstrap_Navwalker(),
						)
					);
					?>
				</div><!-- .offcanvas -->
		
			</div><!-- .container(-fluid) -->
		
		</nav><!-- .site-navigation -->
		<?php
	}
}

if (!function_exists('wsbase_skip_link')) {
    /**
     * Skip Link
     */
    function wsbase_skip_link() {
        echo '<a class="skip-link screen-reader-text" href="#content">Skip to content</a>';
    }

    function wsbase_add_skip_link() {
        add_action('wp_body_open', 'wsbase_skip_link');
    }

    add_action('wp_enqueue_scripts', 'wsbase_add_skip_link');
}