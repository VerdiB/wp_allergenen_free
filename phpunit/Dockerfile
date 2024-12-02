# Gebruik een specifieke Debian-basisimage
FROM debian:latest


# Installeer noodzakelijke tools en dependencies
RUN apt-get update && apt-get install -y curl \
php-cli \
php-xml \
php-mbstring \
unzip


# Installeer Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
&& php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
&& php -r "unlink('composer-setup.php');"

# Voeg composer to aan het PATH
ENV PATH="$PATH:/root/.composer/vendor/bin"
ENV COMPOSER_ALLOW_SUPERUSER=1
# # Voeg wp test config aan het PATH

# Installeer PHPUnit via Composer
RUN composer global require --dev phpunit/phpunit ^11 \
&& ln -s /root/.composer/vendor/bin/phpunit /usr/local/bin/phpunit

# Installeer WP-unit via Composer
# RUN composer global require --dev wp-phpunit/wp-phpunit ^6\
# && ln -s /root/.composer/vendor/bin/wpunit /usr/local/bin/wpunit

#  voeg config aan wordpress root toe
# COPY ../phpunit/unit/wp-tests-config.php /root/.composer/vendor/bin/wp-tests-config.php

WORKDIR /var/www/html/wordpress-tests

CMD phpunit --configuration phpunit.xml 2>&1 | tee phpunit.log

# ENTRYPOINT [ "phpunit", "--configuration", "phpunit.xml" ]

# ENTRYPOINT [ "phpunit", "--testsuite", unit ]