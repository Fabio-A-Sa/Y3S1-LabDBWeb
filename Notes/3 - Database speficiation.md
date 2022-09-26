# Database specification

## Conceptual data model [A4]

UML de diagrama de classes que contém os atributos, associações, multiplicidades e restrições da base de dados do sistema. Para isso:
1. Identificar identidades (pessoas, locais, eventos, conceitos, coisas);
2. Identificar relações entre as entidades;
3. Identificar os atributos;
4. Aplicação das convenções de nomes de acordo com o data modeling;

## Relational schema, validation and schema refinement [A5]

Deve incluir atributos, tipos/domínios, chaves primárias e estrangeiras, e restrições: UNIQUE, DEFAULT, NOT NULL, CHECK. As chaves primárias são sublinhadas e as chaves estrangeiras apontam para a tabela de referência. Em PostgreSQL, a convenção é ser tudo em letras minúsculas e nomes de atributos/classes com underscore. <br>
As dependências devem estar na BCNF, sem redundância e sem anomalias.

## Indexes, triggers, transactions and database population [A6]

Indexes para suportar pesquisas e identificação de características mais especificas, triggers para questões de integridade e transações.

### PostgreSQL

Usar o servidor `db.fe.up.pt`, disponível na rede da FEUP ou através da VPN. Usar PostgreSQL na versão 11.3.

### Docker

Útil para ter virtualizações de sistemas e imagens de programas, de modo a fazer a gestão de versões.

```bash
$ docker run --name some-postgres -e POSTGRES_PASSWORD=mysecret -p 5432:5432 -d postgres:11.3
$ docker exec -it some-postgres bash
```



