# Web Applications and Frameworks

- [Internet vs. Web](#internet-vs-web)
- [Web Applications](#web-applications)
- [Web Applications Architectures](#web-applications-architectures)
    - [Static Web Pages](#static-web-pages)
    - [Dynamic Web Pages](#dynamic-web-pages)
    - [Multi-Page Web Applications](#multi-page-web-applications)
    - [Single Page Web Applications](#single-page-web-applications)
- [Server-side Web Development](#server-side-web-development)
- [Web Frameworks](#web-frameworks)
- [Web application specification](#web-application-specification-a7)
- [OpenAPI](#openapi)

## Internet vs. Web

Enquanto que a Internet são redes conectadas entre si, para ligar dispositivos, a Web (world wide web) é um sistema de distribuição da informação que usa a Internet. <br>
A World Wide Web foi inventada no CERN em 1989, tem uma arquitetura client-server. Uma web page é uma composição de vários objectos e ficheiros (php, html, css, js, images) e três tecnologias principais (URL, HTTP e HTML). O primeiro browser estável foi o Mosaic.

## Web Applications

É um sistema de software que é baseado nos standards e tecnologias da internet e acedido através de um browser. Exemplos: Gmail, Google search, Facebook, SIGARRA.

### Vantagens

- Tem uma independência de plataformas;
- Fácil de dar update ou fix em bugs, pois há sempre uma versão estável pública;
- O acesso é efetuado em qualquer sítio/dispositivo se existir internet;
- Reduz a pirataria;
- Não há necessidade de instalação;
- Há medições de user-interaction em tempo real;

### Desvantagens

- Depende da conecção com a rede/internet;
- User Interfaces menos sofisticadas;
- Acesso a hardware limitado;
- Reduzida integração ao sistema operativo (exemplo: drag&drop);
- É mais difícil de corrigir falhas, pois é um sistema distribuido;
- É necessário maior segurança, há mais riscos;
- Tem muitos custos de bases de dados, servidores;

## Web Applications Architectures

Normalmente a arquitetura está repartida em 3 blocos principais: Presentation (HTML, CSS, Javascript), Business Logic (Javascript, PHP) e Data Management (JavaScript, PHP, PostgreSQL). Cada bloco pode estar do lado do servidor ou do lado do cliente.

### Static Web Pages

As páginas são construídas no momento do design, enviadas diretamente a partir do servidor. Não há código em execução.

### Dynamic Web Pages

As páginas são construídas em runtime, quando o cliente faz um request. Manipuladas com PHP, JavaScript e AJAX. O código está dividido entre o servidor e o browser (cliente). Podem ser:

- `Server-side rendering` (SSR), necessita de várias chamadas ao servidor para interação com o utilizador;
- `Client-side rendering` (CSR), utilização de javascript para todas as tarefas, não é propriamente uma hypertext application;

### Multi-Page Web Applications

Em cada request ao servidor há mudança de páginas (reload). 

#### Vantagens

- Permite um estilo REST;
- É independente do cliente e do browser;
- Boa parte da lógica é mantida no servidor;

#### Desvantagens

- Menor performence e menor responsividade;
- O código fica fragmentado;
- Não há forma de dar updates a uma página aberta;

### Single Page Web Applications

Apenas há uma página, que faz AJAX requests do servidor. O load inicial pode ser mais demorado devido à quantidade de código javascript necessária na parte do cliente.

#### Vantagens

- Melhor experiência por parte do utilizador;
- Reduz o consumo de largura de banda;
- A interface e código do lado do cliente pode ser reutilizado;

#### Desvantagens

- Necessita obrigatoriamente de JavaScript;
- Aumenta a dependência do browser e não permite browser history;
- Não permite REST;

## Server-side Web Development

Em LBAW as web resources podem ser do tipo View, resultantes de requests ao servidor retornando HTML (GET /view.php?id=2), e Action, com a utilização do servidor para computar algumas tarefas (POST /edit.php ou Ajax com Javascript).

# Web Frameworks

Por exemplo ReactJS, Vue.js, Ruby on Rails, Django e Laravel.

### Vantagens

- Velocidade de implementação;
- Testar soluções mais facilmente;
- Existe documentação;
- Manutenção e atualização da estrutura;

### Desvantagens

- Reduzida independência, dependência de entidades externas;
- Menos performence;

# Web application specification [A7]

A ideia da especificação é servir de base para o desenvolvimento do mochup. Cada página terá as suas UI (user interfaces) e para a montar é necessário recorrer a APIs. 

## OpenAPI

Uma forma simples de apresentar a API de um servidor:

- UIs;
- Redirects;
- Ações como POST, GET, DELETE, PUT;
- Retornos JSON ou HTML;

```api
openapi: 3.0.0
info:
 title: Sample API
 description: Optional multiline or single-line description in [CommonMark](http://commonmark.org/help/) or HTML.
 version: 0.1.9
servers:
 - url: http://api.example.com/v1
 description: Optional server description, e.g. Main (production) server
paths:
/users:
 get:
 summary: Returns a list of users.
 description: Optional extended description in CommonMark or HTML.
 responses:
'200': # status code
 description: A JSON array of user names
 content:
application/json:
 schema:
 type: array
 items:
 type: string
```

No documento `.yaml` deve existir uma parte dedicada aos metadados, à documentação externa (no nosso caso, um link de retorno à wiki), aos servidores ligados à API, a definição de tags para melhor representar os dados redundantes, paths da API. <br>
Exemplo da página de login:

```yaml
/api/context:

    get:
      operationId: R803
      summary: 'R803 : Notification context'
      description: 'Get notification context. Access: USR, ADM'
      tags:
        - 'M08: API'

      parameters:
        - in: query
          name: id
          description: 'Notification id'
          schema:
            type: integer
          required: true

      responses:
        '200':
          description: 'Success. Returns some HTML text containing notification context (post, comment, subcomment) information'
        '403':
          description: 'Forbiden action. You need to be logged in first'

/admin/user/unblock:

    post:
      operationId: R503
      summary: 'R503: Unblocking a user from logging in action'
      description: 'Unblock a user. Access: ADM'
      tags:
        - 'M05: Administration'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                user_id:
                  type: integer
              required:
                - user_id

      responses:
        '200':
          description: 'Ok. User unblocked.'
        '401':
          description: 'Unauthorized. You cannot unblock this user.'
```

Para trabalhar com estes ficheiros existe uma opção online: o [Swagger Editor](https://editor.swagger.io). No gitlab a visualização do documento é mais simpática.

O exemplo completo da OnlyFEUP está disponível [aqui](../Project/docs/a9_openapi.yaml). Note-se que além do tópico A7 também contém código referente ao A9, que nada mais é que o update final da documentação.

---

@ Fábio Sá <br>
@ Novembro de 2022 <br>
@ Revisão em Outubro de 2023