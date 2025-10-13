# Space Grotesk Font Integration

## Perubahan yang Dilakukan

### 1. Customizer Integration

- Menambahkan **Space Grotesk** ke daftar font yang tersedia di WordPress Customizer
- Mengubah default font untuk headings dan body menjadi Space Grotesk
- Font dapat diubah melalui: `Appearance > Customize > Typography Setting`

### 2. Menu & Navigation Styling

- Menerapkan Space Grotesk khusus untuk:
  - `.navbar-nav .nav-link` (menu items)
  - `.navbar-brand` (brand/logo text)
  - `.navbar-brand a` (brand links)
  - `.navbar-nav .dropdown-menu .nav-link` (dropdown menu items)

### 3. Font Weights

- **Menu items**: font-weight 500 (medium) untuk keterbacaan yang lebih baik
- **Dropdown items**: font-weight 400 (regular)
- **Tersedia weights**: 300, 400, 500, 600, 700

### 4. Google Fonts Loading

- Font Space Grotesk dimuat dari Google Fonts dengan optimisasi `display=swap`
- URL: `https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap`

## File yang Dimodifikasi

1. **`inc/customizer.php`**

   - Menambahkan Space Grotesk ke array font choices
   - Mengubah default font settings
   - Menambahkan CSS khusus untuk menu

2. **`src/sass/theme/_ws.scss`**

   - Menambahkan styling khusus untuk navbar dan menu
   - Memastikan konsistensi font family di seluruh navigasi

3. **`inc/enqueue.php`**
   - Menambahkan enqueue untuk Google Fonts Space Grotesk

## Cara Menggunakan

1. **Melalui Customizer** (Recommended):

   - Masuk ke `Appearance > Customize > Typography Setting`
   - Pilih "Space Grotesk" untuk Site Typography dan Body Typography

2. **Manual Override** (via CSS):
   ```css
   .navbar-nav .nav-link,
   .navbar-brand {
   	font-family: "Space Grotesk", sans-serif;
   }
   ```

## Build Process

Setelah mengubah SCSS, jalankan:

```bash
npm run css
```

Ini akan mengompilasi SCSS menjadi CSS dan menerapkan perubahan.
