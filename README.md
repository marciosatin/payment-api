# Payment Api

Api de pagamento entre usuários comuns e lojistas utilizando o microframework lumen

## Documentação

A documentação da api pode ser acessada em [documentação](./api-doc.md)

Faça o download da collection do Postman em [collection](./payment_api_postman_collection.json)

## Como instalar

Na raíz do projeto rode o comando `docker-compose up --build -d` para criar os containers

1. Copie o arquivo `.env.exemple` para `.env`
2. Acesse o container `php` executando `docker exec -it php bash`
3. Crie as migrations executando `php artisan migrate`
4. Para popular tabelas com seeders execute `php artisan bd:seed`
5. Execute os **testes** com o comando `vendor/bin/phpunit`