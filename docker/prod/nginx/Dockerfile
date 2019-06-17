FROM nginx:1.15-alphine

COPY ./default.conf /etc/nginx/conf.d/default.conf

WORKDIR /app

# Copy all project public files inside built image
COPY ./../../../public ./public
