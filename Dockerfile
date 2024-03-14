FROM php:8.3-fpm

# 安裝依賴
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

# 清理快取
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 安裝 PHP 擴展
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/
RUN docker-php-ext-install gd

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 設定工作目錄
WORKDIR /var/www/html

# 複製現有的應用程式檔案到 Docker 容器中
COPY . /var/www/html

# 安裝 PHP 依賴
RUN composer install

# 設定權限
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# 啟動 PHP-FPM
CMD ["php-fpm"]
