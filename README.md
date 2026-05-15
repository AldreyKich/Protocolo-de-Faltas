# 📋 Protocolo Eletrônico de Justificativas de Faltas

<div align="center">

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

Sistema web moderno para gerenciamento digital de justificativas de faltas escolares com upload de atestados médicos e acompanhamento em tempo real.

[Demonstração](#-demonstração) • [Funcionalidades](#-funcionalidades) • [Instalação](#-instalação) • [Uso](#-uso) • [Tecnologias](#-tecnologias)

</div>

---

## 📖 Sobre o Projeto

O **Protocolo Eletrônico de Justificativas de Faltas** é uma solução completa para digitalizar e automatizar o processo de justificativa de faltas em instituições de ensino. O sistema elimina a necessidade de deslocamento presencial, permitindo que responsáveis enviem atestados médicos e acompanhem o status da análise de forma online e transparente.

### 🎯 Problema Resolvido

- ❌ Deslocamento presencial obrigatório para entregar documentos
- ❌ Perda ou extravio de documentos físicos
- ❌ Falta de transparência no processo de análise
- ❌ Dificuldade em consultar o histórico de protocolos
- ❌ Processo manual e demorado para a secretaria

### ✅ Solução Oferecida

- ✔️ Envio 100% digital de justificativas e atestados
- ✔️ Armazenamento seguro de documentos
- ✔️ Acompanhamento em tempo real via número de protocolo
- ✔️ Histórico completo e organizado
- ✔️ Interface administrativa para análise eficiente
- ✔️ Notificações de status (Enviado, Em Análise, Aprovado, Rejeitado)

---

## ✨ Funcionalidades

### 🌐 Portal Público

- **Página Inicial Moderna**: Design intuitivo com animações e gradientes
- **Envio de Justificativas**: Formulário completo com validações
  - Seleção de aluno (nome, turma, matrícula)
  - Data da falta (com validação de data futura)
  - Motivo detalhado da falta
  - Dados do responsável (nome, CPF, telefone, e-mail)
  - Upload de atestado (PDF, JPG, PNG - máx. 5MB)
- **Consulta de Protocolo**: Busca por número de protocolo
  - Visualização de status em tempo real
  - Detalhes completos da solicitação
  - Observações da secretaria
  - Visualização de anexos
- **Confirmação de Envio**: Página com número de protocolo gerado

### 🔐 Área Administrativa

- **Dashboard**: Visão geral com estatísticas
  - Total de protocolos por status
  - Protocolos recentes
  - Gráficos e indicadores
- **Gestão de Protocolos**:
  - Listagem com filtros (status, data, aluno)
  - Análise detalhada de cada protocolo
  - Alteração de status (Enviado → Em Análise → Aprovado/Rejeitado)
  - Adição de observações
  - Visualização e download de anexos
- **Gestão de Alunos**:
  - Cadastro, edição e exclusão
  - Campos: nome, matrícula, turma, data de nascimento
  - Ativação/desativação de alunos
- **Gestão de Usuários** (apenas Administrador):
  - Cadastro de usuários do sistema
  - Definição de perfis de acesso
  - Gerenciamento de permissões

### 👥 Perfis de Acesso

| Perfil | Permissões |
|--------|-----------|
| **ADMINISTRADOR** | Acesso total: protocolos, alunos, usuários e configurações |
| **SECRETARIA** | Visualizar e analisar protocolos + gerenciar alunos |
| **VISUALIZADOR** | Apenas visualizar protocolos (sem edição) |

---

## 🚀 Tecnologias

### Backend
- **PHP 7.4+**: Linguagem principal
- **MySQL 5.7+**: Banco de dados relacional
- **PDO**: Camada de abstração de banco de dados

### Frontend
- **HTML5 & CSS3**: Estrutura e estilização
- **Bootstrap 5.3**: Framework CSS responsivo
- **JavaScript (Vanilla)**: Interatividade
- **Bootstrap Icons**: Ícones modernos
- **Google Fonts**: Tipografia (Manrope, Space Grotesk)

### Design
- **Glassmorphism**: Efeito de vidro fosco
- **Gradientes Animados**: Fundo dinâmico
- **Animações CSS**: Transições suaves
- **Design Responsivo**: Mobile-first

---

## 📦 Instalação

### Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL, fileinfo

### Passo 1: Clone o Repositório

```bash
git clone https://github.com/seu-usuario/protocolo-faltas.git
cd protocolo-faltas
```

### Passo 2: Configure o Banco de Dados

1. Crie o banco de dados e as tabelas:
```bash
mysql -u root -p < SQL_Cria_BD_Tabelas.sql
```

2. Insira os dados iniciais (usuários e alunos de exemplo):
```bash
mysql -u root -p protocolo_faltas < config/seed.sql
```

### Passo 3: Configure a Aplicação

Edite o arquivo `config/config.php`:

```php
// URL base da aplicação
define('BASE_URL', '/protocolo_faltas/');

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'protocolo_faltas');
define('DB_USER', 'root');
define('DB_PASS', 'sua_senha');
```

### Passo 4: Permissões de Pasta

Garanta que a pasta `uploads/` tenha permissão de escrita:

```bash
chmod 755 uploads/
```

### Passo 5: Acesse o Sistema

- **Portal Público**: `http://localhost/protocolo_faltas/public/index.php`
- **Área Administrativa**: `http://localhost/protocolo_faltas/admin/login.php`

---

## 🔑 Credenciais Padrão

Após executar o `seed.sql`, use estas credenciais para primeiro acesso:

| Perfil | E-mail | Senha |
|--------|--------|-------|
| Administrador | admin@escola.com | password |
| Secretaria | secretaria@escola.com | password |

> ⚠️ **IMPORTANTE**: Altere as senhas imediatamente após o primeiro acesso!

---

## 📱 Uso

### Para Responsáveis (Portal Público)

1. **Acessar o Portal**: Entre em `public/index.php`
2. **Enviar Justificativa**: Clique em "Enviar Justificativa"
   - Selecione o aluno
   - Informe a data da falta
   - Descreva o motivo
   - Preencha seus dados como responsável
   - Anexe o atestado médico
   - Clique em "Enviar Protocolo"
3. **Anotar o Número**: Guarde o número de protocolo gerado
4. **Consultar Status**: Use "Consultar Protocolo" para acompanhar

### Para Secretaria/Admin (Área Administrativa)

1. **Login**: Acesse `admin/login.php`
2. **Dashboard**: Visualize estatísticas gerais
3. **Analisar Protocolos**:
   - Vá em "Protocolos"
   - Clique em "Ver Detalhes"
   - Visualize o atestado anexado
   - Altere o status conforme análise
   - Adicione observações se necessário
4. **Gerenciar Alunos**: Cadastre novos alunos em "Alunos"
5. **Gerenciar Usuários**: (Admin) Adicione usuários em "Usuários"

---

## 📂 Estrutura do Projeto

```
protocolo_faltas/
│
├── 📁 admin/                    # Área administrativa
│   ├── login.php               # Página de login
│   ├── logout.php              # Logout
│   ├── dashboard.php           # Dashboard principal
│   ├── protocolos.php          # Listagem de protocolos
│   ├── protocolo_detalhe.php   # Detalhes e análise
│   ├── alunos.php              # Gestão de alunos
│   ├── usuarios.php            # Gestão de usuários
│   └── 📁 includes/
│       ├── auth.php            # Verificação de autenticação
│       ├── header.php          # Cabeçalho admin
│       ├── sidebar.php         # Menu lateral
│       └── footer.php          # Rodapé admin
│
├── 📁 assets/                   # Recursos estáticos
│   ├── 📁 css/
│   │   └── style.css           # Estilos customizados
│   └── 📁 js/
│       └── main.js             # Scripts JavaScript
│
├── 📁 config/                   # Configurações
│   ├── config.php              # Configurações gerais
│   ├── db.php                  # Conexão com banco
│   └── seed.sql                # Dados iniciais
│
├── 📁 public/                   # Portal público
│   ├── index.php               # Página inicial
│   ├── novo_protocolo.php      # Formulário de envio
│   ├── confirmacao.php         # Confirmação de envio
│   └── consultar.php           # Consulta de protocolo
│
├── 📁 uploads/                  # Atestados enviados
│   ├── .htaccess               # Proteção de acesso direto
│   └── [arquivos]              # Arquivos anexados
│
├── SQL_Cria_BD_Tabelas.sql     # Script de criação do BD
└── README.md                    # Este arquivo
```

---

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais

#### `aluno`
- Armazena dados dos alunos
- Campos: id_aluno, nome, matricula, turma, data_nascimento, ativo

#### `responsavel`
- Dados dos responsáveis pelos alunos
- Campos: id_responsavel, nome, cpf, telefone, email

#### `protocolo`
- Registros de justificativas enviadas
- Campos: id_protocolo, numero_protocolo, data_falta, motivo, status, observacoes, data_envio, id_aluno, id_responsavel

#### `anexo`
- Arquivos anexados aos protocolos
- Campos: id_anexo, nome_arquivo, caminho_arquivo, tipo_arquivo, data_upload, id_protocolo

#### `usuario`
- Usuários do sistema administrativo
- Campos: id_usuario, nome, email, senha, perfil, ativo

---

## 🎨 Design e Interface

### Características Visuais

- **Paleta de Cores**:
  - Primária: Azul (#3b82f6)
  - Secundária: Verde (#10b981)
  - Fundo: Gradiente escuro (#0a0e1a → #1a1f35)

- **Tipografia**:
  - Títulos: Space Grotesk (moderna e geométrica)
  - Corpo: Manrope (legível e profissional)

- **Efeitos**:
  - Glassmorphism nos cards
  - Animações de entrada (fadeInUp)
  - Hover effects em botões e cards
  - Gradientes animados no fundo

### Responsividade

- ✅ Desktop (1920px+)
- ✅ Laptop (1366px - 1920px)
- ✅ Tablet (768px - 1366px)
- ✅ Mobile (320px - 768px)

---

## 🔒 Segurança

- **Autenticação**: Sistema de login com sessões PHP
- **Senhas**: Hash com `password_hash()` (bcrypt)
- **SQL Injection**: Prepared statements (PDO)
- **XSS**: `htmlspecialchars()` em todas as saídas
- **Upload**: Validação de tipo MIME e tamanho
- **Acesso Direto**: `.htaccess` protege pasta de uploads
- **Permissões**: Controle por perfil de usuário

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Para contribuir:

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

---

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 👨‍💻 Autor

Desenvolvido com ❤️ para facilitar a gestão escolar.

---

## 📞 Suporte

Encontrou um bug ou tem uma sugestão? Abra uma [issue](https://github.com/seu-usuario/protocolo-faltas/issues).

---

## 🎯 Roadmap

- [ ] Sistema de notificações por e-mail
- [ ] Exportação de relatórios em PDF
- [ ] Dashboard com gráficos interativos
- [ ] API REST para integração
- [ ] App mobile (React Native)
- [ ] Autenticação de dois fatores (2FA)
- [ ] Histórico de alterações de protocolo
- [ ] Assinatura digital de documentos

---

<div align="center">

**⭐ Se este projeto foi útil, considere dar uma estrela!**

</div>
