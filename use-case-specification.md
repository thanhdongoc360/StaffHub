# Đặc tả chi tiết Use Case - StaffHub

Tài liệu này bao gồm đặc tả chi tiết của 6 use case quan trọng nhất trong hệ thống StaffHub. Mỗi use case gồm: tên, luồng sự kiện (chính và phát sinh), tiền điều kiện, hậu điều kiện.

---

## 1. Đăng nhập (Login)

### Tên Use Case
**Đăng nhập hệ thống**

### Tác nhân chính
Người dùng (bất kỳ vai trò: Admin, Management, Accountant, Employee)

### Tiền điều kiện
- Người dùng có tài khoản hợp lệ trong hệ thống
- Hệ thống đang hoạt động bình thường

### Luồng sự kiện chính
1. Người dùng mở ứng dụng
2. Hệ thống hiển thị màn hình đăng nhập
3. Người dùng nhập email và mật khẩu
4. Người dùng nhấn nút "Đăng nhập"
5. Hệ thống xác thực email và mật khẩu
6. Hệ thống kiểm tra tài khoản tồn tại và mật khẩu đúng
7. Hệ thống tạo token xác thực (Sanctum token)
8. Hệ thống trả về thông tin người dùng, vai trò, và token
9. Ứng dụng lưu token vào localStorage
10. Ứng dụng chuyển hướng về dashboard tương ứng với vai trò

### Luồng sự kiện phát sinh

**Trường hợp 1: Email không tồn tại hoặc mật khẩu sai**
- 6a. Hệ thống phát hiện email không tồn tại hoặc mật khẩu không khớp
- 6b. Hệ thống trả về lỗi "Thông tin đăng nhập không hợp lệ"
- 6c. Ứng dụng hiển thị thông báo lỗi
- 6d. Người dùng quay lại bước 3 để thử lại

**Trường hợp 2: Tài khoản bị khóa**
- 6a. Hệ thống phát hiện tài khoản bị khóa/vô hiệu hóa
- 6b. Hệ thống trả về lỗi "Tài khoản không hoạt động"
- 6c. Ứng dụng hiển thị thông báo
- 6d. Luồng kết thúc, người dùng liên hệ quản trị viên

### Hậu điều kiện
- **Thành công**: Người dùng được xác thực, có token hợp lệ, được chuyển hướng đến dashboard phù hợp với vai trò
- **Thất bại**: Người dùng vẫn ở màn hình đăng nhập, không nhận được token

---

## 2. Tạo đơn nghỉ phép (Create Leave Request)

### Tên Use Case
**Tạo đơn xin nghỉ phép**

### Tác nhân chính
Employee (Nhân viên)

### Tiền điều kiện
- Người dùng đã đăng nhập và là vai trò Employee
- Người dùng có liên kết Employee profile
- Hệ thống có thông tin nhân viên của người dùng

### Luồng sự kiện chính
1. Nhân viên truy cập màn hình "Đơn nghỉ phép"
2. Nhân viên nhấn nút "Tạo đơn mới"
3. Hệ thống hiển thị form nhập thông tin
4. Nhân viên nhập: từ ngày, đến ngày, loại nghỉ (ngày thường, khám bệnh, việc riêng...), lý do
5. Nhân viên nhấn "Gửi đơn"
6. Hệ thống kiểm tra dữ liệu:
   - Từ ngày ≤ Đến ngày?
   - Từ ngày không phải quá khứ?
   - Các trường bắt buộc có giá trị?
7. Hệ thống kiểm tra hợp lệ ✓
8. Hệ thống tạo LeaveRequest với trạng thái "Chờ duyệt"
9. Hệ thống trả về thông báo thành công
10. Ứng dụng làm mới danh sách đơn

### Luồng sự kiện phát sinh

