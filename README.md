# Hệ Thống Tuyển Dụng Thực Tập Sinh Trực Tuyến - AZ Media (AZHR)

Hệ thống Tuyển dụng & Quản lý Đánh giá Năng lực Thực tập sinh Mini-ATS & Workflow trực tuyến cho Doanh nghiệp AZ Media (Công ty TNHH Đầu tư Phát triển Doanh nghiệp AZ Homes).

---

## 🐳 Chạy bằng Docker và triển khai VPS

Stack Docker gồm PHP-FPM, Nginx và MySQL 8.4. Dữ liệu MySQL, file Laravel và các file tải lên được giữ trong Docker volumes; database không mở cổng trực tiếp ra Internet. Image ứng dụng build asset frontend và chỉ cài Composer dependency production.

### Chạy local

1. Sao chép `.env.docker.example` thành `.env`, tạo `APP_KEY` bằng `php artisan key:generate --show`, rồi đặt `APP_KEY`, `DB_PASSWORD` và `DB_ROOT_PASSWORD` trong `.env`. Dùng mật khẩu riêng cho môi trường local.
2. Đặt `APP_IMAGE=azmedia-app:local`, `NGINX_IMAGE=azmedia-nginx:local`, `APP_URL=http://localhost:8080`, `HTTP_BIND=0.0.0.0`, `HTTP_PORT=8080` trong `.env`.
3. Build và chạy (Compose tự nạp `docker-compose.override.yml` khi chạy local):

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --force
```

Mở `http://localhost:8080`. Override gắn mã nguồn hiện tại vào container và bật debug; không dùng file override này trên production.

Giao diện quản trị MySQL local nằm tại `http://localhost:8081` (phpMyAdmin). Đăng nhập với server `db`, username và password lấy từ `DB_USERNAME` / `DB_PASSWORD` trong `.env`. Cổng MySQL `3307` dành cho MySQL Workbench/HeidiSQL, không mở trực tiếp bằng trình duyệt. phpMyAdmin và cổng DB chỉ bind vào localhost trong override local.

### Triển khai VPS / CI/CD

1. Cài Docker Engine và Docker Compose plugin trên VPS. Sao chép `.env.docker.example` thành `.env`, điền domain, `APP_KEY` được tạo an toàn, database name/user và mật khẩu mạnh riêng biệt; giữ `APP_DEBUG=false`. Không commit hoặc đưa `.env` vào image.
2. Đặt `APP_IMAGE` và `NGINX_IMAGE` thành image tags bất biến do CI build/push (ví dụ tag commit SHA). Cấp quyền registry cho VPS nếu image private. Dùng TLS reverse proxy trên host và giữ `HTTP_BIND=127.0.0.1`; proxy tới `127.0.0.1:8080`. Nếu phục vụ HTTP trực tiếp, cấu hình firewall/TLS trước khi mở port ra Internet.
3. CI build và push hai target của Dockerfile với các giá trị image tương ứng (nên gắn tag theo commit SHA để rollback chính xác):

```bash
docker buildx build --target app --tag "$APP_IMAGE" --push .
docker buildx build --target nginx --tag "$NGINX_IMAGE" --push .
```

VPS chỉ dùng file Compose chính để không nạp override local:

```bash
docker compose -f docker-compose.yml pull
docker compose -f docker-compose.yml up -d
docker compose -f docker-compose.yml run --rm app php artisan migrate --force
```

Trước lần đầu chạy migration, có thể khôi phục `webazhr.sql` vào database `db` nếu cần dữ liệu hiện có; không import dump rồi chạy migrations một cách mù quáng nếu chưa kiểm tra bảng `migrations`. Sao lưu database và volume uploads trước khi nâng cấp. Không chạy `docker compose down -v` trên production vì lệnh đó xóa các volume dữ liệu.

Kiểm tra trạng thái bằng `docker compose -f docker-compose.yml ps` và log bằng `docker compose -f docker-compose.yml logs -f app nginx db`. MySQL chỉ truy cập trong mạng Docker; không publish port 3306.

---

## 🌟 Các Phân Hệ Chính

