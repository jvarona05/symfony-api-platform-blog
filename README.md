<p align="center"><img src="https://www.thinkbean.com/sites/default/files/styles/768x576/public/2018-08/api-platform.png?itok=iNv26RqY" width="280"></p>

# Blog Restful API - Symfony4

Robust API in Symfony 4 using API Platform

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
- Unit Test -PHPUnit
- Feature Test - Behat

## Installation

### Clone the project

```
git clone https://github.com/jvarona05/symfony-api-platform-blog.git

cd symfony-api-platform-blog
```

### Configuration files

```
cp .env.dist .env
cp .env.test.dist .env.test
cp behat.yml.dist behat.yml
```

### Run Docker

```
git clone https://github.com/Laradock/laradock.git

cd laradock

cp env-example .env

docker-compose up -d nginx mysql workspace

cd ..
```

Note: The containers use the ports 80 and 3306. Please,
don't have any programs running on these ports in your machine.

### Configure JWT keys

```
openssl genrsa -out config/jwt/private.pem -aes256 -passout pass:1234 4096

openssl rsa -pubout -in config/jwt/private.pem -passin pass:1234 -out config/jwt/public.pem 
```

### Configure Symfony project

```

docker exec -ti laradock_workspace_1 composer install

docker exec -ti laradock_workspace_1 php bin/console doctrine:migrations:migrate

docker exec -ti laradock_workspace_1 php bin/console doctrine:fixtures:load

docker exec -ti laradock_workspace_1 php bin/console --env=test doctrine:database:create

```

### Run Tests

```
docker exec -ti laradock_workspace_1 php bin/phpunit

docker exec -ti laradock_workspace_1 php vendor/bin/behat
```

### Open the documentation

```
http://localhost/api/docs
```
<p align="center"><img src="https://i.imgur.com/YJ0PiZWg.png" ></p>
