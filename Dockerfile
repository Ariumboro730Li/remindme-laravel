# Install PHP and composer

# FROM php:8.3.1RC3-fpm
FROM php:8.2-fpm

# Install PDO and MySQL extensions (already in your original Dockerfile)
RUN docker-php-ext-install pdo pdo_mysql

# Install cURL extension
RUN docker-php-ext-install curl

# Install cURL
RUN apt-get update && apt-get install -y curl

RUN apt-get update && apt-get install -y libcurl4-openssl-dev

RUN apt-get update && \
    apt-get install -y libldap2-dev && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install ldap

# Update package lists and install dependencies
RUN apt-get update && apt-get upgrade -y \
    && apt-get install -y zlib1g-dev libzip-dev

# install nodejs and npm extension
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y nodejs \
    npm

# Install Nginx
RUN apt-get update && apt-get install -y nginx

# Check if the Zip extension is installed
RUN if ! [ -z "$(php -m | grep zip)" ]; then \
        echo "Zip extension is already installed."; \
    else \
        echo "Installing Zip extension..." \
        && pecl install zip \
        && docker-php-ext-enable zip \
        && echo "Zip extension installed successfully."; \
    fi

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN printf "memory_limit = 256M" > /usr/local/etc/php/conf.d/manual-conf.ini
RUN printf "post_max_size = 100M" > /usr/local/etc/php/conf.d/manual-conf.ini
RUN printf "max_file_uploads = 100" > /usr/local/etc/php/conf.d/manual-conf.ini


