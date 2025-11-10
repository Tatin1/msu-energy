
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

# ⚡ MSU-IIT Energy Monitoring System  
### *(Laravel + IoT + Firebase Integration)*

A **real-time energy monitoring dashboard** built for **MSU-IIT**, powered by **Laravel**, **IoT smart meters**, and **Firebase Realtime Database**.  
This system provides **instant visualization**, **building-level monitoring**, and **historical tracking** of campus-wide power consumption.

---

## 🧩 Features

- 🔌 **Real-time** energy usage visualization  
- 🧠 **Interactive campus map** with building statuses  
- 📊 **Dynamic graphs** powered by Chart.js  
- 🗂️ **System and building logs** with export options  
- ☁️ **Firebase IoT integration** for instant data sync  
- 🧱 **Clean modular Laravel architecture**

---

## ⚙️ Installation Guide

```bash
# Clone this repository
git clone https://github.com/yourusername/energy-monitoring-system.git
cd energy-monitoring-system

# Install dependencies
composer install
npm install && npm run dev

# Copy .env file
cp .env.example .env

# Generate app key
php artisan key:generate
````

---

## 🗄️ Database Setup (MySQL)

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

## 🔥 Firebase IoT Integration

This project connects Laravel with your **Firebase Realtime Database** to receive live energy readings from IoT devices (ESP32, Raspberry Pi, etc.).

### 1️⃣ Firebase Setup

1. Go to [Firebase Console](https://console.firebase.google.com)
2. Create a **project** (e.g., `msu-iit-energy`)
3. Enable **Realtime Database**
4. Copy your credentials and paste them into `.env`:

```env
FIREBASE_API_KEY=YOUR_API_KEY
FIREBASE_DB_URL=https://your-project-id.firebaseio.com
FIREBASE_PROJECT_ID=your-project-id
```

---

### 2️⃣ Firebase Rules

Go to **Realtime Database → Rules** and paste:

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

**Explanation:**

* IoT devices (with `"device": true` claim) can write data.
* Laravel dashboard can read but **not modify** the database.
* Protects data integrity and device authentication.

---

### 3️⃣ Example: IoT Device (ESP32/Arduino)

```cpp
#include <Firebase_ESP_Client.h>

FirebaseData fbdo;
FirebaseAuth auth;
FirebaseConfig config;

config.api_key = "YOUR_FIREBASE_API_KEY";
auth.user.email = "iot_meter@msuiit.edu.ph";
auth.user.password = "iot_secure_password";
config.database_url = "https://your-project-id.firebaseio.com/";

Firebase.begin(&config, &auth);

// Example data upload
Firebase.RTDB.setFloat(&fbdo, "system_summary/totalPower", 1234.56);
Firebase.RTDB.setFloat(&fbdo, "building_status/COE/power", 456.78);
```

✅ IoT → Firebase write
✅ Laravel → Firebase read

---

## 🧠 Firebase Frontend Integration (resources/js/firebase.js)

```js
// resources/js/firebase.js
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.3/firebase-app.js";
import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/10.12.3/firebase-database.js";

const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY || "YOUR_FIREBASE_API_KEY",
  databaseURL: import.meta.env.VITE_FIREBASE_DB_URL || "https://your-project-id.firebaseio.com",
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || "your-project-id",
};

const app = initializeApp(firebaseConfig);
const db = getDatabase(app);

export function listenTo(path, callback) {
  onValue(ref(db, path), (snapshot) => {
    callback(snapshot.val());
  });
}
```

Then in your Blade files:

```html
<script type="module" src="{{ asset('js/firebase.js') }}"></script>
<script type="module">
  import { listenTo } from "/js/firebase.js";
  listenTo('graph_data/COE', (data) => {
    console.log("Live COE Data:", data);
    // Update chart dynamically
  });
</script>
```

---

## 🧱 Project Structure

```
energy-monitoring-system/
├── app/
│   ├── Http/Controllers/DashboardController.php
│   ├── Models/
│
├── resources/views/pages/
│   ├── home.blade.php          # Dashboard
│   ├── map.blade.php           # Interactive map
│   ├── graphs.blade.php        # Graph visualization
│   ├── tables.blade.php        # Logs & tables
│   ├── history.blade.php       # System & building history
│   ├── view.blade.php          # UI preferences
│
├── resources/js/firebase.js
├── public/images/msu-iit-map.jpg
├── routes/web.php
├── .env
└── README.md
```

---

## 🚀 IoT Data Flow Summary

**IoT Device → Firebase → Laravel Dashboard (Real-Time)**

1. Devices send energy readings to Firebase.
2. Laravel frontend listens via Firebase SDK (`onValue`).
3. Dashboard updates charts, tables, and maps instantly.

---

## 🧰 Built With

* Laravel 11
* Tailwind CSS
* Chart.js
* Firebase Realtime Database
* Vite
* MySQL

---

## 🪪 License

This project and the Laravel framework are open-sourced under the [MIT License](https://opensource.org/licenses/MIT).

---

## 🧑‍💻 Contributors

**Developed by:**

> Justine Boncales — MSU-IIT College of Computer Studies
> For the MSU-IIT IoT Energy Monitoring Initiative ⚡


