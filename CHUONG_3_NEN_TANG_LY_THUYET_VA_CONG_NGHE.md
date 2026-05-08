# CHƯƠNG 3: NỀN TẢNG LÝ THUYẾT VÀ CÔNG NGHỆ SỬ DỤNG

## 3.1. Giới thiệu chung

Hệ thống StaffHub được xây dựng với kiến trúc client-server truyền thống, tách biệt rõ ràng giữa tầng giao diện người dùng (frontend) và tầng xử lý nghiệp vụ (backend). Lựa chọn công nghệ dựa trên các yêu cầu cụ thể được nêu trong Chương 2, bao gồm: (1) yêu cầu hiệu năng xử lý nhanh các truy vấn và cập nhật dữ liệu; (2) yêu cầu bảo mật với xác thực token và phân quyền theo vai trò; (3) yêu cầu bảo trì và mở rộng thành phần độc lập; (4) yêu cầu triển khai nhất quán qua Docker.

Mô hình kiến trúc sử dụng các công nghệ hiện đại để cân bằng giữa độ ổn định, khả năng bảo trì và tốc độ phát triển, phù hợp với quy mô của một đồ án tốt nghiệp và nhu cầu thực tế của hệ thống quản lý nhân sự.

## 3.2. Kiến trúc tổng quát
Hệ thống được thiết kế theo kiến trúc ba lớp (Three-tier Architecture).  Ở tầng Presentation (giao diện), ứng dụng sử dụng Vue 3 và Vite chạy trên trình duyệt của người dùng, thực hiện các thao tác hiển thị và gửi các yêu cầu HTTP/HTTPS tới backend.  Ở tầng Application (business logic), backend được xây dựng bằng Laravel 9 chạy trên PHP 8.2 để xử lý logic nghiệp vụ, kiểm tra xác thực và phân quyền, cũng như tương tác với cơ sở dữ liệu thông qua các Model Eloquent.  Ở tầng Data, MySQL 8.0 đảm nhiệm vai trò lưu trữ toàn bộ dữ liệu hệ thống.

Giao tiếp giữa frontend và backend được thực hiện qua RESTful API sử dụng định dạng JSON và được bảo vệ bằng token do Laravel Sanctum phát hành. Toàn bộ hệ thống được đóng gói trong các container và điều phối bằng Docker Compose, giúp đảm bảo tính nhất quán khi triển khai giữa các môi trường (local, staging, production).

## 3.3. Công nghệ Frontend

### 3.3.1. Vue 3 và Vite

Vue 3 và Vite được sử dụng để xây dựng giao diện tương tác nhanh, hỗ trợ bốn phân hệ người dùng khác nhau (Admin, Management, Accountant, Employee) với các thao tác như xem danh sách, cập nhật dữ liệu, và điều hướng trang web. Vue 3 cung cấp Composition API, giúp code tổ chức rõ ràng và dễ bảo trì so với Options API. Vite là build tool hiện đại với hot module replacement (HMR) nhanh, hỗ trợ development speed cao. Ecosystem Vue phong phú, có nhiều thư viện hỗ trợ như router, state management, và UI framework.

So với các lựa chọn thay thế, React (Facebook) mạnh hơn trong các dự án lớn nhưng phức tạp hơn với learning curve cao hơn so với Vue. Angular (Google) là framework đầy đủ nhưng nặng, không phù hợp với quy mô đồ án tốt nghiệp. Vanilla JavaScript rẻ hơn về tài nguyên nhưng khó bảo trì khi dự án phát triển.

### 3.3.2. Pinia (State Management)

Pinia được sử dụng để quản lý trạng thái toàn cầu (user login, selected department, filter criteria) giúp các component chia sẻ dữ liệu mà không cần prop drilling. Pinia là thay thế chính thức cho Vuex trong Vue 3, được khuyến nghị bởi Vue team. Nó có syntax đơn giản, hỗ trợ TypeScript, và cung cấp devtools tốt để debug. Pinia nhẹ hơn Vuex, phù hợp với quy mô dự án hiện tại.

So với các lựa chọn khác, Vuex cũ hơn và complex hơn nhưng vẫn ổn định nếu dự án không quá lớn. Context API (React) không áp dụng vì project sử dụng Vue. Local Storage kết hợp props đơn giản hơn nhưng khó quản lý state consistency.

