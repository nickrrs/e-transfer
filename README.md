# E-transfer
O projeto E-transfer é uma aplicação **simples e direta** que simula um ambiente de transação monetária entre indivíduos, podendo eles ser uma pessoa física (Usuário) ou Lojista (Seller).

## Documentação de Projeto
O projeto consiste em uma aplicação RESTFul em Laravel, rodando em uma ambiente de containers em Docker, e utiliza o banco de dados relacional para armazenamento de informações.

### Para configurar e executar o projeto é só seguir os simples passos:

Antes de iniciar, é importante que você crie um arquivo **.env** caso não o tenha. No arquivo **.env.example** há um modelo do arquivo que possa ser criado, com variáveis e valores ideais para o seu ambiente.

1 - Ao clonar o projeto, na raiz, execute o seguinte comando para monstar as imagens de container e executá-los em segundo plano: 
    
```docker
docker-compose up -d --build 
```

**Obs:** caso o script de "change owner" do Dockerfile não funcione corretamente, pode tentar executar o seguinte comando no terminal: 
```docker  
docker-compose exec app chmod -R 777 /var/www/storage 
```

Rodar esse comando, dá a permissão geral para qualquer usuário dentro da pasta storage, algo importante, já que nessa aplicação usamos escritas em Log. Entretanto, essa permissão em específico não é recomendável de se utilizar fora de ambientes de desenvolvimento/teste.

2 - Uma vez que o seu ambiente docker estiver buildado e rodando certinho, acessa o bash, utilizando o comando: 

> ```docker exec -it laravel_app bash``` (ou, no lugar do "laravel_app", o nome do container da sua aplicação)


3 - Uma vez dentro do console do bash, exexcute o seguinte comando para instalar as dependências do projeto: 

> ```composer install```

4 - Uma vez que as depedências do projeto estiverem instaladas, e .env configurado, execute o seguinte comando no terminal para rodar as migrations e popular o banco de dados: 

>```php artisan migrate```

Execute também o seguinte comando, se preciso:

> ```php artisan key:generate```

5 - Prontinho, seu ambiente de desenvolvimento está configurado, caso queira parar a execução dos containers, execute o seguinte comando:

```docker-compose stop```

### Como funciona a API ?
A aplicação atualmente conta apenas com uma endpoint para execução da funcionalidade de transação monetária entre usuários. Porém, não há um controle de cadastro desses usuários e nem um fluxo de autenticação para os mesmos, assim como para as rotas.

Para fins de testes da aplicação, se for do seu interesse, pode rodar o seguinte comando para executar todos os testes já montados para o sistema, lembre-se de executar o comando estando no console bash do docker: 

> ```php artisan test```

Mas caso queira testar o fluxo da funcionalidade de uma forma manual, primeiro popule o banco de dados, utilizando o seguinte comando:

>```php artisan db:seed```

Uma vez com o banco populado, pode pegar dados de exemplo para utilizar na endpoint.

#### Endpoint de Transação Monetária:
#### GET /api/transfer

Exemplo de corpo de requisição: 
```json
{
	"payer_wallet_id": "056de788-e999-4fa3-890d-d387dc389917",
	"payee_wallet_id": "cff8321d-efc4-4973-82b0-7a3aa743015b",
	"amount": 12
}
```

Exemplo de resposta da requisição bem sucedida: 
```json
{
	"message": "Your transaction was concluded, we have sent an email to the payee.",
	"data": {
		"amount": 12,
		"updated_at": "2024-02-21T16:26:01.000000Z",
		"created_at": "2024-02-21T16:26:01.000000Z"
	}
}
```

## Roadmap e Possíveis Melhorias


- [ ] Implementação de fluxo de cadastros de usuários e lojistas.

- [ ] Implementação de flag nas Wallets, para quando um usuário for inativo/excluído do sistema, manter a Wallet no banco, mas inativada. Importante para manter no banco o histórico de transações feitas utilizando aquela carteira.

- [ ] Implementar fluxo de Autenticação e Autorização, para segurança, gerenciamento de sessão e controle das rotas. Isso inclui também verificar e validar permissões de acesso para diferentes recursos da API.

- [ ] Injeção automatica de dependências das classes utilizando o AppServiceProvider,
bindando as classes utilizadas.

- [ ] Implementar jobs em filas, queueable para o Listeners, para e-mails que não foram enviados por instabilidade de serviços externos.

- [ ] Implementação através de um servidor remoto para realizar implementação de um Cron, ou talvez um serviço em Lambda, para envio dos Jobs/Listeners em fila, para e-mails que não foram enviados pela instabilidade do serviço.
