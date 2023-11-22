FROM ubuntu:23.04
RUN apt-get update
RUN apt-get install -y python3
RUN apt-get install -y python3-flask
COPY app.py /home/ubuntu/app.py
WORKDIR /home/ubuntu