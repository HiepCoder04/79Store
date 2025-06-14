# Hướng dẫn thiết lập dự án Laravel mới clone về

Chào mừng bạn đến với dự án Laravel! Dưới đây là các bước chi tiết để thiết lập và chạy dự án sau khi clone từ repository.

## Yêu cầu hệ thống

Trước khi bắt đầu, hãy đảm bảo máy tính của bạn đã cài đặt các công cụ sau:

- **PHP**: Phiên bản 8.1 trở lên
- **Composer**: Quản lý gói phụ thuộc cho PHP
- **MySQL** hoặc cơ sở dữ liệu tương thích (PostgreSQL, SQLite, v.v.)
- **Node.js** và **NPM** (cho việc biên dịch tài nguyên frontend, nếu cần)
- **Git**: Để clone repository

## Các bước thiết lập

1. **Clone repository**

   ```bash
   git clone https://github.com/codegaeme/laravel_shop
   cd laravel_shop
   ```

2. **Cài đặt các gói phụ thuộc PHP**Chạy lệnh sau để cài đặt các thư viện PHP được yêu cầu:

   ```bash
   composer install
   ```

3. **Sao chép file môi trường**Sao chép file `.env.example` để tạo file `.env`:

   ```bash
   cp .env.example .env
   ```

4. **Tạo khóa ứng dụng**Tạo khóa ứng dụng Laravel bằng lệnh:

   ```bash
   php artisan key:generate
   ```

5. **Cấu hình cơ sở dữ liệu**

   - Mở file `.env` và cập nhật thông tin cơ sở dữ liệu:

     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=<TÊN_CSDL>
     DB_USERNAME=<TÊN_NGƯỜI_DÙNG>
     DB_PASSWORD=<MẬT_KHẨU>
     ```

   - Đảm bảo cơ sở dữ liệu đã được tạo trước trong MySQL hoặc hệ quản trị cơ sở dữ liệu bạn sử dụng.

6. **Chạy migration**Chạy lệnh để tạo các bảng trong cơ sở dữ liệu:

   ```bash
   php artisan migrate
   ```


7. **Khởi động server**Chạy server phát triển của Laravel:

   ```bash
   php artisan serve
   ```

   Mặc định, ứng dụng sẽ chạy tại `http://localhost:8000`.

## Lưu ý

- Nếu bạn gặp lỗi liên quan đến quyền, hãy đảm bảo cấp quyền phù hợp cho các thư mục như `storage` và `bootstrap/cache`:

  ```bash
  chmod -R 775 storage bootstrap/cache
  ```

- Nếu dự án sử dụng các dịch vụ bên thứ ba (như mail, queue, v.v.), hãy kiểm tra và cập nhật cấu hình trong file `.env`.

## Các lệnh hữu ích

- Xóa cache nếu gặp lỗi:

  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  ```

- Tối ưu hóa dự án:

  ```bash
  php artisan optimize
```

## Giải quyết vấn đề

- Nếu gặp lỗi liên quan đến Composer, thử xóa thư mục `vendor` và file `composer.lock`, sau đó chạy lại `composer install`.
- Kiểm tra version PHP và Composer để đảm bảo tương thích với dự án.

Chúc bạn thành công với dự án Laravel! Nếu có thắc mắc, hãy liên hệ với nhóm phát triển.
