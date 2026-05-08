# Lộ Trình Tích Hợp CI với GitHub Actions - StaffHub

## 📋 Tóm Tắt

Dự án StaffHub đã được setup sẵn **2 workflow CI chính**:
- `ci-backend.yml`: Test Laravel backend + PHPUnit + MySQL migrations
- `ci-frontend.yml`: Build Vue 3 app trên Node 18 & 20

Mỗi khi push PR hoặc commit vào `main/develop/staging`, GitHub tự động:
- ✅ Chạy unit tests backend
- ✅ Chạy database migrations  
- ✅ Build frontend app
- ✅ Báo lỗi nếu có

---

## 🚀 Hướng Dẫn Thực Hiện

### Bước 1: Commit Workflow Files

Workflow files đã tạo sẵn trong `.github/workflows/`:

```bash
# Từ root StaffHub folder
git add .github/
git add backend/.env.example
git commit -m "feat: add GitHub Actions CI workflows for backend and frontend"
git push origin main
```

**Cấu trúc tạo ra:**
```
.github/
  workflows/
    ├── ci-backend.yml
    └── ci-frontend.yml
```

---

### Bước 2: Kiểm Tra CI Chạy Lần Đầu

Sau khi push:

1. Vào **GitHub repo** → tab **Actions**
2. Xem workflows chạy:
   - `Backend CI` job: chạy phpunit + migrations (3-5 phút)
   - `Frontend CI` job: build vite trên Node 18 & 20 (2-3 phút)

3. **Nếu fail**: Xem chi tiết logs → fix code → push lại

---

### Bước 3: Cấu Hình Branch Protection (Bắt Buộc CI Pass)

Để PR phải pass CI mới merge được:

1. **GitHub repo** → **Settings** → **Branches**
2. Click **Add branch protection rule**
3. Điền:
   - **Branch name pattern**: `main`
   - ✅ Check: **Require status checks to pass before merging**
   - ✅ Check: **Require branches to be up to date before merging**
4. Click **Create**

**Kết quả**: PR không merge được nếu CI chưa pass ✔️

---

### Bước 4: Setup Local để Match CI Environment

**Backend** (chạy local như CI):
```bash
cd backend

# Cài dependencies
composer install

# Copy .env.example
cp .env.example .env

# Generate key
php artisan key:generate

# Migrate database
php artisan migrate

# Chạy tests (như CI)
./vendor/bin/phpunit
```

**Frontend** (chạy local như CI):
```bash
cd frontend

# Cài dependencies
npm ci  # hoặc npm install

# Build (như CI)
npm run build
```

---

## 📊 Chi Tiết Workflows

### **ci-backend.yml**

**Trigger**: Push/PR đến `main/develop/staging` với thay đổi trong `backend/`

**Jobs**:
| Step | Chi tiết |
|------|----------|
| Checkout | Clone code từ GitHub |
| Setup PHP 8.2 | Cài PHP 8.2 + extensions |
| Cache composer | Cache dependencies để tăc tốc |
| Install dependencies | Chạy `composer install` |
| Copy .env.testing | Sử dụng `.env.example` cho test |
| Generate key | Tạo APP_KEY |
| Wait for MySQL | Chờ MySQL service khởi động |
| Run migrations | `php artisan migrate --env=testing` |
| Run PHPUnit | `./vendor/bin/phpunit` |
| Upload results | Lưu test results nếu fail |

**Services** (MySQL):
- Image: `mysql:8.0`
- Database: `staffhub_test`
- Username: `root`
- Password: `root`

---

### **ci-frontend.yml**

**Trigger**: Push/PR đến `main/develop/staging` với thay đổi trong `frontend/`

**Matrix Strategy**: Test trên **2 phiên bản Node**:
- Node 18.x
- Node 20.x

**Jobs**:
| Step | Chi tiết |
|------|----------|
| Checkout | Clone code từ GitHub |
| Setup Node | Cài Node.js (18 hoặc 20) |
| Cache npm | Cache node_modules để tăc tốc |
| Install | `npm ci` (better than npm install for CI) |
| Build | `npm run build` tạo `dist/` folder |
| Upload artifact | Lưu `dist/` để download hoặc deploy |

