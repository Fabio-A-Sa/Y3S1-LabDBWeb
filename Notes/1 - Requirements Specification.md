# 1 - Requirements Specification

É a documentação do sistema a desenvolver. Nesta etapa há análise dos requisitos e das necessidades dos clientes, da tecnologia a implementar e das suas dependências. Para identificar todos os parâmetros, é necessário recorrer aos seguintes tópicos:

## 1.1 Project Presentation [A1]

Introduz o contexto e a motivação para o projecto. Descreve brevemente o sistema web a desenvolver, bem como os objectivos do projecto, uma listagem das principais funcionalidades que suportará e os grupos de acesso. Estes últimos são os utilizadores, administradores, que têm diferentes permissões dentro dos sistemas.

## 1.2 Actors [A2]

São baseados nos requisitos funcionais. Cada grupo de acesso ou identidade externa ao serviço tem diferentes permissões e por isso são representados por diferentes atores, podendo haver generalizações. Os atores são sempre externos ao serviço.

<p align="center">
    <img src="../Images/Actors.png" alt="Actors" title="Actors" />
</p>
<p align="center">Figura 1: Exemplo de atores do sistema</p>

Os atores devem ser sempre descritos de acordo com uma tabela:

| Identifier    | Description                                                                                                                                                          |
|---------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| User          | Generic user that has access to public information, such as collection's items                                                                                       |
| Visitor       | Unauthenticated user that can register itself (sign-up) or sign-in in the system                                                                                     |
| Reader        | Authenticated user that can consult information, insert works and items, manage list of interests, request the loan of items and comment the works of the collection |
| Owner         | Authenticated user that belongs to the same location as the creator of an item and can change the existing information or lend and record the return of items        |
| Administrator | Authenticated user that is responsible for the management of users and for some specific supervisory and moderation functions                                        |
| OAuth API     | External OAuth API that can be used to register or authenticate into the system.                                                                                     |

<p align="center">Tabela 1: Descrição dos atores do sistema</p>

## 1.3 User Stories [A2]

É uma definição de alto nível que contém somente as informações necessárias para que o desenvolvedor estimar o esforço que o requisito deverá trazer à implementação e a prioridade deste no projecto a desenvolver. É uma breve descrição, sob o ponto de vista de cada Actor, do que este poderá fazer no sistema. <br>
As user stories devem seguir o template:

```gherkin
As a [user], I want [function], so that [value]
```

| Identifier |        Name       | Priority |                                                                      Description                                                                     |
|:----------:|:-----------------:|:--------:|:----------------------------------------------------------------------------------------------------------------------------------------------------:|
| US01       | Sign-in           | high     | As a Visitor, I want to authenticate into the system, so that I can access privileged information                                                    |
| US02       | Sign-up           | high     | As a Visitor, I want to register myself into the system, so that I can authenticate myself into the system                                           |
| US03       | OAuth API Sign-up | low      | As a Visitor, I want to register a new account linked to my Google account, so that I do not need to create a whole new account to use the platform  |
| US04       | OAuth API Sign-in | low      | As a Visitor, I want to sign-in through my Google account, so that I can authenticate myself into the system                                         |

<p align="center">Tabela 2: Visitor User Stories</p>

## 1.4 Suplementary Requirements [A2]

Secção que contém as regras do sistema, os requisitos técnicnicos e outros requisitos não funcionais do projecto.

### 1.4.1 - Business Rules



### 1.4.2 - Technical Requirements



### 1.4.3 - Restrictions


