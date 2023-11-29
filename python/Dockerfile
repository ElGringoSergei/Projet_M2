FROM ubuntu:23.04
RUN apt-get update
RUN apt-get install -y python3
RUN apt-get install -y python3-flask
RUN apt-get install -y gunicorn
COPY app.py /home/ubuntu/app.py
WORKDIR /home/ubuntu
RUN nohup gunicorn -w 4 -b 10.5.0.4:5000 app:app --daemon