### 3.3.3. Ant Design Vue (UI Components Library)

Ant Design Vue cung cấp UI components chuyên nghiệp (bảng dữ liệu, form, modal, dropdown, button) để xây dựng giao diện thống nhất cho bốn nhóm người dùng. Thư viện có component đầy đủ với icons xinh đẹp và hỗ trợ Vue 3 tốt. Nó cung cấp theme tùy chỉnh giúp giao diện nhất quán giữa các phân hệ, hỗ trợ responsive design để giao diện hoạt động tốt trên desktop và tablet. Sử dụng Ant Design Vue cũng giảm thời gian phát triển so với tự viết CSS.

So sánh với các lựa chọn khác, Bootstrap Vue nhẹ hơn nhưng có ít component hơn và cần custom CSS nhiều hơn. Material Design (vue-material) tuân theo Material Design nhưng có ít component hơn Ant Design Vue. Tailwind CSS rất flexible nhưng yêu cầu viết CSS thủ công, phù hợp hơn cho team quen CSS.

### 3.3.4. Vue Router 4

Vue Router 4 được sử dụng để quản lý định tuyến giữa các trang chính như Login, Dashboard, Employee Management, Leave Requests, Payroll, Attendance, Performance Review, và Profile. Vue Router là router chính thức cho Vue, được duy trì bởi Vue team. Nó hỗ trợ lazy loading routes để giảm bundle size khi tải ứng dụng, cung cấp navigation guards để kiểm tra xác thực trước khi vào các page.

So với các lựa chọn thay thế, Nuxt (Vue framework full-stack) mạnh hơn nhưng có overhead lớn đối với đồ án này. TanStack Router mới hơn nhưng có ít community support so với Vue Router.

### 3.3.5. Axios (HTTP Client)

Axios được sử dụng để gửi HTTP request từ frontend đến backend API, xử lý response JSON, và quản lý error khi API không khả dụng. Axios được sử dụng rộng rãi, có cú pháp đơn giản, và hỗ trợ interceptor để xử lý token trước mỗi request. Nó tự động chuyển đổi JSON và chuẩn hóa error handling.

So với các lựa chọn khác, Fetch API (native) đơn giản hơn nhưng không hỗ trợ timeout và cần thêm code cho interceptor. TanStack Query mạnh hơn nhưng có overhead cho những request đơn giản.

## 3.4. Công nghệ Backend

### 3.4.1. Laravel 9 và PHP 8.2

Laravel 9 và PHP 8.2 được sử dụng để xây dựng backend API RESTful, xử lý logic nghiệp vụ như tạo/cập nhật nhân viên, duyệt đơn nghỉ phép, tính lương, và kiểm soát quyền truy cập dựa trên vai trò. Laravel là framework PHP hiện đại, được dùng rộng rãi với ecosystem phong phú bao gồm authentication, ORM, migration, và testing. PHP 8.2 cung cấp performance tốt với typed properties và attributes giúp code rõ ràng hơn. Laravel cung cấp sẵn middleware để xử lý xác thực và phân quyền theo vai trò (role-based access control). Ngoài ra, cộng đồng Laravel lớn với tài liệu đầy đủ giúp debug nhanh.

