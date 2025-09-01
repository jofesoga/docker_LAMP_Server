FROM ubuntu:16.04
MAINTAINER Jose Sosa <jofesoga@gmail.com>
LABEL Description="Cutting-edge LAMP stack, based on Ubuntu 16.04 LTS. Includes .htaccess support and popular PHP7 features, including composer and mail() function." \
	License="Apache License 2.0" \
	Usage="docker run -d -p [HOST WWW PORT NUMBER]:80 -p [HOST DB PORT NUMBER]:3306 -v [HOST WWW DOCUMENT ROOT]:/var/www/html -v [HOST DB DOCUMENT ROOT]:/var/lib/mysql fauria/lamp" \
	Version="1.0"

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update
RUN apt-get upgrade -y
RUN ln -s -f /bin/true /usr/bin/chfn

COPY debconf.selections /tmp/
RUN debconf-set-selections /tmp/debconf.selections

RUN apt-get install -y zip unzip
RUN apt-get install -y \
	php7.0 \
	php7.0-bz2 \
	php7.0-cgi \
	php7.0-cli \
	php7.0-common \
	php7.0-curl \
	php7.0-dev \
	php7.0-enchant \
	php7.0-fpm \
	php7.0-gd \
	php7.0-gmp \
	php7.0-imap \
	php7.0-interbase \
	php7.0-intl \
	php7.0-json \
	php7.0-ldap \
	php7.0-mbstring \
	php7.0-mcrypt \
	php7.0-mysql \
	php7.0-odbc \
	php7.0-opcache \
	php7.0-pgsql \
	php7.0-phpdbg \
	php7.0-pspell \
	php7.0-readline \
	php7.0-recode \
	php7.0-snmp \
	php7.0-sqlite3 \
	php7.0-sybase \
	php7.0-tidy \
	php7.0-xmlrpc \
	php7.0-xsl \
	php7.0-zip
RUN echo exit 101 > /usr/sbin/policy-rc.d
RUN apt-get install apache2 libapache2-mod-php7.0 -y
RUN echo exit 101 > /usr/sbin/policy-rc.d
RUN apt-get install mariadb-common mariadb-server mariadb-client -y
RUN echo exit 101 > /usr/sbin/policy-rc.d
RUN apt-get install nodejs


ENV LOG_STDOUT **Boolean**
ENV LOG_STDERR **Boolean**
ENV LOG_LEVEL warn
ENV ALLOW_OVERRIDE All
ENV DATE_TIMEZONE UTC
ENV TERM dumb


COPY add_customer.php /var/www/html/
COPY add_product.php /var/www/html/
COPY config.php /var/www/html/
COPY create_order.php /var/www/html/
COPY customers.php /var/www/html/
COPY dashboard.php /var/www/html/
COPY edit_product.php /var/www/html/
COPY functions.php /var/www/html/
COPY invoice.php /var/www/html/
COPY login.php /var/www/html/
COPY logout.php /var/www/html/
COPY navbar.php /var/www/html/
COPY orders.php /var/www/html/
COPY products.php /var/www/html/
COPY pwd.php /var/www/html/
COPY reports.php /var/www/html/
COPY testconn.php /var/www/html/
COPY index.php /var/www/html/

COPY  --chmod=755 run-lamp.sh /usr/sbin/


VOLUME /var/www/html
VOLUME /var/log/httpd
VOLUME /var/lib/mysql
VOLUME /var/log/mysql
VOLUME /etc/apache2

EXPOSE 80
EXPOSE 3306

