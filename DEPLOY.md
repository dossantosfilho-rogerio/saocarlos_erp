# Guia de Deploy — São Carlos ERP

## Pré-requisitos na sua máquina
- Terminal com **ssh** e **bash** (Git Bash no Windows, ou WSL)
- O repositório precisa estar no GitHub/GitLab (ver passo 1)

---

## Passo 1 — Enviar o código para o Git

```bash
git init
git add .
git commit -m "deploy inicial"
git remote add origin https://github.com/SEU_USUARIO/SEU_REPO.git
git push -u origin main
```

Edite a variável `GIT_REPO` no `deploy.sh` com a URL correta.

---

## Passo 2 — Executar o deploy

No terminal, dentro da pasta do projeto:

```bash
# Linux/Mac/WSL
bash deploy.sh

# Windows — Git Bash
bash deploy.sh
```

O script irá pedir a senha SSH da VPS uma vez.

---

## Passo 3 — Configurar o .env na VPS (primeira vez)

O script avisa se o `.env` não existir. Conecte-se e edite:

```bash
ssh root@145.223.29.87
nano /var/www/saocarlos_erp/.env
```

Campos essenciais:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://145.223.29.87

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/saocarlos_erp/database/database.sqlite
```

Salve (Ctrl+O, Enter, Ctrl+X) e rode `bash deploy.sh` novamente.

---

## Deploys subsequentes (atualizações)

Basta rodar `bash deploy.sh` novamente — o script faz `git pull`, reinstala dependências e recria os caches.

---

## Segurança recomendada

1. **Troque a senha root** após o primeiro acesso:
   ```bash
   ssh root@145.223.29.87
   passwd
   ```
2. **Crie um usuário não-root** para SSH:
   ```bash
   adduser deploy
   usermod -aG sudo deploy
   ```
3. **Desative login root por SSH** em `/etc/ssh/sshd_config`:
   ```
   PermitRootLogin no
   ```
4. Considere usar **chaves SSH** em vez de senha.
