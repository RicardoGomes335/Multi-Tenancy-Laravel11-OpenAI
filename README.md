# Instalando o projeto

O projeto se utiliza de contêineres Docker, através do pacote *Laravel Sail* para facilitar a configuração do ambiente de desenvolvimento. Portanto, é necessário que já possua o Docker e o Docker Compose instalados na máquina.

Você é livre para rodar o projeto em ambiente local mas esse texto não tratará essa situação. 

Links para instalação e configuração de Docker:

- [Windows](https://docs.docker.com/docker-for-windows/install/)
- [Linux (Debian based)](https://docs.docker.com/engine/install/ubuntu/) 

### Passos para o rodar o projeto localmente:

- Faça um clone do projeto para sua máquina local
- Crie um arquivo `.env`, recomendamos usar `.env-example` como base
- Adicione ou altere as chaves conforme sua necessidade
- acesse a pasta do projeto via console (terminal/PowerShell/CMD)
- execute o comando:
```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
 ```
- Após finalizado processamento, execute o comando `./sail up -d`

O primeiro comando realiza a instalação dos pacotes via composer especificados no arquivo `composer.json` e uma vez que a instalação termina, a pasta *vendor* passa a ficar disponível no projeto. O comando seguinte levanta os contêineres baseado na descrição de serviços feita no arquivo `docker-compose.yml`.

Por padrão, não é necessária nenhuma configuração no arquivo *.env* do projeto. Caso seja necessária alguma edição na configuração padrão (relacionado a binding ports ou credenciais de banco de dados), basta editar o arquivo *.env*. 

# Trabalhando com Contêineres

Uma vez que o projeto está rodando em cima de contêineres Docker, é clara a situação de que a máquina local não possui nenhum dos requisitos necessários para se trabalhar no projeto, assim, comandos como `php artisan`, `composer` ou `npm` não funcionarão localmente. Para executar comandos dentro de um dos contêineres do projeto, um `php artisan route:list` por exemplo, é preciso utilizar o docker para tal, como por exemplo:

```bash
docker-compose exec \ #execução de um comando num contêiner existente
    -u sail \ # especifica o nome do usuário a ser utilizado dentro do contêiner
    projeto_laravel.test \ # especifica qual contêiner receberá o comando
    php artisan route:list # qual o comando a ser executado
```

 A execução, dessa forma, se torna muito verbosa e trabalhosa, podendo levar a potenciais erros de execução. Assim, o *Laravel Sail* oferece um script chamado `sail` e localizado em *vendor/bin/*. Esse script permite que tais comandos sejam executados através de aliases para que o fluxo de desenvolvimento seja mais natural e menos complexo. Assim, para se executar o mesmo comando `php artisan route:list` com o script `sail` ficaria:

 ```bash
 ./vendor/bin/sail artisan route:list

 #ou

 ./vendor/bin/sail art route:list
 ```

### Comandos disponíveis

Para conhecer os comandos disponíveis pelo script sail, execute `./vendor/bin/sail -h` para obter a listagem completa das opções com descrição.

# Próximos passos
Migrations são uma maneira de versionar as tabelas de sua base de dados. Para estruturar o seu banco de dados 
- Execute `./vendor/bin/sail art migrate` para montar sua adicionar as tabelas ao seu banco

- Execute `./vendor/bin/sail art db:seed` para popular o seu banco com dados fictícios

# Instalando a API OpenAI
1. Instale a biblioteca OpenAI para Laravel com Sail
- Execute:
- ./vendor/bin/sail composer require openai-php/laravel

2. Publique o arquivo de configuração com Sail
- Execute:
./vendor/bin/sail artisan vendor:publish --provider="OpenAI\Laravel\ServiceProvider"
Isso irá criar o arquivo config/openai.php

3. Adicione a API Key no .env
- Adicione no seu .env: OPENAI_API_KEY=coloque_sua_chave_aqui

4. Verifique o arquivo config/openai.php
- O config/openai.php deve estar assim (ou parecido):
  
return [
    'api_key' => env('OPENAI_API_KEY'),
];

5. Limpe o cache de configuração
- Depois de ajustar o .env, execute:
- ./vendor/bin/sail artisan config:clear
- ./vendor/bin/sail artisan cache:clear

# Para obter uma chave de API da OpenAI e usá-la no seu .env, siga os passos abaixo:
1. Crie uma conta na OpenAI
- Acesse: https://platform.openai.com/signup
- Faça login com Google, Microsoft, ou e-mail.

2. Acesse o Painel da OpenAI
- Vá para: https://platform.openai.com/account/api-keys

3. Gere uma nova API Key
- Clique em "Create new secret key".
- Dê um nome para a chave (exemplo: LaravelApp).
- Copie a chave exibida (ela será mostrada apenas uma vez).

4. Adicione a chave no seu arquivo .env
- No seu projeto Laravel, abra o arquivo .env e adicione:No seu projeto Laravel, abra o arquivo .env e adicione:
- OPENAI_API_KEY=sua_chave_aqui

5. Teste no Laravel Sail
- Execute:
- ./vendor/bin/sail artisan tinker
- E dentro do Tinker teste:
- OpenAI::models()->list(); (Se tudo estiver correto, ele listará os modelos da OpenAI.)

6. Importe o pacote OpenAI em: app/Livewire/Dashboard.php
- use OpenAI\Laravel\Facades\OpenAI;
- setar o modelo da API = gpt-3.5-turbo-instruct em: app/Livewire/Dashboard.php

        $this->config =  OpenAI::completions()->create([

            'model' => 'gpt-3.5-turbo-instruct', // Pode usar 'gpt-4o-mini' ou 'gpt-4o'
            'prompt' => "Considerando a lista de campos ($fields), gere uma configuração json do Vega-lite v5 (sem campo de dados e com descrição) que atenda o seguinte pedido {$this->question}. Resposta:",
            'max_tokens' => 1500
        ])->choices[0]->text;
