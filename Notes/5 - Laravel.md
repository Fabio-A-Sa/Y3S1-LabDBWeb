# Laravel

- [Model](#model)
    - [Setup](#setup)
    - [Relações entre Modelos](#relações)
- [Controller]()
    - Artisan
- [View]()
    - Blade
- [Policies]()
- [Inspire :)](#inspire)

## Model

### Setup

Todas as entidades da Base de Dados devem ter o correspondente Modelo em PHP dentro da pasta `app/Models`. Uma forma automática de criar os modelos é recorrer ao Artisan. O nome dos modelos é sempre singular:

```bash
$ php artisan make:model <MODEL_NAME>
```

Conteúdo de `app/Models/Post.php` após executar o comando com MODEL_NAME=Post:

```php
class Post extends Model
{
    public $timestamps  = false; // A
    protected $table = 'post';   // B

    protected $fillable = [
        'owner_id', 'group_id', 'content', 'date', 'is_public'  // C
    ];

    // Methods ...
}
```

Depois do ficheiro base criado recomenda-se:
- `A`: desativar os timestamps. É uma funcionalidade de Laravel não explorada no contexto de LBAW;
- `B`: garantir que o nome da tabela correspondente é bem selecionado. Há casos onde o nome da tabela e o nome do modelo não podem ser iguais, por exemplo groups-Group, por ser uma palavra reservada em SQL;
- `C`: garantir que todos os atributos da tabela são conhecidos. O ID pode ser ignorado;

### Relações

//TODO

## View

//TODO

## Controller

### Setup

Os controladores recebem os HTTP requests do servidor e são armazenados no diretório `app/Http/Controllers/`. Para cada Modelo criado existe um Controller. Para criá-los também podemos usar o Artisan:

```bash
$ php artisan make:controller <MODEL_NAME>Controller
```

Exemplo do conteúdo em `app/Http/Controllers/PostController.php`:

```php

```

### Routes


### Validation

// TODO

### Exemplos

// TODO

## Inspire

Para o projecto de LBAW é necessário muita inspiração. Mas nisso o Artisan também pode ajudar:

```php
$ php artisan inspire
```

---

@ Fábio Sá <br>
@ Novembro de 2022 <br>
@ Revisão em Julho de 2023