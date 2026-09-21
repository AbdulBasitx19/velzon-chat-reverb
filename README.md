# 💬 Velzon Real-Time Chat & RBAC System

A production-ready, real-time chat application built with **Laravel 11**, **Laravel Reverb**, and the beautiful **Velzon Admin Theme**. This project extends a robust Role-Based Access Control (RBAC) foundation with a modern, WhatsApp-like chat interface featuring real-time messaging and seamless file sharing.

---

## ✨ Key Features

- 🔐 **Robust RBAC**: Built-in Spatie Laravel Permission for granular role and user management.
- ⚡ **Real-Time Messaging**: Powered by **Laravel Reverb** (First-party WebSocket server) and **Pusher.js** (CDN, no NPM/Echo required).
- 📎 **Advanced File Sharing**: Support for Images, Videos, and Documents with intelligent storage routing:
  - **Public Disk**: Fast, direct browser rendering for Images & Videos.
  - **Private Disk**: Secure, authorized downloads for sensitive Documents (PDF, DOCX, ZIP, etc.).
- 🎨 **Modern UI**: Fully responsive Flexbox layout, custom scrollbars, video preview modals, and attachment previews.
- 🛡️ **Security**: XSS protection, CSRF token validation, and strict channel authorization (`routes/channels.php`).
- 📊 **DataTables**: Server-side paginated user lists using Yajra DataTables.

---

## 📦 IMPORTANT: Download Theme Assets

Due to GitHub's file size limits, the Velzon theme's static assets are not included in this repository.

🔗 **Google Drive Link:** [Velzon Theme Assets](https://drive.google.com/drive/folders/1m_QJfs4-TQ0vzx1bCQw_AeKkJceOPkSG?usp=sharing)

**Setup Instructions:**
1. Download the `assets` folder from the Drive link.
2. Place it directly inside the `public/` directory.
   - **Correct Path:** `your-project/public/assets/`

---

## 🚀 Installation & Setup

### 1. Clone the Repository
```bash
git clone https://github.com/AbdulBasitx19/velzon-chat-reverb.git
cd velzon-chat-reverbs.
```
### 2. Install Dependencies
```bash
composer install
```

### 3. Setup Environment:
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup:
Update your .env file with your database credentials:

DB_CONNECTION=mysql
DB_DATABASE=velzon_chat
DB_USERNAME=root
DB_PASSWORD=

# Reverb / Pusher Configuration
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="127.0.0.1"
REVERB_PORT=8081
REVERB_SCHEME=http

PUSHER_APP_ID="${REVERB_APP_ID}"
PUSHER_APP_KEY="${REVERB_APP_KEY}"
PUSHER_APP_SECRET="${REVERB_APP_SECRET}"
PUSHER_HOST="${REVERB_HOST}"
PUSHER_PORT="${REVERB_PORT}"
PUSHER_SCHEME="${REVERB_SCHEME}"
PUSHER_APP_CLUSTER=mt1


### 5. Run Migrations & Seeders:

# Create tables (users, messages, message_attachments, permissions)
```bash
php artisan migrate
```

# Seed default roles, permissions, and a Super Admin user
```bash
php artisan db:seed --class=RolePermissionSeeder
```

### 6. Create Storage Link (Crucial for File Uploads):
```bash
php artisan storage:link
```

### 7. Download Assets
Follow the "Download Theme Assets" instructions above.

### 8. Running the Application
# Terminal 1: HTTP Web Server (Frontend & API)
```bash
php artisan serve --port=8000
```
# Terminal 2: WebSocket Server (Real-Time Broadcasting)
```bash
php artisan reverb:start --port=8081 --debug
```


### Project Structure Highlights
app/
├── Events/
│   ├── MessageSent.php          # Broadcasts text messages
│   └── FileSent.php             # Broadcasts messages with attachments
├── Http/Controllers/
│   ├── Admin/
│   │   ├── RoleController.php   # RBAC Management
│   │   └── UserController.php   # User Management
│   └── ChatController.php       # Chat UI, message history, file upload/download
└── Models/
    ├── Message.php              # Includes hasMany(attachments) relationship
    └── MessageAttachment.php    # Handles file metadata and storage paths

database/
└── migrations/
    ├── ..._create_messages_table.php
    └── ..._create_message_attachments_table.php

resources/views/
└── chat/
    └── index.blade.php          # Complete Chat UI with Pusher.js & Vanilla JS


### 📄 License
This project is built for educational and portfolio purposes. The Velzon theme is subject to its original licensing terms by Themesbrand.
Built with ❤️ using Laravel 11, Reverb, Spatie Permission, and clean MVC practices.



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



### Event (message) file :
constrcut : Jab hum controller se event(new MessageSent($message)) likhenge ya fire kreingy, toh yeh constructor call hoga
broadcastOn : kis channel pr broadcast krna hy
broadcastAs :  isk andar event ka custom naam hai jo frontend (Pusher.js) use karega -> .bind('message.sent', ...)
broadcastWith : // is k andar  data frontend ko bheja jayega

