# Tên Project

Ứng dụng quản lý chi tiêu với Flutter frontend và Laravel backend.

---

## Yêu cầu

### Backend (Laravel)
- PHP >= 8.x
- Composer
- MySQL / MariaDB
- Git

### Frontend (Flutter)
- Flutter >= 3.x
- Dart >= 3.x
- Android Studio / Xcode
- Git

---

## Backend - Laravel

### Hướng dẫn dev

```bash
git clone https://github.com/username/backend-repo.git backend
cd backend

composer install

cp .env.example .env

change this
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

php artisan key:generate

php artisan migrate:fresh --seed

php artisan serve

```
