FROM nginx:alpine

COPY ./development/nginx/cert.cnf ./

RUN apk add --no-cache openssl

RUN mkdir -p /etc/nginx/ssl

RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/nginx/ssl/key.pem \
    -out /etc/nginx/ssl/cert.pem \
    -config ./cert.cnf

EXPOSE 443

CMD ["nginx", "-g", "daemon off;"]
