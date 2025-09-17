# Carteira Digital - Desafio Técnico

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)
![Docker](https://img.shields.io/badge/Docker-20.10-2496ED?style=for-the-badge&logo=docker)
![Pest](https://img.shields.io/badge/Pest-2.34-F05340?style=for-the-badge&logo=pest)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)

## 🎯 Objetivo do Projeto

O objetivo deste projeto é a criação de uma API funcional para uma carteira financeira digital. A aplicação permite que usuários cadastrados realizem transferências de saldo entre si, façam depósitos e estornem transações, garantindo a consistência e a segurança dos dados em todas as operações.

Este projeto foi desenvolvido como uma solução para um desafio técnico, com foco em demonstrar boas práticas de desenvolvimento, arquitetura de software robusta e cobertura de testes completa.

---

## 🏗️ Arquitetura e Decisões de Design

A aplicação foi arquitetada seguindo os princípios da **Clean Architecture** e do **Domain-Driven Design (DDD)**. A escolha por esta abordagem visa criar um sistema com baixo acoplamento, alta coesão e excelente testabilidade, separando claramente as regras de negócio do framework e de detalhes de infraestrutura.

A estrutura é dividida em quatro camadas principais, seguindo a Regra da Dependência (dependências apontam sempre para dentro):

```
┌───────────────────────────────────────────────┐
│              Presentation (Laravel)           │  <-- Framework
│  (Controllers, Rotas, Blade Views, Requests)  │
│ ┌───────────────────────────────────────────┐ │
│ │             Infrastructure                │ │  <-- Detalhes de Implementação
│ │  (Eloquent Repositories, Serviços Externos) │
│ │ ┌───────────────────────────────────────┐ │ │
│ │ │             Application               │ │ │  <-- Casos de Uso
│ │ │  (Use Cases, DTOs, Eventos)           │ │ │
│ │ │ ┌───────────────────────────────────┐ │ │ │
│ │ │ │              Domain               │ │ │ │  <-- O Coração do Negócio
│ │ │ │ (Entities, Value Objects, Services) │ │ │ │
│ │ │ └───────────────────────────────────┘ │ │ │
│ │ └───────────────────────────────────────┘ │ │
│ └───────────────────────────────────────────┘ │
└───────────────────────────────────────────────┘
```

### 💡 Camadas

1.  **Domain (Domínio):** O núcleo da aplicação. Contém as entidades (`Wallet`, `Transaction`), Value Objects (`Money`) e os serviços de domínio com as regras de negócio puras. Esta camada é 100% agnóstica ao framework (não sabe que o Laravel existe) e não depende de nenhuma outra camada.

2.  **Application (Aplicação):** Orquestra a lógica de domínio para executar os Casos de Uso do sistema (ex: `TransferMoneyUseCase`, `ReverseTransactionUseCase`). Ela recebe DTOs (Data Transfer Objects), utiliza os repositórios para buscar e persistir entidades e coordena as ações.

3.  **Infrastructure (Infraestrutura):** É a implementação concreta dos detalhes técnicos. Aqui residem os **Eloquent Repositories** (que implementam as interfaces definidas no domínio), clientes de serviços externos, etc. É a "cola" entre o domínio e o framework.

4.  **Presentation (Apresentação):** A camada mais externa, responsável por interagir com o mundo exterior. No nosso caso, é o Laravel, com seus **Controllers**, rotas, validação de requests (Form Requests) e views. Os controllers são mantidos extremamente "magros" (skinny), com a única responsabilidade de traduzir a requisição HTTP para uma chamada de Caso de Uso.

### ✨ Benefícios desta Arquitetura

* **Testabilidade:** A lógica de negócio pode ser testada de forma isolada e rápida, sem a necessidade de inicializar o framework ou um banco de dados.
* **Desacoplamento:** O coração da aplicação não depende do Laravel. Em teoria, poderíamos trocar o framework sem alterar as regras de negócio.
* **Manutenibilidade:** A separação clara de responsabilidades (SOLID) torna o código mais fácil de entender, modificar e dar manutenção.

---

## 🚀 Instalação e Execução

O projeto é totalmente containerizado com **Docker** e gerenciado via **Laravel Sail**.

### Pré-requisitos

* Docker
* Docker Compose

### Passos para Instalação

1.  **Clonar o repositório:**
    ```bash
    git clone https://github.com/Marcone-Santos1/wallet
    cd wallet
    ```

2.  **Copiar o arquivo de ambiente:**
    ```bash
    cp .env.example .env
    ```

3.  **Instalar dependências do Composer (via Sail):**
    > ℹ️ Este comando irá baixar a imagem do Laravel Sail e instalar as dependências do PHP. Pode demorar alguns minutos na primeira vez.
    ```bash
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --ignore-platform-reqs
    ```

4.  **Iniciar os containers do Sail:**
    ```bash
    ./vendor/bin/sail up -d
    ```

5.  **Gerar a chave da aplicação:**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

6.  **Executar as migrations do banco de dados:**
    ```bash
    ./vendor/bin/sail artisan migrate:fresh
    ```

7.  **Acessar a aplicação:**
    A aplicação estará disponível em [http://localhost](http://localhost).

---

## 🧪 Executando os Testes

A aplicação possui uma suíte de testes completa, utilizando o framework **Pest**, que cobre tanto a lógica de negócio isoladamente quanto os fluxos completos da aplicação.

Para executar todos os testes, rode o comando:

```bash
./vendor/bin/sail artisan test
```

A suíte inclui:
* **Testes de Unidade:** Validam as regras de negócio nas Entidades (`Wallet`), Value Objects (`Money`) e a orquestração nos Casos de Uso.
* **Testes de Funcionalidade (Feature):** Simulam requisições HTTP para testar os fluxos completos de transferência, depósito e estorno, incluindo validações, respostas de sucesso/erro e a persistência correta dos dados no banco de dados de teste.
* **Testes de Segurança:** Garantem que as rotas estão protegidas e que as regras de autorização são respeitadas.

---
*Este README foi gerado em 17 de Setembro de 2025.*
