FROM docker_wordpress-base:latest

ENV TEST_DIR_PATH /var/www/tests/

# Install and configure xdebug.
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.extension.ini
COPY ./docker/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Install PHP_CodeSniffer
RUN composer require dealerdirect/phpcodesniffer-composer-installer
RUN composer require wp-coding-standards/wpcs

# Install and configure PHPUnit.
RUN composer require --dev phpunit/phpunit ^7
COPY ./docker/phpunit.xml .

# Fetch and configure WordPress test tools.
RUN git clone -b "$WORDPRESS_VERSION" --single-branch --depth 1 https://github.com/WordPress/wordpress-develop.git wordpress-develop \
  && mkdir ${TEST_DIR_PATH} \
  && cp -R ./wordpress-develop/tests/phpunit/includes ${TEST_DIR_PATH}/includes \
  && cp -R ./wordpress-develop/tests/phpunit/data ${TEST_DIR_PATH}/data

COPY ./docker/wp-tests-config.php ${TEST_DIR_PATH}
