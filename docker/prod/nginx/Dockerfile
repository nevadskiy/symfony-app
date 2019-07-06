FROM node:11.13-alpine AS node-builder

WORKDIR /app

COPY ./package.json ./
COPY ./yarn.lock ./

RUN yarn install && npm rebuild node-sass

# Copy FROM source (from docker-context workdir) TO conainer workdir ('/app' in this case)
COPY ./ ./

RUN npm run build

######################

FROM nginx:1.15-alphine

COPY ./default.conf /etc/nginx/conf.d/default.conf

WORKDIR /app

# Copy all project public files inside built image
COPY ./../../../public ./public
COPY --from=node-builder /app/public/build ./public/build
