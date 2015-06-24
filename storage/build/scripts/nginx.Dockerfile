FROM nginx
MAINTAINER Shahriyar Imanov <shehi@imanov.me>

RUN cat /etc/hosts | sed s/localhost/localhost\ basis.audith.org/g | tee /etc/hosts
