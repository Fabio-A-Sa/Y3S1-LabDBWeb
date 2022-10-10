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

PostgreSQL permite fazer ranking de funções de pesquisa, de modo a permitir procurar:

- Termos mais comuns que aparecem no documento;
- Termos mais próximos entre si num mesmo documento;
- A importância dos termos dependendo do peso que se dá a cada parte do documento;

```postgres
SELECT title FROM posts
WHERE search @@ plainto_tsquery('english', 'jumping dog')
ORDER BY ts_rank(search, plainto_tsquery('english', 'jumping dog')) DESC
```

Por questões de otimização, a base de dados do documento deverá conter uma coluna onde os FTS serão manipulados, contendo os tsvectors para cada linha. A cada nova inserção ou update, o tsvector deverá ser recalculado segundo o trigger:

```postgres
CREATE FUNCTION post_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.search = to_tsvector('english', NEW.title);
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF NEW.title <> OLD.title THEN
            NEW.search = to_tsvector('english', NEW.title);
        END IF;
    END IF;
    RETURN NEW;
END
$$ LANGUAGE 'plpgsql';
```

Por questões de otimização, também dá para criar indexes com as colunas pré-calculadas dos tsvectors. Designando a coluna como search, temos que:

```postgres
CREATE INDEX search_idx ON posts USING GIN (search);
CREATE INDEX search_idx ON posts USING GIST (search);
```

A função GIN é usada para dados que mudam pouco, enquanto que GIST é para dados que são frequentemente updated. Em cada situação são mais rápidos.

#### User-Defined Functions

Funções pré-definidas dentro da base de dados que extendem a funcionalidade da mesma.

1. Vantagens:
    - Reduz o número de ligações entre a aplicação e o servidor da base de dados, já que os cálculos são efetuados dentro da BD;
    - Aumenta a performence, já que as funções são pré-compiladas;
    - Podem ser reusadas em várias aplicações

2. Desvantagens:
    - O desenvolvimento de software é mais lento, já que poucos têm competências a esse nível;
    - Mais difícil de manipular versões e mais difícil é fazer debug;
    - Menos portável, porque cada database management system tem a sua forma de fazer user-defined functions;

Um exemplo prático de funções:

```postgres
CREATE OR REPLACE FUNCTION totalRecords ()
RETURNS INTEGER AS $total$
DECLARE
 total INTEGER;
BEGIN
 SELECT COUNT(*) INTO total FROM company;
 RETURN total;
END;
$total$ LANGUAGE plpgsql;
SELECT totalRecords();
```

#### Triggers

Para manter a integridade da base de dados e fazer verificações/updates enquanto ocorre uma inserção, delete ou update numa coluna ou tabela. Exemplo usando a função anterior:

```postgres
CREATE TRIGGER loan_item
    BEFORE INSERT OR UPDATE ON loan
    FOR EACH ROW
    EXECUTE PROCEDURE loan_item();
```

#### Transactions

Para o projecto, é necessário fazer pelo menos uma transação relacionada à eliminação dos dados, que devem ser mantidos mas sem os dados pessoais do utilizador. Deve ser indicada uma justificação, o nível de isolamento e o código SQL para gerar a transação. Para as generalizações também é necessário haver transações, permite inserir dois tuplos numa operação atómica. Exemplo:

> Notification (id, date)
> NewFriend (id -> Notification, ...)
> NewLike (id -> Notification, ...)

As transações são necessárias para:

- garantir uma leitura e escrita paralela (em operações nos bancos, por exemplo);
- prevenir que uma falha no sistema fique registada na base de dados (falta de inserts, deletes);
- garantir todas as propriedades ACID;

###### ACID

1. Atomicity - trata comandos como uma operação única;
2. Consistency - manipulação de bases de dados de forma consistente, excepto erros relacionados com a lógica da aplicação;
3. Isolation - garante que várias operações em simultâneo são tratadas como se fossem isoladas ou singulares;
4. Durability - a operação é preservada na base de dados;

### Problemas de concorrência

#### 1 - Dirty Reads

Quando uma transação lê dados que outra transação está a manipular e ainda não gravou (COMMIT;). São feitas leituras de dados não gravados.

#### 2 - Non-repeatable reads

Quando faz duas ou mais leituras do mesmo dado, pois no meio das leituras houve uma atualização do dado por parte de outra transação concorrente.

#### 3 - Phantom reads

Quando há novas linhas da tabela em manipulação. No caso houve novos inserts por uma transação concorrente.

#### 4 - Serialization anomaly

O resultado obtido de manipulação concorrente de transações depende da ordem de implementação.

### Transaction Isolation

Os vários níveis de isolamento permitem lidar com problemas de concorrência e não dar lock à base de dados desnecessariamente, o que podia provocar uma menor capacidade de operações concorrentes.

#### 1 - Read uncommitted

Permite a leitura de dados ainda não guardados, ou seja, permite **dirty reads**

#### 2 - Read committed

Por omissão, é este nível de isolamento que está no PostgreSQL. Garante a leitura de dados já guardados.

#### 3 - Repeatable read

Garante que se fizer duas ou mais leituras, os resultados serão os mesmos. Só permite leituras de dados entre transações que não alteram os dados.

#### 4 - Serializable

Nunca tem acesso a dados não guardados ou modificados após a transação começar a ocorrer. 

### PostgreSQL

Usar o servidor `db.fe.up.pt`, disponível na rede da FEUP ou através da VPN. Usar PostgreSQL na versão 11.3.

### Docker

Útil para ter virtualizações de sistemas e imagens de programas, de modo a fazer a gestão de versões.

```bash
$ docker run --name some-postgres -e POSTGRES_PASSWORD=mysecret -p 5432:5432 -d postgres:11.3
$ docker exec -it some-postgres bash
```