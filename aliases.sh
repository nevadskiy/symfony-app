alias own='sudo chown -R $(id -u):$(id -g)'
alias from='docker-compose exec'
alias console='from php-cli php bin/console'
alias tf='from php-cli php bin/phpunit --filter'
