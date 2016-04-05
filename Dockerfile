FROM ubuntu:16.04

MAINTAINER warwing@gmx.de

# this is a non-interactive automated build - avoid some warning messages
ENV DEBIAN_FRONTEND noninteractive

# CONFIGURATION VARIABLES
ENV TINE20_VERSION 2016.03
ENV TINE20_SERVER_NAME localhost
ENV TINE20_SERVER_ALIAS localhost
ENV TINE20_DB_HOST 172.17.0.1
ENV TINE20_DB_USER
ENV TINE20_DB_PASS
ENV TINE20_SETUP_USER
ENV TINE20_SETUP_PASS

# update dpkg repositories
RUN apt-get update

# install required packages
RUN apt-get -y install apache2 curl less libapache2-mod-php5 php5 php5-curl php5-gd php5-mysql php5-xsl php-mcrypt php-pear php-xdebug sed unzip

# remove download archive files
RUN apt-get clean

# Install composer - PHP dependency manager
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Allow modules for Apache.
RUN a2enmod rewrite

# Disable apache default sites
RUN a2dissite 000-default

# Install Tine 2.0 to /tine20 folder
RUN mkdir -p /tine20/cache
RUN mkdir -p /tine20/etc
RUN mkdir -p /tine20/web_docroot
RUN mkdir -p /tine20/files
RUN mkdir -p /tine20/log
RUN mkdir -p /tine20/tmp
ADD https://github.com/tine20/Tine-2.0-Open-Source-Groupware-and-CRM/archive/$TINE20_VERSION.zip /tmp/tine20-$TINE20_VERSION.zip
RUN cd /tine20/web_docroot && unzip /tmp/tine20-$TINE20_VERSION.zip && rm /tmp/tine20-$TINE20_VERSION.zip && \
    mv Tine-2.0-Open-Source-Groupware-and-CRM-2016.03/tine20/* ./ && rm -rf Tine-2.0-Open-Source-Groupware-and-CRM-2016.03/ 


# Tine 2.0 Vhost
COPY tine20-vhost.conf /tmp/ 
RUN cat /tmp/tine20-vhost.conf | \
       sed "s/__SERVER_NAME__/$TINE20_SERVER_NAME/g" | \
       sed "s/__SERVER_ALIAS__/$TINE20_SERVER_ALIAS/g" > /etc/apache2/sites-available/tine20.conf

# Tine 2.0 config
COPY config.inc.php /tmp/
RUN cat /tmp/config.inc.php | \
       sed "s/__TINE20_DB_HOST__/$TINE20_DB_HOST/g" | \
       sed "s/__TINE20_DB_USER__/$TINE20_DB_USER/g" | \
       sed "s/__TINE20_DB_PASS__/$TINE20_DB_PASS/g" | \
       sed "s/__TINE20_SETUP_USER__/$TINE20_SETUP_USER/g" | \
       sed "s/__TINE20_SETUP_PASS__/$TINE20_SETUP_PASS/g" > /tine20/etc/config.inc.php
       