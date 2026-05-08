# Lộ Trình Tích Hợp CI (Continuous Integration) cho StaffHub

## 1. CI là gì?

**CI (Continuous Integration)** = **Tích hợp liên tục**. Mỗi khi bạn push code lên GitHub, hệ thống tự động:
- ✅ Chạy unit tests (kiểm tra logic)
- ✅ Chạy linting (kiểm tra format code)
- ✅ Build ứng dụng (kiểm tra syntax)
- ✅ Build Docker image (kiểm tra deploy)

Nếu bất kỳ bước nào **FAIL** → PR bị block (không thể merge), bạn biết code có vấn đề **trước** khi merge vào main.

---

## 2. Mục tiêu CI cho StaffHub

| Mục tiêu | Cách thực hiện |
|----------|----------------|
| **Backend chất lượng** | Chạy phpunit, phpstan, phpcs trên mỗi commit |
| **Frontend không lỗi** | Chạy eslint, build vite trên mỗi commit |
| **Database an toàn** | Test migrations, seed dữ liệu, rollback |
| **Docker hoạt động** | Build Docker image, kiểm tra Dockerfile lúc push |
| **Merge an toàn** | PR phải pass CI trước khi merge vào main |

---

## 3. Lộ Trình Chi Tiết (10 Bước)

### **Bước 1: Chuẩn Bị - Kiểm Tra Công Cụ Hiện Có**

**Mục đích**: Xác định lệnh test/build đã có sẵn.

**Hành động**:
```bash
# Backend: Kiểm tra Laravel đã sẵn PHPUnit chưa
cd backend
Get-Content composer.json | Select-String "phpunit"

# Chạy test thử
./vendor/bin/phpunit

# Frontend: Kiểm tra Node đã sẵn ESLint chưa
cd frontend
Get-Content package.json | Select-String "eslint"

# Chạy lint thử
npm run lint  # hoặc npm run lint:fix
npm run build
```

**Kết quả mong đợi**:
- Backend: `./vendor/bin/phpunit` chạy được (pass hoặc fail vài tests, không quan trọng)
- Frontend: `npm run lint` chạy được, `npm run build` tạo ra `dist/` folder

---

### **Bước 2: Tạo Cấu Trúc Folder CI**

**Mục đích**: Tạo thư mục chứa các workflow GitHub Actions.

**Hành động**:
```bash
# Từ root StaffHub
mkdir -p .github/workflows
```

**Cấu trúc sau bước này**:
```
StaffHub/
  .github/
    workflows/
      (các file .yml sẽ tạo ở bước sau)
```

---

### **Bước 3: Tạo Workflow Backend CI**

**Mục đích**: Mỗi lần push backend, GitHub tự động chạy test + lint.

**File cần tạo**: `.github/workflows/ci-backend.yml`

```yaml
name: Backend CI

on:
  push:
    branches: [main, develop]
    paths:
      - 'backend/**'
      - '.github/workflows/ci-backend.yml'
  pull_request:
    branches: [main, develop]
    paths:
      - 'backend/**'

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: staffhub_test
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
        ports:
          - 3306:3306

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: pdo, pdo_mysql, bcmath, gd
          tools: composer

      - name: Cache composer packages
        uses: actions/cache@v3
        with:
          path: backend/vendor
          key: ${{ runner.os }}-composer-${{ hashFiles('backend/composer.lock') }}
          restore-keys: |
            ${{ runner.os }}-composer-

      - name: Install dependencies
        working-directory: backend
        run: composer install --no-interaction --prefer-dist

      - name: Copy .env
        working-directory: backend
        run: cp .env.example .env.testing

      - name: Generate key
        working-directory: backend
        run: php artisan key:generate --env=testing

      - name: Run migrations
        working-directory: backend
        env:
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: staffhub_test
          DB_USERNAME: root
          DB_PASSWORD: root
        run: php artisan migrate --env=testing

      - name: Run PHPUnit tests
        working-directory: backend
        env:
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: staffhub_test
          DB_USERNAME: root
          DB_PASSWORD: root
        run: ./vendor/bin/phpunit

      - name: Run PHPStan (Static Analysis)
        working-directory: backend
        continue-on-error: true
        run: |
          composer require --dev phpstan/phpstan --no-interaction
          ./vendor/bin/phpstan analyse app --level=5

      - name: Run PHPCS (Code Style)
        working-directory: backend
        continue-on-error: true
        run: |
          composer require --dev squizlabs/php_codesniffer --no-interaction
          ./vendor/bin/phpcs app --standard=PSR12
```

