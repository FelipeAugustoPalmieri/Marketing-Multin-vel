# Projeto TBest

Sistema de controle da rede de marketing multinível TBest.

## Desenvolvimento
Para configurar o seu ambiente de desenvolvimento:

* Crie um host `tbest.dev` apontando para `127.0.0.1` no `/etc/hosts`.
* Configure o seu servidor Apache ou Nginx para apontar o domínio `tbest.dev`
  para o arquivo `web/index`.
* Crie um novo banco de dados no PostgreSQL.
* Rode o comando `bin/setup` para instalar o ambiente e siga as instruções.

### Alimentando com dados de testes

Utilize o comando `php yii seed` para alimentar a base com informações de teste.

## Testes automatizados
Para configurar a suíte de testes

* Crie um host `tbest.test` apontando para `127.0.0.1` no `/etc/hosts`.
* Configure o seu servidor Apache ou Nginx para apontar o domínio `tbest.test`
  para o arquivo `web/index-test.php`.
* Crie um banco de dados no PostgreSQL, diferente daquele utilizado para
  desenvolvimento.
* Rode o comando `bin/setup` e assegure-se de que o ambiente de desenvolvimento
  está pronto.
* Rode o comando `bin/test` para executar os testes.

## Estrutura de diretórios

```
.env           configurações de conexões
assets/        definição de assets
commands/      comandos do terminal (controllers)
config/        configurações da aplicação
controllers/   controladores (web)
mail/          views para e-mails
models/        classes de modelos
runtime/       arquivos temporários do sistema
tests/         testes automatizados
vendor/        pacotes de terceiros
views/         arquivos de views (web)
web/           contém o script de entrada (index.php) e outros arquivos
               que poderão ser acessados pela web
```

## Requisitos

* PHP 7
* PostgreSQL 9
