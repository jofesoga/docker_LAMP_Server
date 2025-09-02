FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y apache2 php libapache2-mod-php php-mysql -y
RUN apt-get install nodejs -y
RUN apt-get install vim -y
RUN apt-get install npm -y
RUN npm install -g bower grunt-cli gulp

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

EXPOSE 80
CMD ["apache2ctl", "-D", "FOREGROUND"]