**Kết quả**: `dist/` folder được tạo sẵn, có thể dùng để deploy

---

## 💡 Quy Trình Làm Việc Hàng Ngày

### **Không có CI** (cũ):
```
Developer A → Code → Push → Merge → ❌ ERROR (phát hiện muộn)
```

### **Có CI** (mới):
```
Developer A → Code → Push → [CI runs tests] →
  ✅ Pass: Merge OK
  ❌ Fail: Block merge → Developer A fix → Push again → [CI runs again]
```

**Ví dụ kịch bản**:

1. Developer A tạo PR: `fix/employee-edit-bug`
2. Push code → GitHub Actions trigger
3. Kết quả:
   - ✅ Backend CI: PHPUnit 24/24 pass
   - ✅ Frontend CI: Build successful
4. → PR merge OK

Nếu có lỗi:
- ❌ Backend CI: PHPUnit 1/24 fail
- → PR bị block
- → Xem logs, fix code
- → Push again → CI chạy lại
- → ✅ Pass → Merge OK

---

## 🔧 Quản Lý GitHub Secrets (Tùy Chọn)

Nếu cần lưu mật khẩu/token mà không hiển thị trong code:

1. **GitHub repo** → **Settings** → **Secrets and variables** → **Actions**
2. Click **New repository secret**
3. Ví dụ:
   - **Name**: `DB_PASSWORD_PROD`
   - **Value**: `your_actual_password`
4. Click **Add secret**

**Sử dụng trong workflow**:
```yaml
env:
  DB_PASSWORD: ${{ secrets.DB_PASSWORD_PROD }}
```

---

## 📝 Thêm Badge CI vào README

Hiển thị trạng thái CI trên GitHub repo:

**Thêm vào `README.md` root hoặc `backend/README.md`**:

```markdown
## CI/CD Status

[![Backend CI](https://github.com/YOUR_USERNAME/StaffHub/actions/workflows/ci-backend.yml/badge.svg)](https://github.com/YOUR_USERNAME/StaffHub/actions/workflows/ci-backend.yml)
[![Frontend CI](https://github.com/YOUR_USERNAME/StaffHub/actions/workflows/ci-frontend.yml/badge.svg)](https://github.com/YOUR_USERNAME/StaffHub/actions/workflows/ci-frontend.yml)
```

Thay `YOUR_USERNAME` bằng username GitHub thực tế.

---

## 🐛 Troubleshooting

### **CI Backend fail: "Connection refused" khi chạy migrations**

**Nguyên nhân**: MySQL chưa khởi động

**Fix**: CI đã có `Wait for MySQL` step, kiểm tra logs xem MySQL có error không

### **CI Frontend fail: "npm ERR! 404 Not Found"**

**Nguyên nhân**: Package trong `package.json` không tồn tại hoặc npm registry lỗi

**Fix**: Chạy `npm ci` local, fix `package-lock.json`, push lại

### **CI pass local nhưng fail trên GitHub**

**Nguyên nhân**: Environment khác (PHP version, MySQL version, Node version)

**Fix**: Kiểm tra workflow sử dụng PHP 8.2, MySQL 8.0, Node 18/20 giống config local

---

## ✅ Checklist Hoàn Thành

- [ ] Commit `.github/workflows/` files
- [ ] Xem Actions tab → CI chạy
- [ ] Cấu hình branch protection cho `main` branch
- [ ] Test: Tạo PR → xem CI pass
- [ ] Thêm badges vào README (tùy chọn)
- [ ] Chúc mừng! 🎉

---

## 📚 Tài Liệu Tham Khảo

- GitHub Actions: https://docs.github.com/en/actions
- GitHub Actions Best Practices: https://docs.github.com/en/actions/guides
- PHPUnit: https://phpunit.de/
- Vite: https://vitejs.dev/
- Laravel Testing: https://laravel.com/docs/9.x/testing

---

## 🆘 Cần Giúp?

Nếu CI fail, hãy:
1. Xem **Actions** tab → chi tiết logs
2. Sao chép error message
3. Chạy local: `composer test` (backend) hoặc `npm run build` (frontend)
4. Fix code → push lại