**Giải thích**:
- `on.push`: Trigger khi push lên `main` hoặc `develop`
- `on.pull_request`: Trigger khi tạo PR
- `services.mysql`: Tạo MySQL container tạm thời cho test
- `steps`: Các bước lần lượt (checkout code → cài PHP → cài dependencies → migrate DB → chạy test)

---

### **Bước 4: Tạo Workflow Frontend CI**

**Mục đích**: Mỗi lần push frontend, GitHub tự động chạy eslint + build.

**File cần tạo**: `.github/workflows/ci-frontend.yml`

```yaml
name: Frontend CI

on:
  push:
    branches: [main, develop]
    paths:
      - 'frontend/**'
      - '.github/workflows/ci-frontend.yml'
  pull_request:
    branches: [main, develop]
    paths:
      - 'frontend/**'

jobs:
  build:
    runs-on: ubuntu-latest

    strategy:
      matrix:
        node-version: [18.x, 20.x]

    steps:
      - uses: actions/checkout@v3

      - name: Setup Node.js ${{ matrix.node-version }}
        uses: actions/setup-node@v3
        with:
          node-version: ${{ matrix.node-version }}
          cache: 'npm'
          cache-dependency-path: frontend/package-lock.json

      - name: Install dependencies
        working-directory: frontend
        run: npm ci

      - name: Run ESLint
        working-directory: frontend
        run: npm run lint

      - name: Build Vue app
        working-directory: frontend
        run: npm run build

      - name: Upload build artifact
        uses: actions/upload-artifact@v3
        with:
          name: frontend-dist
          path: frontend/dist/
```

**Giải thích**:
- `matrix.node-version`: Test trên Node 18 và 20 (đảm bảo tương thích)
- `npm ci`: Install dependencies (phương pháp tốt hơn `npm install` cho CI)
- `npm run lint`: Chạy ESLint
- `npm run build`: Build production assets
- `upload-artifact`: Lưu `dist/` folder để download hoặc deploy

---

### **Bước 5: Cấu Hình .env.example (để CI biết các biến cần)**

**Mục đích**: CI không có `.env` thật, nó copy từ `.env.example` để test.

**Hành động**: Kiểm tra `backend/.env.example` đã có những biến nào:

```bash
cat backend/.env.example
```

**Đảm bảo có**:
```
APP_NAME=StaffHub
APP_ENV=local
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=staffhub_test
DB_USERNAME=root
DB_PASSWORD=root
```

---

### **Bước 6: Tạo Workflow Docker Build & Push**

**Mục đích**: Khi merge vào `main`, GitHub tự động build Docker image và push lên registry (Docker Hub hoặc GitHub Packages).

**File cần tạo**: `.github/workflows/build-and-push.yml`

```yaml
name: Build and Push Docker Images

on:
  push:
    branches: [main]

jobs:
  build-backend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Set up Docker Buildx
        uses: docker/setup-buildx-action@v2

      - name: Login to GitHub Container Registry
        uses: docker/login-action@v2
        with:
          registry: ghcr.io
          username: ${{ github.actor }}
          password: ${{ secrets.GITHUB_TOKEN }}

      - name: Build and push backend image
        uses: docker/build-push-action@v4
        with:
          context: ./backend
          file: ./backend/Dockerfile
          push: true
          tags: ghcr.io/${{ github.repository }}/staffhub-backend:latest

  build-frontend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Set up Docker Buildx
        uses: docker/setup-buildx-action@v2

      - name: Login to GitHub Container Registry
        uses: docker/login-action@v2
        with:
          registry: ghcr.io
          username: ${{ github.actor }}
          password: ${{ secrets.GITHUB_TOKEN }}

      - name: Build and push frontend image
        uses: docker/build-push-action@v4
        with:
          context: ./frontend
          file: ./frontend/Dockerfile
          push: true
          tags: ghcr.io/${{ github.repository }}/staffhub-frontend:latest
```

**Giải thích**:
- `on.push.branches: [main]`: Chỉ trigger khi merge vào main
- `ghcr.io`: GitHub Container Registry (nơi lưu Docker image)
- `${{ secrets.GITHUB_TOKEN }}`: Token tự động từ GitHub để push image

---

### **Bước 7: Cấu Hình Branch Protection**

**Mục đích**: Không cho phép merge PR nếu CI chưa pass.

**Hành động** (trên GitHub web):
1. Vào repo → **Settings** → **Branches**
2. Tìm **Branch protection rules**
3. Click **Add rule**
4. **Branch name pattern**: `main`
5. Check các tùy chọn:
   - ✅ **Require status checks to pass before merging**
   - ✅ **Require branches to be up to date before merging**
   - ✅ **Require code reviews before merging** (optional, 1-2 reviews)
   - ✅ **Dismiss stale pull request approvals**
