<img width="208" height="90" alt="GreenHelp Official 2" src="https://github.com/user-attachments/assets/14192739-f574-454c-bc5c-7860e11ffc26" />

# GreenHelp 🌿

A GreenHelp é uma consultoria especializada em Green IT, dedicada a apoiar empresas na redução de impactos ambientais por meio de soluções tecnológicas sustentáveis, unindo eficiência digital e responsabilidade socioambiental. :contentReference[oaicite:0]{index=0}  

---

## 🔗 Links do Projeto

- 🎨 Protótipo Figma: [GreenHelp Design](https://www.figma.com/design/ikT85Y8M1yzyTWoV3t17R4/GreenHelp-Design)
- 💻 Repositório GitHub: [greenhelp-app](https://github.com/greenhelp-lab/greenhelp-app)

---

## 🌱 Sobre a Plataforma

Plataforma web para clientes e funcionários da consultoria GreenHelp, com foco em:

- Verificar, implementar e certificar práticas de TI Verde nas empresas.
- Tornar a adoção de Green IT simples, gamificada e orientada a resultados.
- Oferecer uma experiência agradável, com visão clara do progresso sustentável da empresa.

Público-alvo: empresas de pequeno, médio e grande porte que desejam investir em sustentabilidade e colher os benefícios desse investimento. :contentReference[oaicite:1]{index=1}  

---

## 🚀 Nossa Missão

Capacitar empresas a reduzirem seu impacto ambiental através de soluções em tecnologia sustentável, oferecendo serviços, métricas e acompanhamento contínuo.

## 🌍 Nossa Visão

Ser referência em Green IT no mercado, facilitando a jornada de transformação sustentável de empresas por meio de uma plataforma completa, intuitiva e baseada em dados.

## 💡 Nossos Valores

- Sustentabilidade e Responsabilidade
- Transparência e Ética
- Inovação em Tecnologia Verde
- Educação e Conscientização
- Colaboração com clientes e parceiros

---

## 🛠️ Funcionalidades Principais

### 1. Autenticação e Perfis

- Login e logout de clientes e administradores.
- Controle de acesso por papel (cliente, suporte, admin).
- Página inicial do cliente com informações da empresa e status dos serviços contratados. :contentReference[oaicite:2]{index=2}  

### 2. Catálogo de Serviços Sustentáveis

- Lista de serviços em formato de cards, com:
  - Imagem
  - Descrição
  - Área sustentável (energia, nuvem, lixo eletrônico, etc.)
  - Pontuação verde associada
- Filtros por área de atuação (ex.: Infraestrutura Eficiente, Energia Renovável, Computação em Nuvem, Descarte de Lixo Eletrônico, Políticas de TI Verde).
- Busca dinâmica em tempo real conforme o usuário digita. :contentReference[oaicite:3]{index=3}  

### 3. Carrinho e Aquisição de Serviços

- Adicionar e remover serviços do carrinho.
- Visualizar total de pontos sustentáveis acumulados.
- Confirmar aquisição dos serviços selecionados.

### 4. Acompanhamento de Serviços

- Tela inicial do cliente com os serviços contratados.
- Status de cada serviço: pendente, em andamento, concluído ou cancelado. :contentReference[oaicite:4]{index=4}  

### 5. Sistema de Pontuação Verde

- Cada serviço possui uma pontuação sustentável.
- A soma dos serviços gera a pontuação total da empresa.
- Pontuação é distribuída por áreas sustentáveis, facilitando a visualização da maturidade em cada eixo. :contentReference[oaicite:5]{index=5}  

### 6. Painel Administrativo

- Gestão de usuários (cadastro, edição, remoção de clientes).
- Gestão de empresas e seus dados.
- Visão geral de serviços, áreas e pontuação total.
- Estatísticas que apoiam a consultoria na tomada de decisão. :contentReference[oaicite:6]{index=6}  

---

## 🧱 Arquitetura e Tecnologias

- **Backend:** PHP  
- **Frontend:** HTML, CSS, JavaScript  
- **Banco de Dados:** MySQL  
- **Padrões de código:**
  - Separação entre lógica, visual e configuração.
  - Commits seguindo convenções semânticas e mensagens descritivas. :contentReference[oaicite:7]{index=7}  

### Requisitos Não Funcionais (Resumo)

- Interface responsiva (mobile e desktop).
- Senhas armazenadas de forma criptografada.
- Validação de sessão nas áreas restritas.
- Busca dinâmica com resposta rápida.
- Compatibilidade com navegadores modernos (Chrome, Edge, Firefox). :contentReference[oaicite:8]{index=8}  

---

## 🗃️ Modelo de Dados (Visão Geral)

Principais entidades do banco `greenhelp_db`: :contentReference[oaicite:9]{index=9}  

- **usuarios**  
  - Dados de acesso, papel (cliente/suporte/admin), avatar, status.
- **empresas**  
  - Empresa vinculada ao usuário, CNPJ, porte, setor, endereço e logo.
- **areas_sustentaveis**  
  - Áreas de atuação em sustentabilidade (infra, energia, nuvem, etc.).
- **servicos**  
  - Serviços oferecidos pela GreenHelp, com preço, pontos, área, descrição longa, itens incluídos, garantia, prazo e contato.
- **carrinho**  
  - Serviços adicionados pelo usuário antes da compra.
- **servicos_andamento**  
  - Serviços já adquiridos, com status e datas.
- **pontuacoes_sustentaveis**  
  - Pontuação por empresa e por área sustentável.

---

## 🔁 Fluxo do Usuário

### Cliente

1. Realiza login na plataforma.
2. Visualiza o resumo da empresa e serviços em andamento.
3. Explora o catálogo de serviços e usa filtros/busca.
4. Adiciona serviços ao carrinho e confirma a aquisição.
5. Acompanha o progresso de cada serviço e sua pontuação verde.

### Administrador

1. Acessa com perfil de admin.
2. Gerencia usuários e empresas.
3. Mantém o catálogo de serviços atualizado.
4. Acompanha métricas gerais de pontuação e serviços contratados.

---

## 🎨 Identidade Visual

- Marca inspirada em tecnologia sustentável, com elementos que remetem a folhas e cuidado ambiental.
- Layout focado em clareza, contraste e leitura confortável para o usuário.
- Interface pensada para transmitir confiança, seriedade e comprometimento com sustentabilidade.

(Adicione aqui paleta de cores, fontes oficiais e versões do logo, se quiser deixar o README ainda mais completo.)

---

## ⚙️ Como Rodar o Projeto Localmente

1. **Clonar o repositório**

   ```bash
   git clone https://github.com/greenhelp-lab/greenhelp-app.git
   cd greenhelp-app
