# Sistema de Gestão de Alunos - API

## 📌 Visão Geral

Esta API fornece um sistema completo para gestão de alunos, com autenticação JWT e controle de acesso baseado em roles (gestor/funcionário). A aplicação foi desenvolvida em Laravel 10 com PHP 8.2+.

## 🔧 Pré-requisitos

- **PHP** 8.2+
- **Composer** 2.5+
- **MySQL** 8.0+ ou **PostgreSQL** 13+
- **Node.js** 18+ (opcional para frontend)
- **Git**

## 🚀 Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/JonnasWillian/systemClass
cd seu-repositorio
```

### 2. Instalar dependências

```bash
composer install
npm install
```

### 3. Configurar ambiente

- Copie o arquivo `.env.example` para `.env`
- Configure as variáveis de banco de dados

```bash
cp .env.example .env
```

### 4. Gerar chave da aplicação

```bash
php artisan key:generate
```

### 5. Configurar JWT

```bash
php artisan jwt:secret
```

### 6. Migrar e popular banco de dados

```bash
php artisan migrate --seed
```

### 7. Iniciar servidor de desenvolvimento

```bash
php artisan serve
```

## 🔐 Autenticação

A API usa JWT (JSON Web Tokens) para autenticação. Inclua o token no header das requisições:

```
Authorization: Bearer {seu_token}
```

### Endpoints de Autenticação

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/api/auth/register` | Registrar novo usuário |
| POST | `/api/auth/login` | Fazer login |
| POST | `/api/auth/logout` | Fazer logout |
| POST | `/api/auth/refresh` | Atualizar token |
| GET | `/api/auth/me` | Obter informações do usuário logado |

## 📋 Endpoints da API

### Alunos

| Método | Endpoint | Descrição | Permissão |
|--------|----------|-----------|-----------|
| GET | `/api/alunos` | Listar alunos (com filtros) | Funcionário/Gestor |
| POST | `/api/alunos` | Criar novo aluno | Funcionário/Gestor |
| GET | `/api/alunos/{id}` | Obter detalhes de um aluno | Funcionário/Gestor |
| PUT | `/api/alunos/{id}` | Atualizar dados do aluno | Funcionário/Gestor |
| PATCH | `/api/alunos/{id}/status` | Atualizar status do aluno | Apenas Gestor |
| DELETE | `/api/alunos/{id}` | Excluir aluno | Apenas Gestor |

### Filtros disponíveis para GET `/api/alunos`

- `nome` - Filtrar por nome do aluno
- `cpf` - Filtrar por CPF
- `data_nascimento` - Filtrar por data de nascimento
- `turma` - Filtrar por turma
- `status` - Filtrar por status (ativo/inativo)
- `per_page` - Itens por página (padrão: 15)

**Exemplo de uso:**
```
GET /api/alunos?nome=João&status=ativo&per_page=10
```

## 👥 Usuários Pré-cadastrados

O sistema já vem com dois usuários cadastrados:

### Gestor
- **Email:** `gestor@teste.com`
- **Senha:** `123456`
- **Permissões:** Todas (criar, editar, excluir, alterar status)

### Funcionário
- **Email:** `funcionario@teste.com`
- **Senha:** `123456`
- **Permissões:** Limitadas (não pode excluir alunos ou alterar status)

## 🧪 Testando a API

### 1. Usando Postman/Insomnia
Importe a coleção de requisições do arquivo `api_collection.json` (disponível na raiz do projeto)

### 2. Via cURL

#### Login:
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"gestor@teste.com","password":"123456"}'
```

#### Listar alunos:
```bash
curl -X GET http://localhost:8000/api/alunos \
  -H "Authorization: Bearer {seu_token}"
```

#### Criar novo aluno:
```bash
curl -X POST http://localhost:8000/api/alunos \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {seu_token}" \
  -d '{
    "nome": "João Silva",
    "email": "joao@exemplo.com",
    "cpf": "123.456.789-00",
    "data_nascimento": "2000-05-15",
    "turma": "Turma A"
  }'
```

## 🛠️ Variáveis de Ambiente Importantes

| Variável | Descrição | Exemplo |
|----------|-----------|---------|
| `APP_ENV` | Ambiente da aplicação | `local`/`production` |
| `APP_KEY` | Chave da aplicação | Gerada automaticamente |
| `DB_CONNECTION` | Tipo de banco de dados | `mysql`/`pgsql` |
| `DB_HOST` | Host do banco de dados | `127.0.0.1` |
| `DB_PORT` | Porta do banco de dados | `3306`/`5432` |
| `DB_DATABASE` | Nome do banco de dados | `sistema_alunos` |
| `DB_USERNAME` | Usuário do banco | `root` |
| `DB_PASSWORD` | Senha do banco | |
| `JWT_SECRET` | Chave secreta para JWT | Gerada automaticamente |

## 📱 Estrutura de Resposta da API

### Sucesso
```json
{
  "success": true,
  "data": {
    // dados da resposta
  },
  "message": "Operação realizada com sucesso"
}
```

### Erro
```json
{
  "success": false,
  "message": "Mensagem de erro",
  "errors": {
    // detalhes dos erros de validação (quando aplicável)
  }
}
```

## 🚀 Deploy em Produção

### 1. Configurar servidor web (Apache/Nginx)
### 2. Instalar dependências sem dev
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Configurar variáveis de ambiente para produção
```bash
APP_ENV=production
APP_DEBUG=false
```

### 4. Gerar cache de configuração
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🐛 Troubleshooting

### Problemas comuns e soluções:

#### Erro de autenticação JWT:
- Verifique se o token está sendo enviado no header
- Execute `php artisan jwt:secret` para gerar nova chave
- Limpe o cache: `php artisan config:clear`

---

⭐ **Se este projeto foi útil para você, considere dar uma estrela!**