
# Panagora-api

A Panagora-api tem o objetivo de consumir a assembleia-api (https://panagora.doc.dev.pandora.com.br/api-assembleia/),
para obter os votantes de um evento e aparti deles capturar os dados de cada votante e gerar um documento em PDF individualmente.



## Instalação

1 - Crie um arquivo .env aparti do .env.example na pasta raiz do projeto:

```bash
cp .env.example .env
```

2 - Atualize as variáveis de ambiente no arquivo .env, ps: nesse contexto só é necessario preecher a PANAGORA_API_ENDPOINT, PANAGORA_API_TOKEN,
APP_PORT (Informe uma porta que esteja disponível no seu sistema) e o APP_NAME (Nome do container)

3 - Suba o container através do docker-compose:

```bash
    docker-compose up -d
```

4 - Acesse o terminal do container e entre na pasta do projeto:

```
docker exec -it panagora_api bash
cd home/project-folder
```

5 - Instale as dependências do projeto:

```
composer install
```

6 - Gere a chave da aplicação

```
php artisan key:g
```

7 - Configure as permissões do projeto:

```
chown www-data -R storage/
```

## Documentação da API

#### Retorna todos os votantes do evento

```http
  GET /api/index/{id_do_evento}/voters
```

##### Alternativa com os possíveis argumentos da rota
```http
  GET /api/index/{id_do_evento}/voters?page=3&per_page=10&getAllDocuments=true
```

| Parâmetro   | Tipo       | Descrição                           |
| :---------- | :--------- | :---------------------------------- |
| `page` | `int` | **Opcional**. A pagina da lista de votantes que deseja |
| `per_page` | `int` | **Opcional**. A quantidade de votantes que deve ser listado por pagina |
| `getAllDocuments` | `boolean` | **Opcional**. Essa flag faz a api criar todos os documentos dos votantes do evento |

#### Retorna os documentos dos votantes desejados

```http
  POST /api/docs/{id_do_evento}/pdf
```

| Parâmetro   | Tipo       | Descrição                                   |
| :---------- | :--------- | :------------------------------------------ |
| `vote_ids`  | `object` | **Obrigatório**. Objeto com os ids dos votantes desejados |

##### Exemplo de como montar a payload

{
	"votes_ids" : [
		5444527,
		5444852
	]
}

Adicione todos os ids dos votantes que desejar, caso não saiba os ids, envie uma request para a primeira rota sem o 
argumento 'getAllDocuments'

#### Os PDFs gerados serão armazenados dentro da pasta da raiz do projeto 'storage/app/public', como mostrado no return 'pdf_path'

## Deploy

Para fazer o deploy desse projeto acesse o servidor onde o projeto vai ser executado.

Dê git clone do projeto e siga os passos de instalação descrito acima.

Execute um git pull do projeto na branch main e o projeto estará disponível em produção.


## Autores

- [Pedro Lima](https://github.com/peeliima)