So với các lựa chọn khác, Node.js + Express nhẹ hơn và fast nhưng yêu cầu viết validation code nhiều với ecosystem fragmented. Django (Python) powerful nhưng performance không bằng Laravel và không quen nhóm dev. ASP.NET Core (C#) tốt nhưng yêu cầu Windows hosting với giá cao, không phổ biến ở Việt Nam. Spring Boot (Java) heavyweight và phức tạp cho đồ án tốt nghiệp.

### 3.4.2. Eloquent ORM (Object-Relational Mapping)

Eloquent được sử dụng để tương tác với database MySQL thông qua các Model PHP thay vì viết raw SQL, giúp code an toàn hơn (tránh SQL injection) và dễ bảo trì. Eloquent là ORM mặc định của Laravel, hỗ trợ relationships (hasMany, belongsTo) để lấy dữ liệu liên quan một cách dễ dàng. Nó tự động map table với Model, giảm boilerplate code, và hỗ trợ query builder fluent API dễ đọc và viết.

So với các lựa chọn khác, Doctrine (PHP) powerful nhưng phức tạp với learning curve cao. Raw SQL queries flexible nhưng dễ bị SQL injection và khó bảo trì. Propel (PHP) cũ hơn với community support giảm.

### 3.4.3. Laravel Sanctum (Authentication & Authorization)

Laravel Sanctum được sử dụng để xác thực người dùng qua email/password, tạo token cho API request, và kiểm tra quyền truy cập dựa trên vai trò (Admin, Management, Accountant, Employee). Sanctum được thiết kế cho SPA (Single Page Application), phù hợp với Vue frontend + Laravel backend. Token-based authentication giúp API stateless và dễ scale horizontal. Middleware có sẵn để kiểm tra vai trò (role:admin, role:management, v.v.), và hỗ trợ mã hóa mật khẩu tự động (bcrypt).

So với các lựa chọn khác, JWT (JSON Web Tokens) tương tự Sanctum nhưng yêu cầu setup thêm package và không có sẵn trong Laravel. Session-based (traditional) đơn giản nhưng không phù hợp cho API vì yêu cầu stateful server. OAuth2 quá phức tạp cho internal application này.

### 3.4.4. PHPUnit (Testing Framework)

PHPUnit được sử dụng để viết unit tests và feature tests, bảo đảm logic nghiệp vụ hoạt động đúng (tạo đơn nghỉ, duyệt lương, v.v.) trước khi triển khai production. PHPUnit là testing framework chuẩn cho PHP, được tích hợp trong Laravel. Nó hỗ trợ mock objects để test feature mà không cần database thực, cho phép test toàn bộ HTTP request → response flow.

So với các lựa chọn khác, Codeception powerful hơn nhưng phức tạp với overhead cho đồ án. Behat là BDD framework, phù hợp hơn cho acceptance tests nhưng không phải unit tests.

## 3.5. Công nghệ Cơ sở dữ liệu

### 3.5.1. MySQL 8.0

MySQL 8.0 được sử dụng để lưu trữ toàn bộ dữ liệu hệ thống bao gồm thông tin nhân viên, đơn nghỉ phép, bảng lương, chấm công, đánh giá hiệu suất, và thông báo. MySQL là cơ sở dữ liệu relational phổ biến, ổn định, có hiệu năng tốt cho quy mô vừa (nội bộ công ty). Nó hỗ trợ ACID transactions bảo đảm tính nhất quán dữ liệu, quan trọng cho các phép tính lương. MySQL dễ sao lưu, restore, và quản lý qua các tools như MySQL Workbench. Đặc biệt, MySQL miễn phí, open-source, không yêu cầu license.

So với các lựa chọn khác, PostgreSQL mạnh hơn với hỗ trợ JSON tốt nhưng không cần thiết cho quy mô này. MongoDB (NoSQL) có flexible schema nhưng không thích hợp cho dữ liệu có relationships rõ ràng như nhân sự. SQL Server powerful nhưng đắt (license) và setup phức tạp.

### 3.5.2. Database Migrations

Database Migrations được sử dụng để quản lý lịch sử thay đổi schema database theo version, giúp dễ rollback hoặc sync schema giữa các môi trường. Laravel migration là cơ chế mặc định, cho phép định nghĩa schema dưới dạng code (Infrastructure as Code). Điều này giúp dễ chia sẻ giữa các thành viên team mà không cần export/import SQL file thủ công.

## 3.6. Công nghệ Infrastructure & Deployment

### 3.6.1. Docker & Docker Compose

Docker và Docker Compose được sử dụng để đóng gói backend (PHP-FPM + Laravel), frontend (Node.js + Vue), database (MySQL), và web server (Nginx) thành các container, giúp development environment giống production environment. Công nghệ này đảm bảo yêu cầu phi chức năng "triển khai nhất quán" giữa local, staging, và production. Docker giảm thời gian setup môi trường phát triển mới, chỉ cần cài Docker, dễ scale horizontal (thêm nhiều instance) nếu cần, và hỗ trợ một lệnh `docker-compose up` để start toàn bộ hệ thống.

So với các lựa chọn khác, Kubernetes quá phức tạp cho quy mô đồ án. Vagrant + VirtualBox cũ hơn và tạo overhead disk & RAM. Manual setup (Apache/Nginx + PHP + MySQL) mất thời gian và dễ inconsistency.

Cấu hình Docker Compose StaffHub gồm bốn service chính: app service (PHP 8.2 FPM) chạy Laravel backend, web service (Nginx Alpine) hoạt động như reverse proxy và server static files, frontend service (Node 20 Alpine) chạy Vue 3 + Vite development server trên port 5173, db service (MySQL 8.0) lưu trữ cơ sở dữ liệu StaffHub. Các service kết nối qua bridge network `staffhub_net` để giao tiếp nội bộ.

### 3.6.2. Nginx (Web Server)

Nginx được sử dụng để nhận HTTP request từ trình duyệt (port 8080), forward request API tới PHP-FPM backend, serve static files (JS, CSS) từ public folder. Nginx nhẹ hơn Apache, hỗ trợ reverse proxy tốt, hỗ trợ URL rewrite để route toàn bộ request đến index.php (Frontend routing), với cấu hình đơn giản và hiệu năng cao.

So với các lựa chọn khác, Apache nặng hơn với cấu hình phức tạp (.htaccess). Caddy modern hơn nhưng có ít support hơn Nginx.

## 3.7. Công nghệ Hỗ trợ

### 3.7.1. Maatwebsite/Excel (Data Export)

Maatwebsite/Excel được sử dụng để xuất danh sách nhân viên và bảng lương ra file Excel (.xlsx) giúp người dùng có thể tải về và xử lý tiếp. Maatwebsite/Excel (PhpSpreadsheet) là package chuẩn trong Laravel ecosystem, hỗ trợ định dạng phức tạp (style, formula, freeze rows), và có sẵn trong backend mà không cần setup thêm.

So với các lựa chọn khác, OpenPyXL (Python) cần server Python riêng nên phức tạp. CSV + Excel online conversion mất functionality như style và formula.

### 3.7.2. Bootstrap 5 (CSS Framework)

Bootstrap 5 được sử dụng để cung cấp utilities CSS cơ bản (grid system, responsive utilities) hỗ trợ Ant Design Vue components. Bootstrap là CSS framework phổ biến nhất, dễ học và dùng, tích hợp tốt với Ant Design Vue mà không có conflict.

## 3.8. Kết luận
Lựa chọn công nghệ cho StaffHub dựa trên ba tiêu chí chính. Thứ nhất, các công nghệ được chọn phải đáp ứng đầy đủ các yêu cầu chức năng và phi chức năng được nêu ở Chương 2, bao gồm khả năng xử lý hiệu năng thông qua tối ưu chỉ mục cơ sở dữ liệu và phản hồi API nhanh, tính bảo mật bằng cơ chế xác thực token và phân quyền theo vai trò, cũng như khả năng bảo trì nhờ tách biệt rõ frontend và backend. Thứ hai, các giải pháp phải phù hợp với quy mô dự án và năng lực phát triển; Vue kết hợp Laravel là một stack phổ biến, có cộng đồng rộng và tài liệu phong phú, giúp giảm rủi ro vận hành và dễ tìm nguồn hỗ trợ. Thứ ba, yếu tố triển khai và vận hành được cân nhắc kỹ, Docker Compose được sử dụng để đảm bảo tính nhất quán giữa các môi trường triển khai, trong khi PHPUnit và migration hỗ trợ đảm bảo chất lượng mã nguồn và quản lý schema. Mô hình kiến trúc ba lớp (Presentation – Application – Data) được giữ để hệ thống dễ mở rộng; trong tương lai nếu cần tích hợp mobile app hoặc hệ thống bên ngoài, backend API hiện có có thể tái sử dụng mà không đòi hỏi thay đổi lớn.

---

**Tài liệu tham khảo:**
- Vue.js 3 Official Documentation. Available at: https://vuejs.org/
- Laravel Framework Official Documentation. Available at: https://laravel.com/
- Docker Documentation. Available at: https://docs.docker.com/
- MySQL 8.0 Reference Manual. Available at: https://dev.mysql.com/doc/
- Ant Design Vue Documentation. Available at: https://www.antdv.com/
