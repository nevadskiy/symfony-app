up:
	docker-compose up -d

down:
	docker-compose down

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

build-production:
	docker build --pull --file=manager/docker/production/nginx.docker --tag ${REGISTRY_ADDRESS}/manager-nginx:${IMAGE_TAG} manager
	docker build --pull --file=manager/docker/production/php-fpm.docker --tag ${REGISTRY_ADDRESS}/manager-php-fpm:${IMAGE_TAG} manager
	docker build --pull --file=manager/docker/production/php-cli.docker --tag ${REGISTRY_ADDRESS}/manager-php-cli:${IMAGE_TAG} manager
	docker build --pull --file=manager/docker/production/postgres.docker --tag ${REGISTRY_ADDRESS}/manager-postgres:${IMAGE_TAG} manager
	docker build --pull --file=manager/docker/production/redis.docker --tag ${REGISTRY_ADDRESS}/manager-redis:${IMAGE_TAG} manager

push-production:
	docker push ${REGISTRY_ADDRESS}/manager-nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/manager-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/manager-php-cli:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/manager-postgres:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/manager-redis:${IMAGE_TAG}

deploy-production:
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -o StrictHostKeyChecking=no -P ${PRODUCTION_PORT} docker-compose-production.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_APP_SECRET=${MANAGER_APP_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_DB_PASSWORD=${MANAGER_DB_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_REDIS_PASSWORD=${MANAGER_REDIS_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_OAUTH_FACEBOOK_SECRET=${MANAGER_OAUTH_FACEBOOK_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_HOST=${STORAGE_FTP_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_USERNAME=${STORAGE_FTP_USERNAME}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_PASSWORD=${STORAGE_FTP_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_BASE_URL=${STORAGE_BASE_URL}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose pull'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose up --build -d'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'until docker-compose exec -T manager-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose run --rm manager-php-cli php bin/console doctrine:migrations:migrate --no-interaction'

# App scripts
test:
	docker-compose exec php-cli php bin/phpunit

test-unit:
	docker-compose exec php-cli php bin/phpunit --testsuite=Unit

# Application commads
migration:
	docker-compose exec php-cli php bin/console make:migration

migrations-diff:
	docker-compose exec php-cli php bin/console doctrine:migrations:diff

migrate:
	docker-compose exec php-cli php bin/console doctrine:migration:migrate --no-interaction

fixtures-load:
	docker-compose exec php-cli php bin/console doctrine:fixtures:load --no-interaction

wait-postgres:
	until docker-compose exec postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

assets-install:
	docker-compose exec node yarn install
	docker-compose exec node yarn rebuild node-sass

assets-build:
	docker-compose exec node yarn dev
