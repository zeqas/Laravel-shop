# 簡易電商系統

這是一個使用 Laravel 建立的簡易電商系統。

## 環境要求

-   PHP >= 8.2
-   Composer
-   MySQL
-   Nginx
-   Docker
-   Docker Compose

## 安裝步驟

```bash
git clone https://github.com/zeqas/Laravel-shop.git  && cd ./Laravel-shop # 複製到本機
cp .env.example .env # 建立 .env 檔
```

設定資料庫相關參數

```env
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=docker
    DB_USERNAME=root
    DB_PASSWORD=1Qaz2Wsx
```

### 使用 docker-compose 建立環境

使用前請確認 ports 的號碼是否已經被使用

```bash
docker-compose up --build -d # 建立容器
docker exec -it app bash # 進入 app 容器
```

```bash
php artisan key:generate # 生成 APP_KEY
php artisan migrate # 資料庫遷移
```

## 基本使用

目前有基礎首頁
但是在未登入的情況下，只能瀏覽，嘗試新增商品時會回應 401 Unauthorized 錯誤

建議透過 Postman 使用相關 API
除了 Controller 的註解之外，可以使用 scribe 生成文件

```bash
php artisan scribe:generate # 透過 scribe 生成文件
```

到 [URL]/docs （例如：http://0.0.0.0:8000/docs）查看相關文件

### 種子資料

可以先執行種子資料，生成各 10 筆的商品與使用者

```bash
php artisan db:seed
```

## 已完成功能

-   商品列表：顯示所有商品的名稱和價格。
-   商品搜尋：根據名稱和價格區間搜尋商品。
-   新增商品：添加新的商品到系統中。
-   編輯商品：修改現有商品的名稱和價格。
-   刪除商品：從系統中移除商品。

-   用戶註冊與登錄

## 待完成功能

-   購物車功能
-   簡易結算流程
-   管理員後台（商品管理、訂單管理）

## 主要技術展示

-   Laravel

    -   API 功能
    -   CRUD
    -   API validation
    -   身份驗證 (middleware)
    -   Migration
    -   Seeder
    -   資料 Model 之間的 relation
    -   Schedule (Optional)
        -   API 分層 (Controller, Service, Repository)

-   docker-compose.yml [MySQL + PHP + Redis] (Optional)
-   API Test
-   API Doc
-   CI Github Action (跑測試 + 產生 API 文件傳到 Github page)

-   MySQL
-   Bootstrap
-   jQuery

## 授權

此項目使用 MIT 授權。請查看 LICENSE 檔案以獲取更多資訊。
