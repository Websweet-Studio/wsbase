# Space Grotesk + Inter Font Integration - UPDATED

## Konsep Font System

### ✅ Space Grotesk untuk:

- **Headings** (h1-h6) dengan font-weight 600
- **Entry titles** (.entry-title, .page-title, .comments-title)
- **Navigation** (.navbar-nav .nav-link, .navbar-brand) dengan font-weight 500
- **Dropdown menu** dengan font-weight 400

### ✅ Inter untuk:

- **Body text** dengan font-weight 400 (mudah dibaca dan modern)
- **Konten artikel** (.entry-content)
- **Form elements** (button, input, select, textarea)
- **Widget content**

## Pengaturan yang Sudah Diterapkan

### 1. Customizer WordPress

- **Site Typography**: Default = Space Grotesk (untuk headings & nav)
- **Body Typography**: Default = Inter (untuk konten)
- Dapat diubah di: `Appearance > Customize > Typography Setting`

### 2. Google Fonts Loading

```php
// Space Grotesk untuk headings & navigation
'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap'

// Inter untuk body text
'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap'
```

### 3. CSS Styling

```css
/* Body menggunakan Inter */
body {
	font-family: "Inter", sans-serif;
	font-weight: 400;
}

/* Headings menggunakan Space Grotesk */
h1,
h2,
h3,
h4,
h5,
h6,
.entry-title,
.page-title,
.comments-title {
	font-family: "Space Grotesk", sans-serif;
	font-weight: 600;
}

/* Navigation menggunakan Space Grotesk */
.navbar-nav .nav-link,
.navbar-brand,
.navbar-brand a {
	font-family: "Space Grotesk", sans-serif;
	font-weight: 500;
}
```

## File yang Dimodifikasi

1. **`inc/customizer.php`** - Menambahkan Inter, mengubah default body ke Inter
2. **`src/sass/theme/_ws.scss`** - CSS untuk dual font system
3. **`inc/enqueue.php`** - Loading kedua Google Fonts

## Hasil Akhir

- ✅ **Entry titles** = Space Grotesk (Modern, Bold)
- ✅ **Navigation/Menu** = Space Grotesk (Konsisten dengan headings)
- ✅ **Body content** = Inter (Mudah dibaca, clean)
- ✅ **Optimal contrast** antara headings dan konten
- ✅ **Performance optimized** dengan display=swap

## Build Process

```bash
npm run css
```

## Penggunaan di WordPress Admin

1. Login ke WordPress Admin
2. `Appearance > Customize > Typography Setting`
3. **Site Typography**: Space Grotesk
4. **Body Typography**: Inter
5. Publish changes
