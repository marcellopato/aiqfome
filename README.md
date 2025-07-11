# API aiqfome Favoritos

API RESTful para gerenciamento de clientes e produtos favoritos, com autenticação, roles/permissões (Spatie), integração com API externa de produtos, documentação Swagger e testes automatizados.

---

## Clonando o projeto

```bash
git clone https://github.com/marcellopato/aiqfome.git
cd aiqfome
```

---

## Requisitos

- [Composer](https://getcomposer.org/)
- [Docker](https://www.docker.com/) (recomendado para ambiente local)
- [Laravel Sail](https://laravel.com/docs/10.x/sail) (opcional, mas recomendado)

---

## Subindo o ambiente com Docker/Sail

```bash
# Instale as dependências
composer install

# Copie o .env de exemplo e configure as variáveis se necessário
cp .env.example .env

# Gere a key do Laravel
php artisan key:generate

# Suba os containers
./vendor/bin/sail up -d

# Rode as migrations e seeders
./vendor/bin/sail artisan migrate --seed
```

---

## Credenciais de acesso

- As credenciais de usuários seedados podem ser encontradas no arquivo de seeder ou conforme configurado no banco de dados após rodar os seeders.
- Por padrão, consulte o seeder `DatabaseSeeder.php` para ver e-mails e senhas criados.

---

## Documentação Swagger

- Acesse a documentação interativa em:  
  ```
  http://localhost/api/documentation
  ```
- O arquivo JSON da documentação pode ser acessado em:  
  ```
  http://localhost/docs?api-docs.json
  ```

---

## Testes

Para rodar todos os testes automatizados:

```bash
./vendor/bin/sail artisan test
```
ou, se não estiver usando Sail:
```bash
php artisan test
```

---

## Endpoints principais

- Autenticação: `/api/login`
- CRUD de clientes: `/api/clients`
- Favoritos: `/api/clients/{client}/favorites`
- Listar produtos (proxy): `/api/products`

---

## Links úteis

- [Repositório no GitHub](https://github.com/marcellopato/aiqfome)
- [Documentação oficial do Laravel](https://laravel.com/docs/)
- [Documentação do Spatie Permissions](https://spatie.be/docs/laravel-permission/v5/introduction)
- [Fake Store API (produtos)](https://fakestoreapi.com/docs)

---

## Observações

- O projeto utiliza autenticação via Sanctum.
- Roles e permissões são gerenciadas pelo pacote Spatie.
- Testes automatizados cobrem autenticação, CRUD, favoritos, roles/permissões e proteção de rotas.

---

## Resultados dos testes automatizados

```sh
# php artisan test

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true  0.14s  

   PASS  Tests\Feature\Auth\AuthenticationTest
  ✓ users can authenticate using the login screen  7.43s  
  ✓ users can not authenticate with invalid password  0.51s  
  ✓ users can logout  0.20s  

   PASS  Tests\Feature\Auth\EmailVerificationTest
  ✓ email can be verified  0.24s  
  ✓ email is not verified with invalid hash  0.53s  

   PASS  Tests\Feature\Auth\PasswordResetTest
  ✓ reset password link can be requested  0.65s  
  ✓ password can be reset with valid token  0.26s  

   PASS  Tests\Feature\Auth\RegistrationTest
  ✓ new users can register  0.19s  

   PASS  Tests\Feature\AuthProtectionTest
  ✓ nao autenticado nao acessa rotas protegidas  0.17s  
  ✓ nao autenticado nao acessa favoritos  0.18s  
  ✓ token invalido nao acessa rotas protegidas  0.24s  
  ✓ rota publica login funciona sem autenticacao  0.42s  

   PASS  Tests\Feature\ClientTest
  ✓ criar cliente valido  0.22s  
  ✓ nao permite email duplicado  0.19s  
  ✓ listar clientes  0.14s  
  ✓ exibir cliente especifico  0.27s  
  ✓ atualizar cliente  0.18s  
  ✓ remover cliente  0.17s  
  ✓ validacao campos obrigatorios  0.17s  

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response  0.18s  

   PASS  Tests\Feature\FavoriteTest
  ✓ adicionar favorito valido  0.48s  
  ✓ nao permite favorito duplicado  0.80s  
  ✓ nao adiciona favorito invalido  0.14s  
  ✓ listar favoritos retorna apenas validos  1.39s  
  ✓ remover favorito  0.75s  

   PASS  Tests\Feature\RolePermissionTest
  ✓ manager pode crud qualquer cliente  0.27s  
  ✓ user so pode acessar editar remover proprio registro  0.18s  

  Tests:    28 passed (72 assertions)
  Duration: 18.36s
```
