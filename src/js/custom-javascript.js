/**
 * Custom JavaScript for Wsbase Theme
 */

(function($) {
    'use strict';

    // Navbar scroll effect
    $(document).ready(function() {
        var navbar = $('.navbar');
        var navbarCollapse = $('.navbar-collapse');

        // Add scrolled class to navbar when page is scrolled
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                navbar.addClass('scrolled');
            } else {
                navbar.removeClass('scrolled');
            }
        });

        // Close mobile menu when clicking outside
        $(document).click(function(event) {
            if (!$(event.target).closest('.navbar').length) {
                navbarCollapse.removeClass('show');
            }
        });

        // Close mobile menu when clicking on a link
        $('.navbar-nav .nav-link').on('click', function() {
            navbarCollapse.removeClass('show');
        });

        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 80
                }, 1000);
            }
        });

        // Add hover effect to dropdown menus
        $('.dropdown').on('show.bs.dropdown', function() {
            $(this).find('.dropdown-menu').first().stop(true, true).slideDown(200);
        });

        $('.dropdown').on('hide.bs.dropdown', function() {
            $(this).find('.dropdown-menu').first().stop(true, true).slideUp(200);
        });
    });

})(jQuery);