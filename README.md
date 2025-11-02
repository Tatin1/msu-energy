
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

# ⚡ MSU-IIT Energy Monitoring System (Laravel + IoT + Firebase)

A Laravel-based **Energy Monitoring Dashboard** designed for **MSU-IIT**, connected to **IoT smart meters** via **Firebase Realtime Database**.

It displays:
- Real-time power readings  
- Building-specific status and consumption  
- Historical and system logs  
- Interactive map visualization of the MSU-IIT campus  

---

## 🧩 Features

- 🔌 Real-time energy data visualization  
- 🧠 Interactive campus map with building power status  
- 📊 Dynamic graphs using Chart.js  
- 🗂️ System and building logs with export options  
- ☁️ Firebase integration for IoT data streaming  
- 🧱 Modular Laravel architecture (per-page Blade views)

---

## ⚙️ Project Setup

```bash
# Clone this repository
git clone https://github.com/yourusername/energy-monitoring-system.git
cd energy-monitoring-system

# Install dependencies
composer install
npm install && npm run dev

# Create your .env file
cp .env.example .env

# Generate app key
php artisan key:generate
````

---

## 🗄️ Database Setup (MySQL + Migrations)

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

## 🔥 IoT Firebase Integration Guide

This system is **IoT-ready** — it connects Laravel with your **Firebase Realtime Database** to stream energy data from IoT devices (e.g., ESP32 or Raspberry Pi).

### 1️⃣ Firebase Setup

1. Go to [Firebase Console](https://console.firebase.google.com)
2. Create a **new project** (e.g., `msu-iit-energy`)
3. Add a **Realtime Database**
4. Copy your credentials and add them to `.env`:

```env
FIREBASE_API_KEY=YOUR_API_KEY
FIREBASE_DB_URL=https://your-project-id.firebaseio.com
FIREBASE_PROJECT_ID=your-project-id
```

---

### 2️⃣ Firebase Rules (Security)

Paste the following into your Firebase **Realtime Database → Rules** tab:

```json
{
  "rules": {
    ".read": false,
    ".write": false,

    "system_summary": {
      ".read": "auth != null",
      ".write": "auth != null && auth.token.device === true"
    },
    "building_status": {
      ".read": true,
      ".write": "auth != null && auth.token.device === true"
    },
    "graph_data": {
      ".read": true,
      ".write": "auth != null && auth.token.device === true"
    },
    "transformer_logs": {
      ".read": true,
      ".write": "auth != null && auth.token.device === true"
    },
    "system_logs": {
      ".read": true,
      ".write": "auth != null && auth.token.device === true"
    },
    "building_data": {
      ".read": true,
      ".write": "auth != null && auth.token.device === true"
    },
    "system_data": {
      ".read": true,
      ".write": "auth != null && auth.token.device === true"
    }
  }
}
```

🧠 Explanation:

* **IoT devices** authenticate with a Firebase Auth token that includes a `"device": true` claim.
* **Laravel dashboard** can **read** but not **write** to the DB.
* Protects your database from unauthorized access.

---

### 3️⃣ IoT Device Example (ESP32/Arduino)

```cpp
#include <Firebase_ESP_Client.h>

FirebaseData fbdo;
FirebaseAuth auth;
FirebaseConfig config;

config.api_key = "YOUR_FIREBASE_API_KEY";
auth.user.email = "iot_coe_meter@msuiit.edu.ph";
auth.user.password = "iot_secure_pass";
config.database_url = "https://your-project-id.firebaseio.com/";

Firebase.begin(&config, &auth);

// Example: Sending data to Firebase
Firebase.RTDB.setFloat(&fbdo, "system_summary/totalPower", 1234.56);
Firebase.RTDB.setFloat(&fbdo, "building_status/COE/power", 456.78);
```

✅ Secure device → Firebase write
✅ Laravel → Firebase read

---

### 4️⃣ Real-Time Updates (Frontend)

Add this snippet to any Blade view (e.g., `graphs.blade.php` or `map.blade.php`):

```html
<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.3/firebase-app.js";
  import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/10.12.3/firebase-database.js";

  const firebaseConfig = {
    apiKey: "YOUR_FIREBASE_API_KEY",
    databaseURL: "https://your-project-id.firebaseio.com",
    projectId: "your-project-id"
  };

  const app = initializeApp(firebaseConfig);
  const db = getDatabase(app);

  // Listen for live COE updates
  onValue(ref(db, 'graph_data/COE'), (snapshot) => {
    const data = snapshot.val();
    console.log("Live COE data:", data);
    // Update chart dynamically
  });
</script>
```

---

## 🧱 File Overview

```
energy-monitoring-system/
├── app/
│   ├── Http/Controllers/DashboardController.php
│   ├── Models/
│
├── resources/views/pages/
│   ├── home.blade.php          # Dashboard
│   ├── map.blade.php           # Interactive map
│   ├── graphs.blade.php        # Chart display
│   ├── tables.blade.php        # Logs & tables
│   ├── history.blade.php       # System & building data
│   ├── view.blade.php          # View preferences
│
├── public/images/msu-iit-map.jpg
├── routes/web.php
├── .env
└── README.md
```

---

## 🚀 Ready for IoT Data Flow

**IoT → Firebase → Laravel Dashboard (Real-time)**

1. Devices push readings to Firebase paths like:

   * `system_summary/`
   * `building_data/COE/`
   * `system_logs/`
2. Laravel frontend listens for updates using `onValue()` and updates charts, map, or tables live.
3. Future backend versions can sync Firebase → MySQL for historical archiving.

---

## 🧠 Laravel Info

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

* [Simple, fast routing engine](https://laravel.com/docs/routing).
* [Powerful dependency injection container](https://laravel.com/docs/container).
* Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
* Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
* Database agnostic [schema migrations](https://laravel.com/docs/migrations).
* [Robust background job processing](https://laravel.com/docs/queues).
* [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

---

## License

The Laravel framework and this project are open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

````

---

Would you like me to append a **Firebase initialization helper file** (`resources/js/firebase.js`) mentioned in the guide, so you can just import it in every Blade view with a single line like:  
```html
<script type="module" src="{{ asset('js/firebase.js') }}"></script>
````

This will make your real-time connection modular and cleaner.
