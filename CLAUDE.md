# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WsBase is a WordPress starter theme built with Bootstrap 5. It's a clean, minimalist theme based on Underscores that provides a solid foundation for WordPress theme development with modern tooling.

## Architecture

### Core Structure
- **Root PHP files**: Standard WordPress template files (index.php, header.php, footer.php, etc.)
- **inc/**: Modular PHP includes organized by functionality
- **src/**: Source files for compilation (SASS and JavaScript)
- **css/**: Compiled CSS output
- **js/**: Compiled JavaScript output

### PHP Architecture
The theme uses a modular include system in [`functions.php`](functions.php:16-33) that loads functionality from the `inc/` directory:

- `setup.php` - Theme setup and WordPress feature support
- `enqueue.php` - Script and style loading
- `customizer.php` - Theme customizer options
- `widgets.php` - Widget area registration
- `woocommerce.php` - WooCommerce integration (loaded conditionally)
- `class-wp-bootstrap-navwalker.php` - Bootstrap navigation walker
- `template-functions.php` - Core template utilities
- `template-hooks.php` - WordPress action/filter hooks

### Asset Pipeline
- **SASS**: Located in [`src/sass/`](src/sass/) with Bootstrap 5 integration
- **JavaScript**: Located in [`src/js/`](src/js/) with Bootstrap 5 components
- **Build System**: Rollup for JS bundling, SASS compilation, PostCSS processing

## Development Commands

### Asset Development
```bash
# Install dependencies
npm install

# Watch for changes and auto-compile
npm run watch

# Watch with BrowserSync
npm run watch-bs

# Compile CSS only
npm run css

# Compile JavaScript only
npm run js

# Copy Bootstrap assets
npm run copy-assets
```

### Build & Distribution
```bash
# Compile all assets (CSS + JS)
npm run dist

# Build distribution package (creates theme zip)
npm run build

# Full package process (compile + build)
npm run package
```

### Development Server
```bash
# Start BrowserSync development server
npm run bs
```

## Key Features & Integrations

### Supported Plugins
- **WooCommerce**: Full support with dedicated styling and functionality
- **Beaver Builder**: Page builder integration
- **Contact Form 7**: Bootstrap-styled forms
- **Jetpack**: Feature integration

### Bootstrap Integration
- Bootstrap 5 grid system and components
- Custom WordPress nav walker for Bootstrap navigation
- Bootstrap-styled WordPress blocks
- Responsive design utilities

### Customization Points
- **Theme variables**: [`src/sass/theme/theme_variables.scss`](src/sass/theme/theme_variables.scss)
- **Custom styles**: [`src/sass/theme/theme.scss`](src/sass/theme/theme.scss)
- **Custom JavaScript**: [`src/js/custom-javascript.js`](src/js/custom-javascript.js)

## File Naming Conventions
- WordPress templates follow standard WP hierarchy (index.php, single.php, page.php, etc.)
- Include files use kebab-case in `inc/` directory
- SASS files use kebab-case with theme prefix for custom files
- JavaScript uses camelCase for Bootstrap integration files

## Build Process Details
The build process uses:
- **SASS** → CSS compilation with Bootstrap integration
- **PostCSS** → Autoprefixer and CSS optimization
- **Rollup** → JavaScript bundling with Babel transpilation
- **CleanCSS** → CSS minification
- **Terser** → JavaScript minification

Distribution builds create a WordPress-ready zip file in `dist/` with version naming based on `package.json` version.