# Biểu đồ use case phân rã - StaffHub

Các biểu đồ dưới đây bám theo đúng tên các use case mức cao trong biểu đồ tổng quan. Mỗi khối có thể copy riêng để dán vào PlantUML.

## 1. Quản lý nhân sự

Phần này mô tả các chức năng chính liên quan đến hồ sơ nhân viên trong hệ thống. Admin có thể xem, thêm, sửa, xóa và theo dõi chi tiết thông tin nhân viên, trong khi Management chỉ được xem danh sách và chi tiết nhân viên thuộc phạm vi quản lý.

```plantuml
@startuml
left to right direction
skinparam packageStyle rectangle

actor Admin
actor Management

rectangle "Quản lý nhân sự" {
  usecase "Xem danh sách nhân viên" as UC1
  usecase "Xem chi tiết nhân viên" as UC2
  usecase "Thêm nhân viên" as UC3
  usecase "Cập nhật thông tin nhân viên" as UC4
  usecase "Xóa nhân viên" as UC5
}

Admin --> UC1
Admin --> UC2
Admin --> UC3
Admin --> UC4
Admin --> UC5

Management --> UC1
Management --> UC2

@enduml
```

## 2. Quản lý nghỉ phép

Phần này thể hiện quy trình xử lý đơn nghỉ phép của nhân viên. Employee tạo và xem đơn của mình, còn Admin và Management có thể xem danh sách đơn, duyệt hoặc từ chối đơn nghỉ theo quyền hạn được phân công.

```plantuml
@startuml
left to right direction
skinparam packageStyle rectangle

actor Employee
actor Management
actor Admin

rectangle "Quản lý nghỉ phép" {
  usecase "Tạo đơn nghỉ phép" as UC1
  usecase "Xem danh sách đơn nghỉ" as UC2
  usecase "Duyệt đơn nghỉ" as UC3
  usecase "Từ chối đơn nghỉ" as UC4
}

Employee --> UC1
Employee --> UC2

Management --> UC2
Management --> UC3
Management --> UC4

Admin --> UC2
Admin --> UC3
Admin --> UC4

@enduml
```

## 3. Quản lý lương

Phần này mô tả các thao tác liên quan đến tiền lương. Employee chỉ xem lương cá nhân, Management xem lương theo phòng ban, còn Accountant chịu trách nhiệm tạo bảng lương, tính lương, duyệt lương, phát hành lương và xuất dữ liệu lương.

```plantuml
@startuml
left to right direction
skinparam packageStyle rectangle

actor Employee
actor Management
actor Accountant
actor Admin

rectangle "Quản lý lương" {
  usecase "Xem lương cá nhân" as UC1
  usecase "Xem danh sách lương" as UC2
  usecase "Tạo bảng lương" as UC3
  usecase "Tính lương" as UC4
  usecase "Duyệt lương" as UC5
  usecase "Phát hành lương" as UC6
  usecase "Xuất bảng lương" as UC7
}

Employee --> UC1

Management --> UC2

Accountant --> UC2
Accountant --> UC3
Accountant --> UC4
Accountant --> UC5
Accountant --> UC6
Accountant --> UC7

Admin --> UC2
Admin --> UC3

@enduml
```

## 4. Chấm công & Lịch làm việc

Phần này bao gồm các chức năng chấm công và phân ca làm việc. Employee thực hiện check-in, check-out và xem lịch làm việc; Management và Admin có thể tạo lịch tuần, gán nhân viên vào ca và quản lý ca làm việc.

```plantuml
@startuml
left to right direction
skinparam packageStyle rectangle

actor Employee
actor Management
actor Admin

rectangle "Chấm công & Lịch làm việc" {
  usecase "Check-in" as UC1
  usecase "Check-out" as UC2
  usecase "Xem lịch sử chấm công" as UC3
  usecase "Xem lịch làm việc" as UC4
  usecase "Tạo lịch tuần" as UC5
  usecase "Gán nhân viên vào ca" as UC6
  usecase "Quản lý ca làm việc" as UC7
}

Employee --> UC1
Employee --> UC2
Employee --> UC3
Employee --> UC4

Management --> UC4
Management --> UC5
Management --> UC6
Management --> UC7

Admin --> UC5
Admin --> UC6
Admin --> UC7

@enduml
```

## 5. Thông báo & Hồ sơ cá nhân

Phần này tập trung vào các chức năng cá nhân mà mọi vai trò đều cần sử dụng. Người dùng có thể xem thông báo, đánh dấu đã đọc, cập nhật thông tin cá nhân và đổi mật khẩu để tự quản lý tài khoản của mình.

```plantuml
@startuml
left to right direction
skinparam packageStyle rectangle

actor Admin
actor Management
actor Accountant
actor Employee

rectangle "Thông báo & Hồ sơ cá nhân" {
  usecase "Xem thông báo" as UC1
  usecase "Đánh dấu đã đọc" as UC2
  usecase "Cập nhật hồ sơ cá nhân" as UC3
  usecase "Đổi mật khẩu" as UC4
}

Admin --> UC1
Admin --> UC2
Admin --> UC3
Admin --> UC4

Management --> UC1
Management --> UC2
Management --> UC3
Management --> UC4

Accountant --> UC1
Accountant --> UC2
Accountant --> UC3
Accountant --> UC4

Employee --> UC1
Employee --> UC2
Employee --> UC3
Employee --> UC4

@enduml
```

## 6. Đánh giá hiệu suất

Phần này mô tả quy trình đánh giá kết quả làm việc của nhân viên. Management có thể tạo, cập nhật, xác nhận và xem lịch sử đánh giá, còn Admin chủ yếu xem tổng quan và theo dõi thông tin đánh giá.

```plantuml
@startuml
left to right direction
skinparam packageStyle rectangle

actor Admin
actor Management

rectangle "Đánh giá hiệu suất" {
  usecase "Xem danh sách đánh giá" as UC1
  usecase "Tạo đánh giá" as UC2
  usecase "Cập nhật đánh giá" as UC3
  usecase "Xác nhận đánh giá" as UC4
  usecase "Xem lịch sử đánh giá" as UC5
}

Admin --> UC1
Admin --> UC5

Management --> UC1
Management --> UC2
Management --> UC3
Management --> UC4
Management --> UC5

@enduml
```