### 1. Phân hệ Ứng viên (Careers & Candidate Portal)
- **Careers Landing Page (`/` hoặc `/careers`):** Giới thiệu văn hóa công ty, chế độ đãi ngộ, môi trường "Thầy giỏi - Bạn tốt - Sách hay", triết lý AZ Media và danh sách các vị trí tuyển dụng thực tập sinh đang mở (lọc theo Chuyên ngành, Hình thức, Địa điểm).
- **Nộp hồ sơ trực tuyến (`/careers` modal):** Ứng viên điền thông tin, chọn vị trí (hoặc tự điền vị trí khác), tải lên CV PDF.
- **Quy trình Đánh giá Năng lực 4 phần (`/candidate/test/{code}`):**
  1. *Phần 1: Đánh giá Năng lực & Tính cách (NLB / NLP, DISC, MBTI, Thần số học đường đời).*
  2. *Phần 2: Bánh xe cuộc đời (Đánh giá mức độ hài lòng trên thang điểm 1-10).*
  3. *Phần 3: Hoài bão, Động cơ & Kế hoạch phát triển bản thân (Cam kết, tự luận).*
  4. *Phần 4: Hiểu biết về Văn hóa & Doanh nghiệp AZ Media (Trắc nghiệm + Tự luận cảm nhận).*
- **Xuất PDF Bài Test:** Hệ thống tự động biên dịch kết quả bài test thành file PDF hoàn chỉnh chuẩn báo cáo cho Admin/Mentor đánh giá.
- **Tra cứu kết quả hồ sơ (`/candidate/track`):** Tra cứu theo mã ứng viên hoặc email.

### 2. Phân hệ Quản trị Tuyển dụng (Mini-ATS & Workflow)
- **Đăng nhập quản trị (`/portal/login`):**
  - Tài khoản mặc định: `admin@gmail.com` / `123456789` hoặc `admin@azmedia.com.vn` / `123456789`
- **Dashboard & Analytics (`/portal/dashboard`):** Thống kê số lượng vị trí tuyển, ứng viên đã nộp, ứng viên đã hoàn thành test, tỷ lệ đạt/trúng tuyển.
- **Quản lý Ứng viên (`/portal/applications` & `/portal/kanban`):**
  - Pipeline trạng thái: Mới nộp ➔ Đã làm test ➔ Đã duyệt hồ sơ ➔ Hẹn phỏng vấn ➔ Đạt/Mời nhận việc ➔ Từ chối.
  - Xem chi tiết hồ sơ, mở xem CV trực tuyến, xem/tải file PDF bài test trực tiếp.
  - Phân công Mentor phỏng vấn (Admin, Lê Đình Quang, Người phụ trách tự điền tên).
  - Tự động gửi Email thông báo kết quả / lịch phỏng vấn với các biến động `{candidate_name}`, `{position_title}`, `{interview_time}`, `{interview_location}`, `{mentor_name}`.
- **Quản lý Vị trí Tuyển dụng (`/portal/positions`):** Thêm, sửa, xóa, bật/tắt trạng thái mở tuyển.
- **Ngân hàng Đề Test (`/portal/questions`):** Quản lý câu hỏi trắc nghiệm, thêm mới, sửa câu hỏi và đáp án, xóa câu hỏi.

---

## 🛠️ Yêu Cầu Cài Đặt & Môi Trường

- **PHP:** >= 8.1 (khuyên dùng PHP 8.2)
- **Web Server:** Apache (XAMPP) hoặc `php artisan serve`
- **Database:** MySQL / MariaDB (XAMPP)
- **Composer** & **Node.js**

---

## 🚀 Hướng Dẫn Cài Đặt Nhanh

### 1. Clone dự án
```bash
git clone https://github.com/phanduy02102004-sketch/webazhr.git
cd webazhr
```

### 2. Cài đặt thư viện
```bash
composer install
```

### 3. Cấu hình cơ sở dữ liệu (.env)
Sao chép file `.env.example` thành `.env`:
```bash
cp .env.example .env
php artisan key:generate
```
Cấu hình thông số MySQL trong file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laptrinhwebnangcao
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Import Cơ Sở Dữ Liệu
Cơ sở dữ liệu hoàn chỉnh đã được xuất sẵn tại file **`webazhr.sql`** (và `database/webazhr.sql`).
- Mở **phpMyAdmin** (`http://localhost/phpmyadmin`).
- Tạo cơ sở dữ liệu mới có tên: **`laptrinhwebnangcao`**.
- Chọn tab **Import** và chọn file **`webazhr.sql`** rồi bấm **Go / Thực hiện**.

Hoặc import qua dòng lệnh MySQL:
```bash
mysql -u root -p laptrinhwebnangcao < webazhr.sql
```

### 5. Khởi chạy máy chủ
```bash
php artisan serve
```
Truy cập:
- **Trang chủ tuyển dụng:** [http://127.0.0.1:8000](http://127.0.0.1:8000)
- **Trang quản trị ATS:** [http://127.0.0.1:8000/portal](http://127.0.0.1:8000/portal)
