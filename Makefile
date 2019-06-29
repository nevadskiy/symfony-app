up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans

restart: down up

build:
	docker-compose up -d --build

clear:
	docker-compose down --volumes --remove-orphans

s:
	docker-compose ps

rebuild: down build

pull:
	docker-compose pull

prod-build:
	docker build --pull --file=docker/prod/nginx/Dockerfile --tag ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG} manager
	docker build --pull --file=docker/prod/php-fpm/Dockerfile --tag ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG} manager
	docker build --pull --file=docker/prod/php-cli/Dockerfile --tag ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG} manager

prod-push:
	docker push ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG}

prod-deploy:
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -o StrictHostKeyChecking=no -P ${PRODUCTION_PORT} docker-compose.prod.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRIY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose pull'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose --build -d'

# App scripts
test:
	docker-compose exec php-cli php bin/phpunit

# Application commads
migration:
	docker-compose exec php-cli php bin/console make:migration

migrate:
	docker-compose exec php-cli php bin/console doctrine:migration:migrate