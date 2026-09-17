# 🛡️ Velzon RBAC App - Laravel Role & Permission Management

A complete, production-ready Role-Based Access Control (RBAC) system built with **Laravel**, **Spatie Laravel Permission**, and the beautiful **Velzon Admin Theme**. 

This project demonstrates a full-stack implementation of user management, role assignment, and granular permission control using AJAX, Yajra DataTables, and Laravel's native authentication.

---

## ✨ Key Features

- 🔐 **Manual Authentication**: Custom Login/Logout logic with secure session handling.
- 👥 **User Management**: Full CRUD (Create, Read, Update, Delete) for users via AJAX.
- 🛡️ **Role Management**: Create, edit, and delete roles dynamically.
- 🔑 **Granular Permissions**: Assign specific permissions to roles using Spatie's `syncPermissions()`.
- 🔄 **Dynamic Role Assignment**: Assign multiple roles to users on the fly using `syncRoles()`.
- 📊 **Server-Side DataTables**: Fast, paginated, and searchable tables using Yajra DataTables.
- 🎨 **Velzon Admin UI**: Fully integrated with the premium Velzon Bootstrap 5 admin template.
- 🛠️ **Custom Database Schema**: Extended default `users` table with `username` and `phone_num`.

---

## 📦 IMPORTANT: Download Theme Assets

Due to GitHub's file size limits, the Velzon theme's static assets are hosted externally.

🔗 **Google Drive Link:** [Velzon Theme Assets](https://drive.google.com/drive/folders/1m_QJfs4-TQ0vzx1bCQw_AeKkJceOPkSG?usp=sharing)

**Setup Instructions:**
1. Download the `assets` folder from the Drive link.
2. Place it directly inside the `public/` directory.
   - **Correct Path:** `your-project/public/assets/`

---

## 🚀 Installation & Setup

### 1. Clone the Repository
```bash
git clone https://github.com/AbdulBasitx19/velzon-rbac-app.git
cd velzon-rbac-app
```
### 2. Install Dependencies:
```bash
composer install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
Update your .env file with your database credentials:
    DB_CONNECTION=mysql
    DB_DATABASE=velzon_rbac
    DB_USERNAME=root
    DB_PASSWORD=
    SESSION_DRIVER=database

Run migrations to create the tables (including Spatie's custom permission tables):
```bash
php artisan migrate
```

### 5. Seed Roles & Permissions
```bash
php artisan db:seed --class=RolePermissionSeeder
```

### 7. Download Assets
Follow the "Download Theme Assets" instructions above.

### 8. Start the Server
```bash
php artisan serve
```

### 📂 Project Structure Highlights
app/
├── Http/Controllers/
│   ├── LoginController.php          # Handles Auth logic
│   └── Admin/
│       ├── RoleController.php       # Handles Roles & Permissions CRUD
│       └── UserController.php       # Handles Users CRUD & Role Assignment
└── Models/
    └── User.php                     # Extended with Spatie's HasRoles trait

database/
├── migrations/
│   ├── ..._create_users_table.php   # Customized with username & phone_num
│   └── ..._create_permission_tables.php # Spatie's migration with custom columns
└── seeders/
    └── RolePermissionSeeder.php     # Seeds default roles, permissions, and admin user

resources/views/
├── auth/                            # Login UI
├── layouts/                         # Master layout, Sidebar, Topbar
└── users/
    ├── role.blade.php               # Role Management UI (DataTables + Modals)
    └── index.blade.php              # User Management UI (DataTables + Modals)

### 📄 License
This project is built for educational and portfolio purposes. The Velzon theme is subject to its original licensing terms by Themesbrand.

Built with ❤️ using Laravel, Spatie Permission, Yajra DataTables, and clean MVC practices.



### Chat system
## Reverb :
 aek server hy jispr ham real time communicate krtay hein
## event : 
jab controller msg server pr store krta hy toh controller sath main aek event genrate krta hy 
## broadcast:
reverb pr event aata hy toh woh usko aagay( bger mangay ) bhej deta hy kisi channel pr (push)
## 1. public_channel :
 yeh woh channel hy jaha pr har koi broadcasted data ko sun ry hotay hein 
## 2. private_channel:
 only authenticated user hee channel ko join kr k us channel pr aye broadcasted data ko sun skta hy  
## pusher cdn :
 browser reverb sy aye msg ko sunnay k liay push cdn use krta hy
## .bind() method :
 jab pusher kisi channel pr jurr jata hy, toh .bind() use krtay hein .
 pusher.bind('MessageSent', function(data) { ... }).
 Means Hey Pusher, jab bhi is channel par 'MessageSent' naam ka event aaye, toh yeh JavaScript function chala dena (jo message ko screen par dikhayega)
