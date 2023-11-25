FROM mysql:8.2
ENV MYSQL_ROOT_PASSWORD mwartel
COPY my.cnf /etc/mysql/
COPY db.sql /docker-entrypoint-initdb.d/
