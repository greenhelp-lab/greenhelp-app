# GreenHelp

GreenHelp é uma aplicação acadêmica em PHP, HTML, CSS e JavaScript vanilla para simular a contratação e o acompanhamento de serviços de Green IT.

O projeto representa uma plataforma simples onde empresas podem visualizar serviços sustentáveis, adicionar serviços ao carrinho, simular uma contratação e acompanhar o andamento pelo painel do cliente. Também existe um painel administrativo para gerenciar usuários, empresas e serviços.

## Objetivo

O objetivo principal é demonstrar evolução incremental de uma aplicação web simples:

- organização básica de frontend e backend;
- uso de sessões e perfis de acesso;
- consultas e alterações com PDO;
- fluxo de marketplace e carrinho;
- painel administrativo;
- melhoria gradual de legibilidade, responsividade e manutenção.

Este não é um sistema de produção. É um projeto de portfólio acadêmico com foco em clareza, funcionalidade e evolução realista.

## Funcionalidades

- Login e logout de usuários.
- Perfis de cliente e administrador.
- Cadastro de empresa e usuário.
- Catálogo de serviços sustentáveis.
- Busca e filtro de serviços.
- Carrinho com seleção e finalização simulada.
- Serviços contratados com status.
- Perfil do usuário com edição de dados e foto.
- Perfil da empresa com edição de dados e logo.
- Painel administrativo com métricas simples.
- CRUD básico de clientes, administradores e serviços.

## Stack

- PHP
- MySQL
- HTML
- CSS
- JavaScript vanilla

Não há frameworks, TypeScript, Docker ou pipeline de build. A estrutura foi mantida simples para combinar com o escopo acadêmico do projeto.

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

## Configuração local

Crie um arquivo `.env` na raiz do projeto:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=greenhelp
PORT=3306
```

O projeto espera rodar dentro de uma pasta chamada `greenhelp-app`, por exemplo:

```text
http://localhost/greenhelp-app/public/index.html
```

## Status

A V1 está em fase de refatoração e estabilização. O foco atual é manter o projeto funcional, reduzir duplicação, remover código morto e deixar a base mais fácil de apresentar e evoluir.
