# Web Applications

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

As páginas são construídas em runtime, quando o cliente faz um request. Manipuladas com PHP, JavaScript e AJAX. O código está dividido entre o servidor e o browser (cliente).

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

-

#### Desvantagens

