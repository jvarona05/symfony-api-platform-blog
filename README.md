<p align="center"><img src="https://www.thinkbean.com/sites/default/files/styles/768x576/public/2018-08/api-platform.png?itok=iNv26RqY" width="280"></p>

# Blog Restful API - Symfony4

Robust APIs in Symfony 4 using API Platform

## Main Technologies

- Symfony 4
- API Platform
- JWT
- Restful API
- Migrations
- DataFixtures
- Authorization with symfony isGranted
- EventSubscriber
- SwiftMailer
- UnitTest -PHPUnit
- Feature Test - Behat

## Installation

### Clone the project

```
git clone https://github.com/jvarona05/symfony-api-platform-blog.git

cd symfony-api-platform-blog
```

### Create .env file

```
cp .env.dist .env
cp .env.test.dist .env.test
```

### Run Docker

```
git clone https://github.com/Laradock/laradock.git

cd laradock

cp env-example .env

docker-compose up -d nginx mysql workspace phpmyadmin
```

Note: The containers use the ports 80, 8080 and 3306. Please,
don't have any programs running on these ports in your machine.

### Configure the project

```
openssl genrsa -out config/jwt/private.pem -aes256 -passout pass:1234 4096

openssl rsa -pubout -in config/jwt/private.pem -passin pass:1234 -out config/jwt/public.pem 

docker exec -ti laradock_workspace_1 composer install

docker exec -ti laradock_workspace_1 php bin/console doctrine:migrations:migrate

docker exec -ti laradock_workspace_1 php bin/console doctrine:fixtures:load

docker exec -ti laradock_workspace_1 php bin/phpunit

docker exec -ti laradock_workspace_1 php vendor/bin/behat
```

### Open the documentation

```
http://localhost/api/docs
```