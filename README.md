# TH Admin Bar for Magento 2

[![Latest Stable Version](http://poser.pugx.org/th/module-adminbar/v)](https://packagist.org/packages/th/module-adminbar)
[![Total Downloads](http://poser.pugx.org/th/module-adminbar/downloads)](https://packagist.org/packages/th/module-adminbar)
[![License](http://poser.pugx.org/th/module-adminbar/license)](https://packagist.org/packages/th/module-adminbar)
[![CI](https://github.com/tuanhaviet22/magento2-module-adminbar/actions/workflows/ci.yml/badge.svg)](https://github.com/tuanhaviet22/magento2-module-adminbar/actions/workflows/ci.yml)

Một thanh công cụ dạng WordPress cho Magento 2, giúp truy cập nhanh vào các chức năng quản trị khi đang xem frontend.

## Tính năng chính

- **Giao diện giống WordPress**: Cung cấp trải nghiệm quen thuộc cho các lập trình viên WordPress
- **Chức năng theo ngữ cảnh**: 
  - Chỉnh sửa sản phẩm (khi đang ở trang sản phẩm)
  - Chỉnh sửa trang CMS (khi đang ở trang CMS)
  - Quản lý bộ nhớ đệm
  - Truy cập nhanh vào bảng điều khiển quản trị
- **Hỗ trợ nhiều theme**:
  - Theme Luma (tích hợp sẵn)
  - Theme Hyva (qua module tương thích TH_AdminbarHyva)
- **Dễ dàng cấu hình**: Cài đặt từ Admin cho giao diện và hành vi
- **Tối ưu hiệu suất**: Ảnh hưởng tối thiểu đến hiệu suất frontend
- **Bảo mật**: Chỉ hiển thị cho người dùng admin đã đăng nhập

## Yêu cầu

- PHP 7.4, 8.1, 8.2 hoặc 8.3
- Magento 2.4.x
- Quyền quản trị viên để cấu hình module

## Cài đặt

### Cài đặt qua Composer (Khuyến nghị)

```bash
composer require th/module-adminbar
bin/magento module:enable TH_Adminbar
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
```

### Cài đặt thủ công

1. Tải module và giải nén vào thư mục:
```bash
app/code/TH/Adminbar
```

2. Kích hoạt module:
```bash
bin/magento module:enable TH_Adminbar
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
```

### Cho người dùng Hyva Theme

Cài đặt thêm module tương thích:

```bash
composer require th/module-adminbar-hyva
bin/magento module:enable TH_AdminbarHyva
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
```

## Cấu hình

Điều hướng đến **Cửa hàng > Cấu hình > TH Extensions > Admin Bar** để cấu hình:

- **Bật/Tắt** thanh công cụ admin
- **Hiển thị ở chế độ Production**
- **Vị trí** (đầu hoặc cuối trang)
- **Giao diện** (màu nền và màu chữ)

## Cách hoạt động

1. **Kiểm tra xác thực**: Xác minh phiên admin thông qua AJAX
2. **Nhận diện ngữ cảnh**: Tự động phát hiện loại trang hiện tại (sản phẩm, CMS, v.v.)
3. **Hiển thị động**: Hiển thị các liên kết chỉnh sửa phù hợp theo ngữ cảnh
4. **Tương thích theme**: Sử dụng template phù hợp dựa trên theme đang hoạt động

## Chi tiết kỹ thuật

### Module chính (TH_Adminbar)
- **Logic backend**: Toàn bộ logic nghiệp vụ và cấu hình
- **Tương thích Luma**: Các template mặc định hoạt động với theme Luma
- **Điểm cuối AJAX**: Kiểm tra trạng thái xác thực
- **Cấu hình Admin**: Tùy chọn cấu hình hệ thống

### Module tương thích Hyva (TH_AdminbarHyva)
- **Tích hợp Alpine.js**: Các thành phần phản ứng
- **Tailwind CSS**: Kiểu dáng hiện đại
- **Ghi đè Template**: Các template dành riêng cho Hyva
- **CSS Merging**: Tự động hợp nhất cấu hình Tailwind

## Tùy biến

### Tùy chỉnh Template

Cho theme Luma:
```
app/design/frontend/[Vendor]/[Theme]/TH_Adminbar/templates/adminbar.phtml
```

Cho theme Hyva:
```
app/design/frontend/[Vendor]/[Theme]/TH_AdminbarHyva/templates/hyva/adminbar.phtml
```

### Tùy chỉnh Giao diện

Thanh admin mặc định sử dụng inline styles nhưng có thể tùy chỉnh thông qua:
- Cấu hình admin (màu sắc)
- Ghi đè CSS trong theme của bạn
- Các tiện ích Tailwind (theme Hyva)

## Bảo mật

- Chỉ hiển thị cho người dùng admin đã xác thực
- Tuân theo quản lý phiên admin của Magento
- Có thể tắt ở chế độ production
- Sử dụng bảo vệ CSRF tích hợp của Magento

## Thiết kế Responsive

- Thiết kế thân thiện với thiết bị di động
- Thu gọn trên màn hình nhỏ
- Giao diện thân thiện với cảm ứng

## Hiệu suất

- **Yêu cầu HTTP tối thiểu**: Một cuộc gọi AJAX duy nhất để kiểm tra trạng thái
- **Tải lười biếng**: Chỉ tải khi người dùng admin được xác thực
- **Template được cache**: Tận dụng các lớp cache của Magento
- **Tối ưu hóa tài nguyên**: Dấu ấn CSS và JavaScript tối thiểu

## Tương thích

- **Phiên bản Magento**: 2.4.x
- **Phiên bản PHP**: 7.4, 8.1, 8.2, 8.3
- **Theme**: Luma, Hyva (với module tương thích)
- **Bộ nhớ đệm**: Tương thích với tất cả các lớp bộ nhớ đệm của Magento

## Xử lý sự cố

### Thanh Admin không hiển thị
1. Kiểm tra module đã được bật: `bin/magento module:status TH_Adminbar`
2. Xác minh admin đã đăng nhập
3. Kiểm tra cấu hình: Cửa hàng > Cấu hình > TH Extensions > Admin Bar
4. Xóa cache: `bin/magento cache:clean`

### Vấn đề với Theme Hyva
1. Đảm bảo module TH_AdminbarHyva đã được cài đặt và kích hoạt
2. Kiểm tra Alpine.js đã được tải trên trang
3. Xác minh biên dịch Tailwind CSS đã bao gồm các style của admin bar

## Giấy phép

Open Software License (OSL 3.0)

## Đóng góp

1. Fork repository
2. Tạo nhánh tính năng
3. Thực hiện các thay đổi
4. Kiểm tra kỹ lưỡng
5. Gửi pull request

## Hỗ trợ

Để được hỗ trợ và giải đáp thắc mắc, vui lòng tạo issue trong repository hoặc liên hệ với nhóm phát triển.
