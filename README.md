# docker-tine20-external-mysql

## Motivation
The motivation for this docker configuration is, that I would like to have a clearly defined installation 
of [Tine 2.0](http://tine20.github.io/Tine-2.0-Open-Source-Groupware-and-CRM/) that is independent of 
installation directories. In my target environment I have a MySQL on my host that is used by other 
applications. So I decided to use this external MySQL server to stick with a low memory footprint compared 
to an additional linked MySQL docker container.

## Target environment
* Synology DSM 6 with installed MySQL server
* Let's encrypt certificats
* Tine accessible under specific subdomain - published via reverse proxy

## Installation
* Checkout sources..
* docker build --force-rm=true --no-cache=true --build-arg TINE20_SERVER_NAME=<host_with_domain> --build-arg TINE20_SERVER_ALIAS=<host> --build-arg TINE20_DB_USER=<user> --build-arg TINE20_DB_PASS=<pass> --build-arg TINE20_SETUP_USER=<user> --build-arg TINE20_SETUP_PASS=<pass> -t warwing/tine20-docker:2016.03.1-1 .
* docker run -i -t -d -p 8080:80 --name tine20-2016.03.1 warwing/tine20-docker:2016.03.1-1

## Synology configuration
* Certifcate: -> System settings -> Security -> Certificates
* Reverse Proxy: -> Application portal -> Reverse Proxy
