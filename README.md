# WsBase

WsBase is a simple starter theme for WordPress that offers a clean and minimalist design, making it both user-friendly and highly customizable. This theme comes with the added support and enhancements provided by [WebSweetStudio.com](https://websweetstudio.com/).

## Installation

Install manually to your theme directory.

- Download or clone the repository from [GitHub](https://github.com/websweetstudio/wsbase.git)
- Copy the contents of the `wsbase` folder to your `wp-content/themes` folder.
- Activate the theme.

## Development

```bash
npm install
```

```bash
npm run dist
```

```bash
npm run build
```

```bash
npm run package
```

## Features

- Clean and simple theme.
- Easy to use and easy to customize.
- Responsive design.
- Customizer support.
- Header & Footer Builder (Customizer).
- Beaver Builder support.
- WooCommerce support.
- Bootstrap 5 support.

## Auto Release (GitHub)

This repository includes an auto-release workflow that creates a new GitHub Release when the theme version is higher than the latest release tag.

- Workflow: `.github/workflows/auto-release.yml`
- Trigger: push to `main` or `master`
- Steps: `npm ci` → `npm run package` → upload `dist/wsbase-v{version}.zip` as a new Release

To publish a new release:

- Bump `version` in `package.json`
- Bump `Version` in `style.css`
- Push to GitHub

## Credits

- Bootstrap 5: [https://getbootstrap.com](https://getbootstrap.com)
- WooCommerce: [https://woocommerce.com](https://woocommerce.com)
- WP Bootstrap Navwalker by Edward McIntyre & William Patton: [https://github.com/wp-bootstrap/wp-bootstrap-navwalker](https://github.com/wp-bootstrap/wp-bootstrap-navwalker)

## License

WsBase is distributed under the GNU General Public License v3.0 (GPL-3.0). Feel free to modify and share the theme in accordance with the license terms.

## Demo

To see WsBase in action, you can visit the demo site at [https://template.sweet.web.id/](https://template.sweet.web.id/).
Feel free to explore the theme and discover its clean design and customizable features. Happy website building!