6. Click **Create**

**Kết quả**: PR không merge được nếu CI chưa pass ✔️

---

### **Bước 8: Thêm GitHub Secrets (Nếu Cần)**

**Mục đích**: Lưu mật khẩu, token mà không hiển thị trong code.

**Hành động** (trên GitHub web):
1. **Settings** → **Secrets and variables** → **Actions**
2. Click **New repository secret**
3. Ví dụ:
   - **Name**: `DB_PASSWORD`
   - **Value**: `your_actual_password`
4. Click **Add secret**

**Sử dụng trong workflow**:
```yaml
env:
  DB_PASSWORD: ${{ secrets.DB_PASSWORD }}
```

---

### **Bước 9: Thêm CI Badge vào README**

**Mục đích**: Hiển thị trạng thái CI trên trang GitHub repo.

**Hành động**: Thêm vào `backend/README.md` hoặc root `README.md`:

```markdown
## CI/CD Status

[![Backend CI](https://github.com/your-username/StaffHub/actions/workflows/ci-backend.yml/badge.svg)](https://github.com/your-username/StaffHub/actions/workflows/ci-backend.yml)
[![Frontend CI](https://github.com/your-username/StaffHub/actions/workflows/ci-frontend.yml/badge.svg)](https://github.com/your-username/StaffHub/actions/workflows/ci-frontend.yml)
```

---

### **Bước 10: Kiểm Tra CI Chạy**

**Mục đích**: Đảm bảo toàn bộ workflow hoạt động.

**Hành động**:
1. Commit các file workflow vào git
2. Push lên GitHub
3. Vào **Actions** tab → xem workflows chạy
4. Kiểm tra logs chi tiết

**Chờ kết quả**:
- ✅ **Green** = Toàn bộ pass
- ❌ **Red** = Có lỗi (xem logs để fix)

---

## 4. Quy Trình Làm Việc Hàng Ngày với CI

### **Trước**: Merge PR bằng tay, không chắc chắn

```
developer → push → merge → 💥 ERROR IN PRODUCTION
```

### **Sau**: CI tự động kiểm tra

```
developer → push → [CI runs tests] → 
  - ✅ Pass → merge OK
  - ❌ Fail → PR blocked → developer fix → re-push → [CI runs again]
```

**Lợi ích**:
- 🛡️ Lỗi bị phát hiện sớm (trước merge)
- 📊 Có log/history của mỗi run
- 🚀 Deploy tự động sau khi merge (CD - Continuous Deployment)
- 👥 Team tin tưởng chất lượng code

---

## 5. Ví Dụ: Kịch Bản Thực Tế

### Kịch bản 1: Developer A fix bug

```
1. Tạo branch: git checkout -b fix/employee-edit
2. Code xong, commit: git commit -m "Fix employee edit bug"
3. Push: git push origin fix/employee-edit
4. Tạo PR trên GitHub
5. ⏳ CI tự động chạy (ci-backend.yml + ci-frontend.yml)
6. Kết quả:
   - PHPUnit: ✅ Pass (24 tests)
   - ESLint: ✅ Pass (0 warnings)
   - Build: ✅ Pass
7. PR merges → Build & Push workflow chạy
8. Docker image được push lên ghcr.io
9. Deploy team can pull image và deploy to staging/production
```

### Kịch bản 2: Developer B viết test không pass

```
1. Push code → CI chạy
2. PHPUnit: ❌ FAIL (1 test failed)
3. GitHub hiển thị: "Some checks were not successful"
4. PR bị block, không thể merge
5. Developer B xem logs → fix code
6. Push again → CI chạy lại
7. Lần này ✅ Pass → PR merge OK
```

---

## 6. Checklist Hoàn Thành

- [ ] Tạo folder `.github/workflows/`
- [ ] Tạo file `ci-backend.yml`
- [ ] Tạo file `ci-frontend.yml`
- [ ] Tạo file `build-and-push.yml` (tùy chọn)
- [ ] Kiểm tra `backend/.env.example` đúng
- [ ] Push tất cả lên GitHub
- [ ] Xem Actions tab → CI chạy
- [ ] Cấu hình branch protection (require CI pass)
- [ ] Thêm badges vào README
- [ ] Chúc mừng! 🎉 CI setup xong!

---

## 7. Tài Liệu Tham Khảo

- GitHub Actions Docs: https://docs.github.com/en/actions
- PHPUnit Docs: https://phpunit.de/
- ESLint Docs: https://eslint.org/
- Docker & GitHub Actions: https://docs.github.com/en/actions/publishing-packages/publishing-docker-images
