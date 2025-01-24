# Desenvolvimento de Coding challenge PHP.
Pequena aplicação REST API com laravel 10.3 como Rest API;


### para iniciar o React:


1. na raiz do projeto, ``` $ cd tecnofit-app ```
2. ``` $ npm install ```
3. ``` $ npm start ```

### Teste o acesso do app em http://localhost:3000/


### Para iniciar o laravel com o docker:

#### Talvez precise inicializar o Docker em seu WLS : ``` $ sudo service docker start ```

1. na raiz do projeto, ``` $ cd tecnofit-api ```
2. ``` $ docker-compose up -d ```
3. ``` $ docker-compose exec tecnofit_api bash ```
4. ``` $ composer update ```
5. ``` $ php artisan key:generate ```
6. ``` $ php artisan jwt:secret ```
7. ``` $ php artisan optimize:clear ```


#### Para popular o banco de dados:

```
$ docker-compose exec tecnofit_api bash 
```

```
php artisan migrate:fresh --seed
```

### Teste o acesso da api em http://localhost/


#### Para realizar os testes:
1. ``` $ php artisan test ```
2. Ou diretamente com PHPUnity: ``` $ vendor/bin/phpunit ```


#### Para gerar e visualizar o Swagger:
1. ``` $ php artisan l5-swagger:generate ```
2. Acesse a documentação no navegador: ``` http://localhost/api/documentation ```
2. Faça o login e salve o token no botão: ``` Authorize ```


## Esta disponível o arquivo para api no postman na raiz do projeto!


#### O retorno do challenge esta em localhost/api/personal-records/ranking/list