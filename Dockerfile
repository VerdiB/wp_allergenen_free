FROM wpdiaries/wordpress-xdebug:6.7.2-php8.4-apache AS base

ARG DOMAIN

#needs to run first and not in any other run commands 
RUN a2enmod ssl && \
    a2enmod rewrite

RUN apt-get update && apt-get install wget libnss3-tools -y && \
    curl -O  https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x wp-cli.phar &&\
    mv wp-cli.phar /usr/local/bin/wp && \
    # Make the localhost ssl by installing mkcert and making the certificates with it
    mkdir -p /etc/apache2/ssl && \ 
    wget https://github.com/FiloSottile/mkcert/releases/download/v1.4.3/mkcert-v1.4.3-linux-amd64 && \
    chmod +x mkcert-v1.4.3-linux-amd64 && \
    mv mkcert-v1.4.3-linux-amd64 /usr/local/bin/mkcert && \
    mkcert -install && \ 
    mkcert ${DOMAIN}

# moves the certificates towards the correct folder and adds the server name to the config file
RUN (echo "ServerName ${DOMAIN}" && cat /etc/apache2/apache2.conf) > apache2.conf && mv apache2.conf /etc/apache2/apache2.conf && \
    mv ${DOMAIN}.pem /etc/apache2/ssl/cert.pem && \
    mv ${DOMAIN}-key.pem /etc/apache2/ssl/cert-key.pem 


# Expose the ssl port and leaving the port 80 in as test port for function x or y
# EXPOSE 80
EXPOSE 443

# # Installing wordpress and plugins
# RUN wp core download --allow-root && \
# wp plugin install debug-bar query-monitor plugin-check --allow-root --activate

FROM base AS phpunit

# Installeer noodzakelijke tools en dependencies
RUN apt-get update && apt-get install -y curl \
    unzip

# Installeer Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Voeg composer to aan het PATH
ENV PATH="$PATH:/root/.composer/vendor/bin"
ENV COMPOSER_ALLOW_SUPERUSER=1
# Voeg wp test config aan het PATH

# Installeer PHPUnit via Composer
RUN composer global require --dev phpunit/phpunit ^11 \
    && ln -s /root/.composer/vendor/bin/phpunit /usr/local/bin/phpunit \
	&& vn co https://develop.svn.wordpress.org/trunk/ /usr/local/bin/wordpress-develop
ENV XDEBUG_MODE="coverage"

# Installeer WP-unit via Composer
# RUN composer global require --dev wp-phpunit/wp-phpunit ^6\
# && ln -s /root/.composer/vendor/bin/wpunit /usr/local/bin/wpunit

#  voeg config aan wordpress root toe
# COPY ../phpunit/unit/wp-tests-config.php /root/.composer/vendor/bin/wp-tests-config.php

WORKDIR /var/www/html/wordpress-tests

CMD phpunit --configuration phpunit.xml 2>&1 | tee phpunit.log

FROM atmoz/sftp:alpine AS sftp

ARG User

COPY ./wordpress /home/${User}/wordpress

RUN chmod -R 777 home/${User}/wordpress

