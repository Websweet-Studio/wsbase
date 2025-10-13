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
		// Auto-detect Beaver Builder (BB + UAB + Themer)
		if (class_exists('FLBuilder') && class_exists('FLThemeBuilderLoader')) {
			// Silently hide theme navbar - let Themer take control
			return;
		}

		// Elementor fallback
		if (function_exists('elementor_theme_do_location')) {
			if (elementor_theme_do_location('header')) {
				return;
			}
		}

		// Default theme header (untuk non-builder sites)
		$header_position   = get_theme_mod( 'wsbase_header_position', 'position-relative' );
		?>

		<header id="wrapper-navbar" class="<?php echo $header_position; ?> bg-white">

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
		// Auto-detect Beaver Builder (BB + UAB + Themer)
		if (class_exists('FLBuilder') && class_exists('FLThemeBuilderLoader')) {
			// Silently hide theme footer - let Themer take control
			return;
		}

		// Elementor fallback
		if (function_exists('elementor_theme_do_location')) {
			if (elementor_theme_do_location('footer')) {
				return;
			}
		}

		// Default theme footer (untuk non-builder sites)
		$container = get_theme_mod( 'wsbase_container_type' );
		?>
		<div class="wrapper-footer" id="wrapper-footer">
			<footer class="site-footer" id="colophon">

				<!-- Footer main content -->
				<div class="footer-main py-5 bg-light">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="row">
							<!-- About section -->
							<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
								<div class="footer-about">
									<?php if ( ! has_custom_logo() ) : ?>
										<h3 class="footer-brand mb-3">
											<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
												<?php bloginfo( 'name' ); ?>
											</a>
										</h3>
									<?php else : ?>
										<div class="footer-logo mb-3">
											<?php the_custom_logo(); ?>
										</div>
									<?php endif; ?>

									<p class="footer-description text-muted">
										<?php
										$footer_description = get_theme_mod( 'wsbase_footer_description', get_bloginfo( 'description' ) );
										echo esc_html( $footer_description );
										?>
									</p>
								</div>
							</div>

							<!-- Quick links -->
							<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
								<h4 class="footer-title h5 mb-3">Quick Links</h4>
								<?php
								wp_nav_menu(
									array(
										'theme_location' => 'footer',
										'container' => false,
										'menu_class' => 'footer-menu list-unstyled',
										'depth' => 1,
										'fallback_cb' => false,
									)
								);
								?>
							</div>

							<!-- Contact info -->
							<div class="col-lg-4 col-md-12">
								<h4 class="footer-title h5 mb-3">Contact Info</h4>
								<div class="footer-contact">
									<?php
									$footer_contact = get_theme_mod( 'wsbase_footer_contact', '' );
									if ( $footer_contact ) :
										echo '<div class="contact-info text-muted">' . wp_kses_post( $footer_contact ) . '</div>';
									endif;
									?>

									<!-- Social links -->
									<div class="social-links mt-3">
										<?php
										$social_links = array(
											'facebook' => get_theme_mod( 'wsbase_facebook', '#' ),
											'twitter' => get_theme_mod( 'wsbase_twitter', '#' ),
											'instagram' => get_theme_mod( 'wsbase_instagram', '#' ),
											'linkedin' => get_theme_mod( 'wsbase_linkedin', '#' ),
										);

										foreach ( $social_links as $platform => $url ) :
											if ( $url && $url !== '#' ) :
												?>
												<a href="<?php echo esc_url( $url ); ?>"
												   class="social-link me-2 text-muted"
												   target="_blank"
												   rel="noopener noreferrer"
												   aria-label="<?php echo esc_attr( ucfirst( $platform ) ); ?>">
													<i class="fab fa-<?php echo esc_attr( $platform ); ?>"></i>
												</a>
												<?php
											endif;
										endforeach;
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Footer bottom -->
				<div class="footer-bottom py-3 bg-white border-top">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="row align-items-center">
							<div class="col-md-6">
								<div class="copyright text-muted small">
									<?php wsbase_site_info(); ?>
								</div>
							</div>
							<div class="col-md-6 text-md-end">
								<div class="footer-bottom-links">
									<?php
									wp_nav_menu(
										array(
											'theme_location' => 'footer-bottom',
											'container' => false,
											'menu_class' => 'footer-bottom-menu list-unstyled list-inline mb-0 small',
											'depth' => 1,
											'fallback_cb' => false,
										)
									);
									?>
								</div>
							</div>
						</div>
					</div>
				</div>

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