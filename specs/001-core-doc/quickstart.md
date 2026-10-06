# Quickstart — banco PostgreSQL/PostGIS de teste

**Tarefas**: T015–T019

**Data**: 5 de outubro de 2026

## Objetivo

Os testes automatizados usam exclusivamente o banco `ovpdh_test`. O banco de desenvolvimento, o banco do VPS e o legado nunca podem ser usados como alvo do PHPUnit.

## Pré-requisitos

- PHP 8.2+ com `pgsql` e `pdo_pgsql`;
- PostgreSQL 16+;
- PostGIS disponível no servidor;
- usuário PostgreSQL local com permissão sobre `ovpdh_test`;
- dependências instaladas por Composer.

## Configuração local

Inclua no `.env` local, que é ignorado pelo Git:

```ini
database.tests.hostname = 127.0.0.1
database.tests.database = ovpdh_test
database.tests.username = SEU_USUARIO_POSTGRES
database.tests.password = SUA_SENHA_POSTGRES
database.tests.DBDriver = Postgre
database.tests.DBPrefix =
database.tests.port = 5432
database.tests.charset = UTF8
database.tests.schema = public
```

As credenciais de teste não são copiadas para `phpunit.dist.xml`, commits, relatórios ou logs.

## Criação do banco

Execute com um usuário PostgreSQL autorizado:

```sql
CREATE DATABASE ovpdh_test ENCODING 'UTF8' TEMPLATE template0;
```

Conectado ao novo banco:

```sql
CREATE EXTENSION IF NOT EXISTS postgis;
```

O nome terminado em `_test` é uma barreira de segurança verificada automaticamente. Não restaure dados pessoais ou o backup histórico nesse banco.

## Execução

No Windows, a partir da raiz do projeto:

```powershell
C:\xampp\php\php.exe vendor\bin\phpunit tests\database\PostgresHealthTest.php --no-coverage
```

Depois do teste de saúde:

```powershell
C:\xampp\php\php.exe vendor\bin\phpunit --no-coverage
```

`PostgresHealthTest` confirma o driver PostgreSQL, o sufixo seguro do banco, o schema `public`, a extensão PostGIS e uma operação espacial com SRID 4326.

## Helpers

- `tests/_support/PostgresTestCase.php`: inicia uma transação antes de cada teste e sempre executa rollback no encerramento;
- `tests/_support/AuthTestHelper.php`: cria um usuário único, atribui o grupo solicitado e autentica a sessão Shield.

Exemplo:

```php
final class ExampleTest extends \Tests\Support\PostgresTestCase
{
    use \Tests\Support\AuthTestHelper;

    protected $migrate = true;

    public function testVolunteerCanOpenTheWorkspace(): void
    {
        $user = $this->actingAsTestUser('voluntario');

        $this->assertTrue($user->inGroup('voluntario'));
    }
}
```

## Proteções operacionais

1. O grupo `tests` usa PostgreSQL mesmo quando o ambiente de desenvolvimento ainda usa outro driver.
2. O teste falha se o banco não terminar em `_test`.
3. Não se executa teste com as credenciais ou o nome do banco de produção.
4. Testes de escrita usam transação e rollback; suites de migrations usam banco descartável e restauração controlada.
5. Segredos permanecem somente no `.env` de cada ambiente.

## Solução de problemas

- **password authentication failed**: confira `database.tests.username` e `database.tests.password` no `.env`.
- **database does not exist**: crie `ovpdh_test` com o comando acima.
- **PostGIS não habilitado**: conecte a `ovpdh_test` como proprietário e execute `CREATE EXTENSION postgis`.
- **driver ausente**: habilite `pgsql` e `pdo_pgsql` no PHP usado pelo terminal.
- **falha ao migrar**: confirme que o teste de saúde passa antes de investigar migrations.
