
<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---

# ⚡ MSU-IIT Energy Monitoring System (Laravel + MySQL + Vite)

A **Laravel-based Energy Monitoring Dashboard** designed for **MSU-IIT**, providing real-time visualization of power consumption across campus buildings.  
Built with **Laravel 11**, **MySQL**, and **Vite + TailwindCSS**, the system helps monitor, record, and analyze energy usage efficiently.

---

## 🧩 Features

- 📊 **Real-time energy dashboard** with building-level metrics  
- 🗂️ **Logs and reports** for system and building data  
- 🌍 **Interactive map** of MSU-IIT building locations  
- 🎨 **Modern frontend** built with Vite and TailwindCSS  
- 🧱 **Clean MVC structure** for scalable Laravel development  

---

## ⚙️ Local Development Setup (Laravel Herd)

> 🐑 Using [Laravel Herd](https://herd.laravel.com) for local PHP development is recommended for best performance.

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/yourusername/energy-monitoring-system.git
cd energy-monitoring-system
````

### 2️⃣ Install Backend Dependencies

```bash
composer install
```

### 3️⃣ Install Frontend Dependencies

```bash
npm install
npm run dev
```

### 4️⃣ Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Then open `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=energy_monitoring
DB_USERNAME=root
DB_PASSWORD=
```

If you’re using **Herd**, MySQL runs automatically on port `3306`.

---

## 🗄️ Database Setup (Using TablePlus + MySQL)

You can manage and visualize your database easily using [TablePlus](https://tableplus.com).

### 1️⃣ Create a Database

1. Open **TablePlus**
2. Connect to your **MySQL** server (Herd or manual connection)
3. Create a new database:

   ```
   energy_monitoring
   ```

### 2️⃣ Run Migrations and Seeders

```bash
php artisan migrate
php artisan db:seed
```

Tables created:

* `buildings`
* `building_data`
* `system_data`
* `transformer_logs`
* `system_logs`
* `users`

---

## 💻 Running the Application

### If Using Laravel Herd

Once Herd is installed and configured, your app will be accessible at:

```
https://energy-monitoring-system.test
```

### If Using Artisan

You can also run it manually:

```bash
php artisan serve
```

Visit your app at:

```
http://127.0.0.1:8000
```

---

## 🎨 Frontend Setup (Vite + TailwindCSS)

This project uses **Vite** for lightning-fast builds and **TailwindCSS** for modern UI design.

### 1️⃣ Development Build (Hot Reload)

```bash
npm run dev
```

### 2️⃣ Production Build

```bash
npm run build
```

The compiled assets will be stored in:

```
/public/build/
```

---

## 🧱 Folder Structure Overview

### 🗂️ Backend (Laravel MVC)

```
app/
├── Http/
│   └── Controllers/
│       └── DashboardController.php
├── Models/
resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   └── pages/
│       ├── home.blade.php          # Dashboard
│       ├── map.blade.php           # Building map
│       ├── graphs.blade.php        # Graphs and charts
│       ├── tables.blade.php        # Logs
│       ├── history.blade.php       # Historical records
│       └── view.blade.php          # Preferences view
routes/
├── web.php
```

### 🎨 Frontend (Vite + TailwindCSS)

```
resources/
├── css/
│   └── app.css            # TailwindCSS entry
├── js/
│   ├── app.js             # Main JS entry
│   ├── components/        # Optional custom JS modules
│   └── charts/            # Chart.js scripts
vite.config.js             # Vite configuration
tailwind.config.js         # Tailwind config
```

### 🗄️ Database Files

```
database/
├── migrations/
├── seeders/
└── factories/
```

---

## 🧠 Developer Notes

* **Laravel 11** framework
* **MySQL** for persistent data storage
* **Vite + TailwindCSS** for fast and modern frontend
* **TablePlus** for database management
* **Blade templates** for server-side rendering
* **MVC pattern** for maintainability and scalability

---

## 🧰 Recommended Tools

| Purpose               | Tool                                                      |
| --------------------- | --------------------------------------------------------- |
| Local PHP Development | [Laravel Herd](https://herd.laravel.com)                  |
| Database Management   | [TablePlus](https://tableplus.com)                        |
| Frontend Build Tool   | [Vite](https://vitejs.dev)                                |
| CSS Framework         | [TailwindCSS](https://tailwindcss.com)                    |
| Code Editor           | [Visual Studio Code](https://code.visualstudio.com)       |
| Version Control       | [Git](https://git-scm.com) + [GitHub](https://github.com) |

---

## 🧾 Example `.env` File

Here’s an example configuration for your local setup:

```env
APP_NAME="MSU-IIT Energy Monitoring System"
APP_ENV=local
APP_KEY=base64:GENERATED_KEY_HERE
APP_DEBUG=true
APP_URL=https://energy-monitoring-system.test

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=energy_monitoring
DB_USERNAME=root
DB_PASSWORD=

VITE_APP_NAME="${APP_NAME}"
```

---

## 🚀 Deployment Notes

For production deployment:

1. Run `composer install --optimize-autoloader --no-dev`
2. Run `npm run build`
3. Set `APP_ENV=production` and `APP_DEBUG=false`
4. Configure your `.env` for production MySQL credentials
5. Use a web server like **Nginx** or **Apache** to serve `/public`

---

## 🪄 Quick Start Summary

```bash
# 1. Clone project
git clone https://github.com/yourusername/energy-monitoring-system.git

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup
php artisan migrate --seed

# 5. Run app
php artisan serve
npm run dev
```

Access the system at:

```
http://127.0.0.1:8000
```

---

## 🧠 About Laravel

Laravel is a web application framework with expressive, elegant syntax that simplifies common web development tasks such as routing, caching, sessions, and database management.
Learn more at [laravel.com](https://laravel.com).

---

## 🪪 License

This project and the Laravel framework are open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


