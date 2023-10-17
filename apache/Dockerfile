FROM httpd:2.4.57-alpine
RUN apk update; \
    apk upgrade;
COPY apache.conf /usr/local/apache2/conf/httpd.conf
COPY httpd-ssl.conf /usr/local/apache2/conf/extra/httpd-ssl.conf
#COPY ../certs/node.crt /usr/local/apache2/cert/certificate.crt
#COPY ../certs/node.key /usr/local/apache2/cert/private.key
#COPY ../certs/ca.crt /usr/local/apache2/cert/server-ca.crt

EXPOSE 80
EXPOSE 443
