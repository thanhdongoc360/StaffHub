# Quy trình nghiệp vụ trọng tâm - Activity Diagram (PlantUML)

Tài liệu này gồm 4 quy trình nghiệp vụ quan trọng của hệ thống StaffHub. Mỗi mục có biểu đồ activity bằng PlantUML và mô tả ngắn để đưa vào báo cáo đồ án.

## 1) Quy trình tiền lương theo tháng

### PlantUML

```plantuml
@startuml
title Quy trình tiền lương theo tháng

|Accountant|
start
:Chọn tháng/năm cần lập lương;
:Tạo bảng lương tháng;

|System|
:Kiểm tra bảng lương tháng đã tồn tại?;
if (Đã tồn tại?) then (Có)
  :Trả thông báo không tạo mới;
  stop
else (Không)
  :Copy dữ liệu nền từ tháng trước;
  :Khởi tạo trạng thái draft;
endif

|Accountant|
:Xem danh sách lương;
:Chỉnh sửa lương từng nhân viên (nếu cần);
:Thực hiện tính lương;

|System|
:Tính total = base + bonus - tax;
:Cập nhật trạng thái calculated;

|Accountant|
:Duyệt lương;

|System|
:Cập nhật trạng thái approved;

|Accountant|
:Phát hành lương;

|System|
:Cập nhật trạng thái published;

|Employee|
:Xem phiếu lương cá nhân;

|Accountant|
:Xuất bảng lương Excel;
stop
@enduml
```

### Mô tả ngắn

Quy trình tiền lương được thực hiện theo chu kỳ tháng và có vòng đời trạng thái rõ ràng: draft, calculated, approved, published. Kế toán chịu trách nhiệm tạo bảng lương, kiểm tra/chỉnh sửa dữ liệu, tính lương, duyệt và phát hành. Sau khi phát hành, nhân viên có thể xem lương cá nhân và kế toán có thể xuất dữ liệu để báo cáo.

## 2) Quy trình nghỉ phép

### PlantUML

```plantuml
@startuml
title Quy trình nghỉ phép

|Employee|
start
:Nhập thông tin đơn nghỉ
(từ ngày, đến ngày, loại, lý do);
:Gửi đơn nghỉ phép;

|System|
:Kiểm tra dữ liệu hợp lệ;
if (Hợp lệ?) then (Không)
  :Trả lỗi nhập liệu;
  stop
else (Có)
  :Tạo đơn với trạng thái "Chờ duyệt";
endif

|Management|
:Xem danh sách đơn nghỉ phòng ban;
:Mở chi tiết đơn;
if (Đồng ý?) then (Duyệt)
  :Duyệt đơn;
  |System|
  :Cập nhật trạng thái "Đã duyệt";
else (Từ chối)
  :Từ chối đơn;
  |System|
  :Cập nhật trạng thái "Từ chối";
endif

|Employee|
:Xem kết quả xử lý đơn nghỉ;
stop
@enduml
```

### Mô tả ngắn

Nhân viên tạo đơn nghỉ phép và hệ thống lưu ở trạng thái chờ duyệt. Quản lý xử lý đơn theo phạm vi phòng ban, quyết định duyệt hoặc từ chối. Kết quả được cập nhật về đơn để nhân viên theo dõi trên màn hình danh sách đơn/nghiệp vụ liên quan.

## 3) Quy trình chấm công và lịch làm việc

### PlantUML

```plantuml
@startuml
title Quy trình chấm công và lịch làm việc

|Management|
start
:Tạo lịch tuần;
:Gán nhân viên vào ca;

|System|
:Lưu phân ca theo ngày;

|Employee|
:Xem lịch làm việc cá nhân;
:Đến ca làm và check-in;

|System|
:Kiểm tra đã check-in trong ngày chưa?;
if (Đã check-in?) then (Có)
  :Từ chối check-in trùng;
else (Không)
  :Ghi nhận check-in;
endif

|Employee|
:Kết thúc ca và check-out;

|System|
:Tính thời gian làm việc;
:Tính đi muộn / nửa ngày / OT
theo attendance rule;
:Lưu bản ghi chấm công;

|Management|
:Rà soát chấm công phòng ban;
if (Có ngoại lệ?) then (Có)
  :Cập nhật điều chỉnh bản ghi;
  |System|
  :Lưu điều chỉnh;
endif

stop
@enduml
```

### Mô tả ngắn

Quy trình bắt đầu từ lập lịch và phân ca, sau đó nhân viên thực hiện check-in/check-out theo ca làm việc đã được gán. Hệ thống tự động tính toán thời gian làm, tình trạng đi muộn và tăng ca. Quản lý có thể rà soát và điều chỉnh các trường hợp ngoại lệ để đảm bảo dữ liệu chấm công chính xác.

## 4) Quy trình đánh giá hiệu suất

### PlantUML

```plantuml
@startuml
title Quy trình đánh giá hiệu suất định kỳ

|Management|
start
:Chọn kỳ đánh giá (tháng/năm);
:Chọn nhân viên cần đánh giá;

|System|
:Tìm review theo kỳ;
if (Đã có review?) then (Có)
  :Mở review hiện có;
else (Không)
  :Khởi tạo review trạng thái draft;
endif

|Management|
:Nhập điểm KPI, kỷ luật,
hợp tác, phát triển;
:Nhập nhận xét;
:Lưu đánh giá;

|System|
:Tính total score và xếp loại;
:Lưu trạng thái draft/submitted;

|Management|
:Xác nhận đánh giá;

|System|
:Cập nhật trạng thái confirmed;

|Admin|
:Xem tổng quan và lịch sử đánh giá;
stop
@enduml
```

### Mô tả ngắn

Đánh giá hiệu suất được thực hiện định kỳ theo tháng/năm bởi quản lý. Hệ thống cho phép tạo mới hoặc cập nhật review hiện có, tính điểm tổng và xếp loại từ các tiêu chí thành phần. Sau khi xác nhận, dữ liệu được lưu làm lịch sử để theo dõi chất lượng nhân sự theo thời gian.
