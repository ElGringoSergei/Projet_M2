FROM mysql:8.1
ENV MYSQL_ROOT_PASSWORD mwartel
COPY my.cnf /etc/mysql/
COPY db.sql /docker-entrypoint-initdb.d/
RUN chown -R mysql:mysql /var/lib/mysql
