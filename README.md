# Gerenciador de Afiliados

Sistema simples em PHP (sem framework) para gerar páginas com produtos afiliados. Cada produto possui um link de afiliado e um QR code. As páginas também exibem um QR code para fácil compartilhamento.

## Instalação

1. Instale as dependências PHP:

```bash
composer install
```

2. Crie o banco de dados MariaDB/MySQL e execute o script em `db/schema.sql`.

3. Ajuste as configurações de conexão em `config/config.php`.

4. Inicie o servidor embutido do PHP apontando para a raiz do projeto:

```bash
php -S localhost:8000 -t .
```

Acesse `/admin/index.php` para gerenciar as páginas e produtos.

A página pública inicial está em `index.php` e páginas geradas são acessadas em `page.php?slug=SEU_SLUG`.
