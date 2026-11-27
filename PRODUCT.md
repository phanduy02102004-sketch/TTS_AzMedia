# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- Ứng viên thực tập sinh, chủ yếu là sinh viên đang tìm vị trí thực tập. Họ xem vị trí, nộp hồ sơ và CV, làm bài đánh giá, rồi tra cứu tiến độ hồ sơ qua cổng tuyển dụng công khai.
- Đội tuyển dụng AZ Media tiếp nhận hồ sơ, sàng lọc, phỏng vấn và cập nhật kết quả tập trung.
- Thực tập sinh đã được nhận sử dụng cổng riêng để xem thông tin và theo dõi nội dung liên quan đến kỳ thực tập.

## Product Purpose

Hệ thống hỗ trợ AZ Media quản lý xuyên suốt quy trình tuyển thực tập sinh: tìm vị trí, nộp hồ sơ, đánh giá, sàng lọc, phỏng vấn, quyết định và tra cứu trạng thái. Thành công là các bên có thể thực hiện và theo dõi quy trình tập trung, rõ ràng và liên tục từ ứng tuyển đến kỳ thực tập.

## Positioning

Một cổng tuyển dụng và quản lý thực tập tích hợp, kết nối trải nghiệm ứng viên, quy trình tuyển dụng nội bộ và thông tin sau khi được nhận trong cùng một hệ thống.

## Operating Context

Ứng viên sử dụng cổng công khai trên máy tính hoặc thiết bị di động để xem vị trí, gửi hồ sơ, hoàn thành bài đánh giá và tra cứu tiến độ. Đội tuyển dụng xử lý hồ sơ, sàng lọc, phỏng vấn và cập nhật trạng thái trong cổng quản trị. Thực tập sinh đã được nhận dùng cổng riêng trong thời gian thực tập.

## Capabilities and Constraints

- Bảo toàn các bước ứng tuyển, đánh giá, sàng lọc, phỏng vấn, quyết định và tra cứu trạng thái hồ sơ.
- Các thay đổi phải phù hợp với cấu trúc và luồng Laravel hiện có.
- Không làm gián đoạn cổng ứng viên, cổng quản trị hoặc cổng thực tập sinh.
- Giao diện web cần dễ dùng và tương thích trên máy tính cũng như thiết bị di động.
- Nhãn biểu mẫu cần rõ ràng, hỗ trợ điều hướng bàn phím, độ tương phản phù hợp và giảm chuyển động khi cần.
- Chưa có quy chuẩn accessibility cụ thể nào được xác nhận trong tài liệu dự án.

## Brand Commitments

- Giữ đúng tên AZ Media và nhận diện thương hiệu hiện có.
- Nội dung phải phù hợp với văn hóa doanh nghiệp AZ Media.

## Evidence on Hand

- `README.md` mô tả dự án và các luồng chính.
- Mã nguồn Laravel hiện có trong `app/`, `routes/`, `resources/` và `public/`.
- Các giao diện cổng ứng viên, tuyển dụng, quản trị và thực tập sinh trong `resources/views/azmedia/`.
- Không tự tạo thêm khách hàng, số liệu, chứng thực hoặc tuyên bố triển khai khi chưa có nguồn xác nhận.

## Product Principles

- Quy trình tuyển dụng phải liền mạch từ ứng tuyển đến thực tập.
- Trạng thái hồ sơ cần rõ ràng và có thể tra cứu.
- Mỗi nhóm người dùng cần có đúng quyền và luồng phù hợp với vai trò.
- Thay đổi phải bảo toàn tính ổn định của hệ thống Laravel hiện có.
- Trải nghiệm phải dễ dùng trên cả máy tính và thiết bị di động.

## Accessibility & Inclusion

Ưu tiên nhãn biểu mẫu rõ ràng, điều hướng bàn phím, độ tương phản phù hợp và hỗ trợ giảm chuyển động. Chưa có tiêu chuẩn tuân thủ cụ thể được xác nhận.
