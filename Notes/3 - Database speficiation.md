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

### Mapeamento de generalizações

Principalmente quando as generalizações são completas e disjuntas, há três hipóteses para o mapeamento:

- **Superclasse**, onde a classe mais geral contém todos os atributos, alguns até podem ser nulos, e uma enumeração para diferenciar o tipo do objecto;
- **ER**, onde existe a caracterização de um objecto geral e cada uma das subdivisões. As classes filhas, que contém outros atributos, apontam para a classe que lhes deu origem com uma chave estrangeira;
- **Object Oriented**, onde apenas as classes filhas são caracterizadas e vários dos atributos são comuns às unidades;

## Indexes, triggers, transactions and database population [A6]

Indexes para suportar pesquisas e identificação de características mais especificas, triggers para questões de integridade e transações.

### Indexes

Usados para que as pesquisas/relações mais comuns no sistema sejam mais rápidas. Podem ser dos tipos: B-Tree, Hash, GiST, GIN. Quando pode existir uma ordenação é usada a B-Tree para uma pesquisa em tempo logarítmico, enquanto Hash é usado para quando não pode haver uma ordenação (apenas está implementado o operador igual), com tempo constante. Sem indexes, a pesquisa na base de dados é sempre sequencial e em muitos casos demora mais tempo.

```postgres
CREATE INDEX idx_numeric ON sample(x) USING BTREE(x);
CREATE INDEX idx_numeric ON sample(x) USING HASH(x);
```

No projecto, há um limite de 3 indexes a implementar. É errado propôr um index numa chave primária, é útil usá-las numa chave estrangeira que será muito usada no sistema para juntar tabelas ou em transformações de dados, como na função lower() que vemos a seguir:

```postgres
SELECT * FROM test1 WHERE lower(col1) = 'value';
CREATE INDEX test1_lower_col1_idx ON test1 (lower(col1));
```

Curiosamente o PostgreSQL cria automaticamente um unique index quando uma restrição "unique" é usada ou quando se declara uma chave primária numa tabela.

#### Clustering

Usado para bases de dados grandes, onde os dados estão no disco e quando existem indexes para agrupar os mesmos. É uma one-time operation, logo as alterações efetuadas na tabela não serão clustered a menos que rode periodicamente.

#### Cardinality

Relação com os valores duplicados em colunas. As chaves primárias tem grande cardinalidade, os nomes (primeiro, último) têm média cardinalidade, enquanto os atributos booleanos têm baixa (só permite dois estados).

#### (Full) Text Search

Usar o operador `LIKE` não suporta:

- Singulares e plurais ao mesmo tempo;
- Dados não ordenados, apenas um conjunto de dadso;
- Não permite pesquisa de várias palavras;
- Não tem suporte para indexes;

No sistema a desenvolver, convém ver projectar um documento onde a pesquisa em texto (parcial ou total) deve ser significativa. Na OnlyFEUP usaremos os posts e os comentários associados. 

##### tsvector

Um vector que faz store a lexemas distintos:

```postgres
SELECT to_tsvector('english', 'The quick brown fox jumps over the lazy dog')
'brown':3 'dog':9 'fox':4 'jump':5 'lazi':8 'quick':2
```

##### tsquery

Uma estrutura otimizada para procurar em tsvectors

```postgres
SELECT plainto_tsquery('portuguese','o velho barco');
'velh' & 'barc'
```

##### Weights

Dá para adicionar pesos às pesquisas. Por exemplo, um match num post será mais importante que um match num comentário. Os valores podem variar de 'A' a 'D' e declaram-se da seguinte forma:

```postgres
SELECT
setweight(to_tsvector('english', 'The quick brown fox jumps over the lazy dog'), 'A') ||
setweight(to_tsvector('english', 'An English language pangram. A sentence that contains
all of the letters of the alphabet.'), 'B')
```

##### Queries



### PostgreSQL

Usar o servidor `db.fe.up.pt`, disponível na rede da FEUP ou através da VPN. Usar PostgreSQL na versão 11.3.

### Docker

Útil para ter virtualizações de sistemas e imagens de programas, de modo a fazer a gestão de versões.

```bash
$ docker run --name some-postgres -e POSTGRES_PASSWORD=mysecret -p 5432:5432 -d postgres:11.3
$ docker exec -it some-postgres bash
```
