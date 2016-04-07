FROM ubuntu:14.04.3

MAINTAINER warwing@gmx.de

# this is a non-interactive automated build - avoid some warning messages
ENV DEBIAN_FRONTEND noninteractive

# CONFIGURATION VARIABLES
ARG TINE20_VERSION=2016.03.1
ARG TINE20_SERVER_NAME=localhost
ARG TINE20_SERVER_ALIAS=localhost
ARG TINE20_DB_HOST=172.17.0.1
ARG TINE20_DB_USER
ARG TINE20_DB_PASS
ARG TINE20_SETUP_USER
ARG TINE20_SETUP_PASS

# update dpkg repositories
RUN apt-get update

# install required packages
# Ubuntu 16.04
# RUN apt-get -y install apache2 curl less libapache2-mod-php5 php5 php5-curl php5-gd php5-mysql php5-xsl php-mcrypt php-pear php-xdebug sed unzip
# Ubuntu 14.04.3
RUN apt-get -y install apache2 curl less libapache2-mod-php5 php5 php5-curl php5-gd php5-mysql php5-xsl php5-mcrypt php-pear php5-xdebug sed unzip


# remove download archive files
RUN apt-get clean

# Install composer - PHP dependency manager
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Allow modules for Apache.
RUN a2enmod rewrite

# Update mcrypt for Ubuntu 14.04.3
RUN php5enmod mcrypt

# Disable apache default sites
RUN a2dissite 000-default

# Install Tine 2.0 to /tine20 folder
RUN mkdir -p /tine20/cache /tine20/conf_templates /tine20/etc /tine20/web_docroot /tine20/files /tine20/log /tine20/tmp
ADD http://packages.tine20.org/source/$TINE20_VERSION/tine20-allinone_$TINE20_VERSION.zip /tmp/tine20-allinone_$TINE20_VERSION.zip
RUN cd /tine20/web_docroot && unzip /tmp/tine20-allinone_$TINE20_VERSION.zip && rm /tmp/tine20-allinone_$TINE20_VERSION.zip 

# Implement hack to support contacts of OS X El Capitan
COPY FindAddressbookContainer.php /tine20/web_docroot/FindAddressbookContainer.php

# Tine 2.0 Vhost
COPY tine20-vhost.conf /tine20/conf_templates/ 
RUN cat /tine20/conf_templates/tine20-vhost.conf | \
       sed "s/__SERVER_NAME__/$TINE20_SERVER_NAME/g" | \
       sed "s/__SERVER_ALIAS__/$TINE20_SERVER_ALIAS/g" > /etc/apache2/sites-available/tine20.conf

# Tine 2.0 config
COPY config.inc.php /tine20/conf_templates/ 
RUN cat /tine20/conf_templates/config.inc.php | \
       sed "s/__TINE20_DB_HOST__/$TINE20_DB_HOST/g" | \
       sed "s/__TINE20_DB_USER__/$TINE20_DB_USER/g" | \
       sed "s/__TINE20_DB_PASS__/$TINE20_DB_PASS/g" | \
       sed "s/__TINE20_SETUP_USER__/$TINE20_SETUP_USER/g" | \
       sed "s/__TINE20_SETUP_PASS__/$TINE20_SETUP_PASS/g" > /tine20/etc/config.inc.php

RUN chown -R www-data:www-data /tine20

RUN a2ensite tine20
RUN service apache2 restart

ADD run-apache-with-stdout-logging.sh /tine20/run-apache-with-stdout-logging.sh
RUN chmod 755 /tine20/run-apache-with-stdout-logging.sh

EXPOSE 80

WORKDIR /tine20

CMD ["/tine20/run-apache-with-stdout-logging.sh"]

# Updating existing installation: php -d include_path=/tine20/etc setup.php --update
