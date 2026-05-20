# GreenHelp

GreenHelp é uma aplicação acadêmica em PHP, HTML, CSS e JavaScript vanilla para simular a contratação e o acompanhamento de serviços de Green IT.

O projeto representa uma plataforma simples onde empresas podem visualizar serviços sustentáveis, adicionar serviços ao carrinho, simular uma contratação e acompanhar o andamento pelo painel do cliente. Também existe um painel administrativo para gerenciar usuários, empresas e serviços.

|                                               Home Cliente                                                |                                                 Painel Admin                                                  |
| :-------------------------------------------------------------------------------------------------------: | :-----------------------------------------------------------------------------------------------------------: |
| ![greenhelp-home-client](https://github.com/user-attachments/assets/06b98c5f-1654-4c3d-96ad-861d6691073b) | ![greenhelp-admin-dashboard](https://github.com/user-attachments/assets/d0a3529b-1736-44cb-afb8-0f94331b68c5) |

## Objetivo

O objetivo principal é demonstrar evolução incremental de uma aplicação web simples:

- organização básica de frontend e backend;
- uso de sessões e perfis de acesso;
- consultas e alterações com PDO;
- fluxo de marketplace e carrinho;
- painel administrativo;
- melhoria gradual de legibilidade, responsividade e manutenção.

## Funcionalidades

- Login e logout de usuários.
- Perfis de cliente e administrador.
- Cadastro de empresa e usuário.
- Catálogo de serviços sustentáveis.
- Busca e filtro de serviços.
- Carrinho com seleção e finalização simulada.
- Serviços contratados com status.
- Pontuação sustentável por área após a contratação de serviços.
- Perfil do usuário com edição de dados e foto.
- Perfil da empresa com edição de dados e logo.
- Painel administrativo com métricas simples.
- CRUD básico de clientes, administradores e serviços.

|                                          Loja de Serviços                                          |                                          Detalhes do Serviço                                          |                                              Carrinho                                              |
| :------------------------------------------------------------------------------------------------: | :---------------------------------------------------------------------------------------------------: | :------------------------------------------------------------------------------------------------: |
| ![greenhelp-shop](https://github.com/user-attachments/assets/31287532-4e0b-4f4c-b864-6ef4e38fcc9e) | ![greenhelp-service](https://github.com/user-attachments/assets/7bbc5e5a-a1ee-4380-b792-6490cdb1a901) | ![greenhelp-cart](https://github.com/user-attachments/assets/2488f45a-2a41-458c-a7f9-02eb1d0cd846) |

## Stack

- PHP
- MySQL
- HTML
- CSS
- JavaScript vanilla

Não há frameworks, TypeScript, Docker ou pipeline de build (ainda!). A estrutura foi mantida simples para combinar com o escopo acadêmico do projeto.

## Database

O banco de dados é composto por tabelas para usuários, empresas, serviços, categorias, contratações e pontuações. Confira o diagrama:

<p align="center">
  <img width="545" height="533" alt="greenhelp-db-diagram" src="https://github.com/user-attachments/assets/f87eef21-e5a4-4053-aba4-77aa65715d0e" />
</p>


## Estrutura

```text
public/
  css/
  icons/
  imgs/
  js/
  uploads/
src/
  config/
  controllers/
  helpers/
  pages/
```

## Status

A V1 (esta aplicação) está funcional. Uma demo aberta para experimentar a plataforma será disponibilizada em breve.
