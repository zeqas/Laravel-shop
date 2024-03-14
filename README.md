# 簡易電商系統

這是一個使用 Laravel 建立的簡易電商系統。

## 環境要求

-   PHP >= 7.4
-   Composer
-   Node.js
-   MySQL
-   Nginx

## 安裝步驟

```bash
git clone https://github.com/zeqas/Laravel-shop.git  && cd /Laravel-shop #1 複製到本機
composer install #2 安裝套件
php artisan serve #3 啟動伺服器
php artisan migrate #4 資料庫遷移
```

## 已完成功能

-   產品列表：顯示所有產品的名稱和價格。
-   產品搜尋：根據名稱和價格區間搜尋產品。
-   新增產品：添加新的產品到系統中。
-   編輯產品：修改現有產品的名稱和價格。
-   刪除產品：從系統中移除產品。

## 待完成功能

-   購物車功能
-   用戶註冊與登錄
-   簡易結算流程
-   管理員後台（產品管理、訂單管理）

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
