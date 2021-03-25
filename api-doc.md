
# Documentação

O header `Content-Type` da requisição deve ser `application/json`

### Usuários

**Cadastrar usuário**

POST `/users`

| Parâmetros      |Tipo                          |Obrigatório                    |Informação|
|:----------------|:------------------------------|:-----------------------------|:---|
|full_name        | string                        |Sim            |Nome completo do usuário|
|email            | string unique                 |Sim            |E-mail do usuário|
|balance          | decimal(8,2)                  |Sim            |Valor do saldo|
|cpf              | string max 11 unique          |Sim            |Cpf do usuário|
|id_type          | int          |Sim    |Tipo do usuário 1 - Comum 2 - Lojista|

#### Request body
```json
{
    "full_name": "User teste comum",
    "email": "comum@comum.com.br",
    "password": "teste2",
    "balance": 100,
    "cpf": "13158097028",
    "id_type": 1
}
```
#### Response 200
```json
{
    "id": 1,
    "full_name": "User teste comum",
    "email": "comum@comum.com.br",
    "password": "teste2",
    "balance": 100,
    "cpf": "13158097028",
    "id_type": 1
}
```
#### Response 400
```json
{
	"email": ["The email has already been taken."],
	"cpf": ["The cpf has already been taken."]
}
```

**Listar usuário**

Para realizar o filtro pelo id basta informá-lo como parâmetro `/users/1` por exemplo.
Para filtrar por nome basta informar `/users/?q=nome`

GET `/users`

#### Response 200
```json
[
  {
	"id": 1,
	"full_name": "User teste comum",
	"username": null,
	"cpf": "13158097028",
	"email": "comum@comum.com.br",
	"balance": "100.00",
	"id_type": 1
  },
  {
	"id": 2,
	"full_name": "User teste lojista",
	"username": null,
	"cpf": "13158097030",
	"email": "lojista@lojista.com.br",
	"balance": "100.00",
	"id_type": 2
  }
]
```
#### Response 404
```json
{
    "code": 404,
    "message": "User not found"
}
```

**Cadastrar lojista**

POST `/users/sellers`

| Parâmetros      |Tipo                          |Obrigatório                    |Informação|
|:----------------|:------------------------------|:-----------------------------|:---|
|cnpj             | string max 14 unique          |Sim            |Cnpj do lojista|
|id_user          | int                           |Sim            |Código do usuário lojista|

#### Request body
```json
{
    "cnpj": "44605670000107",
    "id_user": 1
}
```
#### Response 200
```json
{
    "cnpj": "44605670000107",
    "id_user": 1,
    "id": 1
}
```
#### Response 400
```json
{
    "code": 400,
    "message": "User must be a user type lojista"
}
{
    "cnpj": [
        "The cnpj has already been taken.",
        "The cnpj must be at least 14 characters."
    ],
    "id_user": [
        "The selected id user is invalid."
    ]
}
```

### Transferências

**Realizar transferência**

POST `/transactions`

| Parâmetros      |Tipo                          |Obrigatório                    |Informação|
|:----------------|:------------------------------|:-----------------------------|:---|
|payer_id         | int                           |Sim      |Código do pagador|
|payee_id         | int                           |Sim      |Código do beneficiário |
|value            | decimal(8,2)                  |Sim      |Valor da transferência|

#### Request body
```json
{
    "payer_id": 1,
    "payee_id": 2,
    "value": 100
}
```
#### Response 200
```json
{
    "payer_id": 1,
    "payee_id": 2,
    "value": 100,
    "created_at": "2021-03-25T02:43:09.000000Z",
    "id": 1
}
```
#### Response 400
```json
{
    "code": 400,
    "message": "Insufficient funds for payer"
}
{
    "code": 400,
    "message": "Invalid transaction for payer and payee"
}
{
    "code": 400,
    "message": "Transaction not allowed for that user"
}
{
    "value": [
        "The value must be greater than 0."
    ]
}
```