**Trường hợp 1: Dữ liệu nhập không hợp lệ**
- 6a. Hệ thống phát hiện dữ liệu không hợp lệ (ngày không hợp lệ, thiếu trường)
- 6b. Hệ thống trả về thông báo lỗi chi tiết
- 6c. Ứng dụng hiển thị lỗi trên form
- 6d. Nhân viên sửa lỗi và gửi lại

**Trường hợp 2: Nhân viên không có Employee profile**
- 6a. Hệ thống kiểm tra không tìm thấy Employee liên kết
- 6b. Hệ thống trả về lỗi "Không tìm thấy thông tin nhân viên"
- 6c. Ứng dụng hiển thị thông báo
- 6d. Luồng kết thúc, nhân viên liên hệ IT

### Hậu điều kiện
- **Thành công**: LeaveRequest được tạo với trạng thái "Chờ duyệt", hiển thị trong danh sách đơn của nhân viên
- **Thất bại**: LeaveRequest không được tạo, form vẫn giữ dữ liệu nhập

---

## 3. Duyệt đơn nghỉ phép (Approve Leave Request)

### Tên Use Case
**Duyệt/Từ chối đơn xin nghỉ phép**

### Tác nhân chính
Management (Quản lý phòng ban)

### Tiền điều kiện
- Người dùng đã đăng nhập và là vai trò Management
- Người dùng có liên kết Employee profile (để xác định phòng ban)
- Có đơn nghỉ ở trạng thái "Chờ duyệt" của nhân viên cùng phòng ban

### Luồng sự kiện chính
1. Quản lý truy cập màn hình "Đơn nghỉ phép"
2. Hệ thống hiển thị danh sách đơn nghỉ của phòng ban quản lý
3. Quản lý tìm kiếm/lọc đơn cần xử lý
4. Quản lý chọn đơn để xem chi tiết
5. Hệ thống hiển thị thông tin: nhân viên, loại nghỉ, từ ngày, đến ngày, lý do, trạng thái
6. Quản lý quyết định: duyệt hoặc từ chối
7. **Nếu duyệt**:
   - Quản lý nhấn nút "Duyệt"
   - Hệ thống cập nhật trạng thái LeaveRequest thành "Đã duyệt"
   - Hệ thống trả về thông báo thành công
8. **Nếu từ chối**:
   - Quản lý nhấn nút "Từ chối"
   - Hệ thống cập nhật trạng thái LeaveRequest thành "Từ chối"
   - Hệ thống trả về thông báo thành công

### Luồng sự kiện phát sinh

**Trường hợp 1: Đơn đã được xử lý trước đó**
- 5a. Hệ thống kiểm tra trạng thái đơn
- 5b. Nếu trạng thái ≠ "Chờ duyệt", hệ thống trả về lỗi "Đơn này đã được xử lý"
- 5c. Ứng dụng hiển thị thông báo không cho phép xử lý
- 5d. Luồng kết thúc

**Trường hợp 2: Nhân viên không thuộc phòng ban của quản lý**
- 5a. Hệ thống kiểm tra phòng ban nhân viên đơn
- 5b. Nếu phòng ban ≠ phòng ban quản lý, hệ thống trả về lỗi "Không có quyền xử lý"
- 5c. Ứng dụng hiển thị thông báo không có quyền
- 5d. Luồng kết thúc

### Hậu điều kiện
- **Thành công (Duyệt)**: LeaveRequest cập nhật trạng thái "Đã duyệt", được hiển thị trong danh sách của nhân viên, và trạng thái thay đổi là final
- **Thành công (Từ chối)**: LeaveRequest cập nhật trạng thái "Từ chối", được hiển thị trong danh sách của nhân viên
- **Thất bại**: LeaveRequest giữ nguyên trạng thái cũ

---

## 4. Tạo bảng lương tháng (Create Payroll Batch)

### Tên Use Case
**Tạo bảng lương tháng mới**

### Tác nhân chính
Accountant (Kế toán)

