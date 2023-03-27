
# API to CMS being Microservice (Laravel 9.x + Docker)

## Rodar o Laravel

Clone Repositório
```sh
git clone https://github.com/rodolfoneto/fc-admin-videos-laravel-infra.git laravel9
```

```sh
cd laravel9/
```


Alterne de branch
```sh
git checkout main
```


Remova o versionamento
```sh
rm -rf .git/
```


Crie o Arquivo .env
```sh
cp .env.example .env
```


Atualize as variáveis de ambiente do arquivo .env
```dotini
APP_NAME="CMS Videos"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=videos_cms
DB_USERNAME=root
DB_PASSWORD=root

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```


Suba os containers do projeto
```sh
docker-compose up -d
```


Acesse o container app
```sh
docker-compose exec app bash
```


Instalar as dependências do projeto
```sh
composer install
```


Gerar a key do projeto Laravel
```sh
php artisan key:generate
```


Acesse o projeto
[http://localhost:8000](http://localhost:8000)
