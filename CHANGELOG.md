# Changelog - TH Admin Bar

Tất cả những thay đổi quan trọng của dự án này sẽ được ghi lại trong tập tin này.

Định dạng dựa trên [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
và dự án này tuân theo [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2023-07-10

### Thêm vào
- Phát hành ban đầu của module TH Admin Bar
- Thanh công cụ dạng WordPress cho Magento 2
- Chức năng theo ngữ cảnh:
  - Liên kết chỉnh sửa sản phẩm trên trang sản phẩm
  - Liên kết chỉnh sửa trang CMS trên trang CMS
  - Công cụ quản lý bộ nhớ đệm
  - Truy cập nhanh vào bảng điều khiển quản trị
- Bảng cấu hình quản trị:
  - Bật/tắt thanh quản trị
  - Cài đặt hiển thị trong chế độ sản xuất
  - Cấu hình vị trí (trên/dưới)
  - Tùy chỉnh giao diện (màu sắc)
- Tính năng bảo mật:
  - Xác minh phiên quản trị qua AJAX
  - Bảo vệ CSRF
  - Kiểm soát chế độ sản xuất
- Hỗ trợ nhiều theme:
  - Tương thích với theme mặc định Luma
  - Kiến trúc mở rộng cho các module tương thích theme
- Tối ưu hóa hiệu suất:
  - Yêu cầu HTTP tối thiểu
  - Tải lười biếng cho người dùng đã xác thực
  - Template được lưu trong bộ nhớ đệm
  - Tối ưu hóa tài nguyên
- Thiết kế responsive:
  - Giao diện thân thiện với thiết bị di động
  - Điều khiển thân thiện với cảm ứng
  - Thu gọn trên màn hình nhỏ hơn
- Chức năng quản trị:
  - Observer đăng nhập/đăng xuất quản trị
  - Theo dõi hoạt động quản trị
  - Trạng thái xác thực dựa trên cookie
  - Xây dựng URL quản trị động

### Tính năng kỹ thuật
- Tương thích PHP 7.4, 8.1, 8.2, 8.3
- Tương thích Magento 2.4.x
- Tự động tải PSR-4
- Cấu trúc gói Composer
- Tài liệu đầy đủ
- Hướng dẫn cài đặt
- Tài liệu xử lý sự cố

### Bảo mật
- Chỉ hiển thị cho người dùng quản trị đã xác thực
- Tôn trọng quản lý phiên quản trị của Magento
- Có thể bị vô hiệu hóa trong chế độ sản xuất
- Sử dụng bảo vệ CSRF tích hợp của Magento
- Điểm cuối AJAX an toàn để kiểm tra trạng thái

### Hiệu suất
- Tác động tối thiểu đến hiệu suất frontend
- Một cuộc gọi AJAX duy nhất cho trạng thái xác thực
- Tận dụng các lớp bộ nhớ đệm của Magento
- Tải tài nguyên tối ưu
- Khởi tạo lười biếng

## [Chưa phát hành]

### Tính năng dự kiến
- Thêm hành động theo ngữ cảnh
- Thêm tùy chọn tùy chỉnh
- Cải thiện trải nghiệm trên thiết bị di động
- Tích hợp với nhiều tính năng quản trị Magento hơn
- Cải thiện hiệu suất
- Bổ sung module tương thích theme

---

## Lịch sử phiên bản

- **1.0.0** - Phát hành ổn định ban đầu với chức năng cốt lõi
- **Phiên bản tương lai** - Sẽ tuân theo semantic versioning

## Hỗ trợ

Đối với các vấn đề hoặc câu hỏi cụ thể về phiên bản, vui lòng:
1. Kiểm tra tài liệu phiên bản liên quan
2. Xem hướng dẫn cài đặt cho phiên bản của bạn
3. Tạo issue trên GitHub với thông tin phiên bản
4. Bao gồm chi tiết môi trường và phiên bản