### Tiền điều kiện
- Người dùng đã đăng nhập và là vai trò Accountant
- Tháng/năm cần lập lương chưa có bảng lương nào
- Tháng trước đã có dữ liệu lương (hoặc là lần đầu tạo)

### Luồng sự kiện chính
1. Kế toán truy cập màn hình "Quản lý lương" -> "Tạo bảng lương"
2. Hệ thống hiển thị form chọn tháng/năm
3. Kế toán chọn tháng và năm cần tạo
4. Kế toán nhấn "Tạo"
5. Hệ thống kiểm tra xem bảng lương tháng/năm đã tồn tại chưa:
   - Truy vấn DB: SELECT * FROM salaries WHERE month = ? AND year = ?
6. **Nếu chưa tồn tại**:
   - Hệ thống xác định tháng trước: prevMonth, prevYear
   - Hệ thống lấy dữ liệu lương tháng trước: SELECT * FROM salaries WHERE month = prevMonth AND year = prevYear
   - Hệ thống lấy danh sách employee chưa có lương tháng này
   - Hệ thống tạo bản ghi Salary mới cho mỗi employee, copy dữ liệu từ tháng trước (base_salary, bonus, tax) hoặc khởi tạo 0 nếu lần đầu
   - Hệ thống set trạng thái = "draft", created_at = now()
   - Hệ thống trả về thông báo "Tạo bảng lương thành công"
7. Ứng dụng làm mới danh sách lương

### Luồng sự kiện phát sinh

**Trường hợ 1: Bảng lương tháng đã tồn tại**
- 5a. Hệ thống kiểm tra thấy bảng lương tháng/năm đã có
- 5b. Hệ thống trả về lỗi "Bảng lương tháng này đã được tạo rồi"
- 5c. Ứng dụng hiển thị thông báo
- 5d. Luồng kết thúc, kế toán có thể chọn tháng khác

**Trường hợp 2: Không có employee nào cần tạo lương**
- 5a. Hệ thống kiểm tra danh sách employee chưa có lương tháng này rỗng
- 5b. Hệ thống trả về lỗi "Tất cả nhân viên đã có lương tháng này"
- 5c. Ứng dụng hiển thị thông báo
- 5d. Luồng kết thúc

### Hậu điều kiện
- **Thành công**: Bảng lương tháng được tạo với trạng thái "draft", mỗi nhân viên có 1 bản ghi Salary, dữ liệu copy từ tháng trước nếu có
- **Thất bại**: Không tạo bảng lương mới, dữ liệu DB không thay đổi

---

## 5. Tính lương (Calculate Salary)

### Tên Use Case
**Tính lương tháng**

### Tác nhân chính
Accountant (Kế toán)

### Tiền điều kiện
- Người dùng đã đăng nhập và là vai trò Accountant
- Bảng lương tháng đã được tạo với trạng thái "draft"
- Các bản ghi Salary có giá trị base_salary, bonus, tax (hoặc 0 nếu không có)

### Luồng sự kiện chính
1. Kế toán truy cập màn hình "Quản lý lương" -> "Danh sách lương"
2. Hệ thống hiển thị danh sách lương theo tháng/năm được chọn, trạng thái "draft"
3. Kế toán (tuỳ chọn) chỉnh sửa từng bản ghi salary nếu cần (base_salary, bonus, tax)
4. Kế toán chọn thao tác "Tính lương" (có thể tính hàng loạt hoặc từng bản ghi)
5. **Tính hàng loạt**:
   - Kế toán chọn tháng/năm và nhấn "Tính lương hàng loạt"
   - Hệ thống lấy tất cả Salary với status = "draft" của tháng/năm đó
6. Hệ thống thực hiện tính toán:
   - Với mỗi bản ghi: total = base_salary + bonus - tax
   - Hệ thống cập nhật Salary.total = tổng tính toán
   - Hệ thống cập nhật Salary.status = "calculated"
7. Hệ thống trả về thông báo "Tính lương thành công", hiển thị số lượng bản ghi đã tính

### Luồng sự kiện phát sinh

**Trường hợp 1: Không có bản ghi lương nào ở trạng thái draft**
- 5a. Hệ thống kiểm tra không tìm thấy Salary.status = "draft"
- 5b. Hệ thống trả về lỗi "Không có lương nào cần tính"
- 5c. Ứng dụng hiển thị thông báo
- 5d. Luồng kết thúc

**Trường hợp 2: Một số bản ghi lương bị thiếu dữ liệu**
- 5a. Hệ thống kiểm tra base_salary = NULL hoặc âm
- 5b. Hệ thống trả về cảnh báo "Có X bản ghi thiếu base_salary, vui lòng kiểm tra"
- 5c. Kế toán có thể chọn bỏ qua hoặc sửa trước khi tính
- 5d. Quay lại bước 3

### Hậu điều kiện
- **Thành công**: Tất cả Salary được cập nhật total = base + bonus - tax, trạng thái = "calculated"
- **Thất bại**: Không có thay đổi trên DB

---

## 6. Check-in chấm công (Check-In Attendance)

### Tên Use Case
**Check-in chấm công**

### Tác nhân chính
Employee (Nhân viên)

### Tiền điều kiện
- Người dùng đã đăng nhập và là vai trò Employee
- Người dùng có liên kết Employee profile
- Hệ thống có AttendanceRule cấu hình (standard_work_minutes, work_start_time, late_threshold_minutes)
- Hôm nay chưa có bản ghi check-in cho nhân viên này

### Luồng sự kiện chính
1. Nhân viên truy cập ứng dụng, mở màn hình "Chấm công"
2. Hệ thống hiển thị thông tin ngày hiện tại và nút "Check-in"
3. Nhân viên nhấn nút "Check-in"
4. Hệ thống thực hiện:
   - Lấy ngày hôm nay (today)
   - Lấy employee_id của người dùng từ token
   - Kiểm tra xem đã có bản ghi Attendance với employee_id và date = today chưa
5. **Nếu chưa có**:
   - Hệ thống tạo bản ghi Attendance mới: employee_id, date = today, check_in_time = now()
   - Hệ thống trả về bản ghi Attendance
6. **Nếu đã có**:
   - Hệ thống kiểm tra check_in_time đã được set chưa
7. Ứng dụng hiển thị thông báo "Check-in thành công" kèm thời gian check-in

### Luồng sự kiện phát sinh

**Trường hợp 1: Nhân viên đã check-in trong hôm nay**
- 5a. Hệ thống kiểm tra thấy bản ghi Attendance đã tồn tại với check_in_time ≠ NULL
- 5b. Hệ thống trả về lỗi "Bạn đã check-in rồi"
- 5c. Ứng dụng hiển thị thông báo
- 5d. Luồng kết thúc, nhân viên không thể check-in lần thứ 2 trong ngày

**Trường hợp 2: Nhân viên không có Employee profile**
- 5a. Hệ thống kiểm tra không tìm thấy employee liên kết
- 5b. Hệ thống trả về lỗi "Không tìm thấy thông tin nhân viên"
- 5c. Ứng dụng hiển thị thông báo
- 5d. Luồng kết thúc, nhân viên liên hệ IT

### Hậu điều kiện
- **Thành công**: Bản ghi Attendance được tạo/cập nhật với check_in_time = now(), check_out_time = NULL, status = NULL (chờ check-out)
- **Thất bại**: Không có thay đổi, nhân viên vẫn không check-in được

---

## Ghi chú chung

- Tất cả use case đều yêu cầu xác thực người dùng thành công trước
- Dữ liệu được lưu vào database qua Eloquent ORM (Laravel)
- Hệ thống trả lỗi dưới dạng JSON response với HTTP status code thích hợp (200, 400, 403, 404, 422)
- Các use case phức tạp như "Tính lương" và "Duyệt đơn" có thể kích hoạt tạo Notification cho người dùng liên quan
