# Gerenciador de Afiliados

Sistema simples em PHP puro que gera páginas com produtos afiliados. Cada produto possui um link de afiliado, imagem em base64 e um QR code. As páginas também exibem um QR code para fácil compartilhamento.

## Banco de Dados
Crie um banco MariaDB/MySQL e execute os comandos:

```sql
CREATE TABLE tb_pagina_afiliados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE tb_produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    url_afiliado TEXT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    imagem LONGTEXT DEFAULT NULL
);

CREATE TABLE tb_pagina_produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pagina_id INT NOT NULL,
    produto_id INT NOT NULL,
    FOREIGN KEY (pagina_id) REFERENCES tb_pagina_afiliados(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES tb_produto(id) ON DELETE CASCADE
);
```

## Configuração e Execução
1. Ajuste as credenciais de banco em `index.php` e `admin.php`.
2. Coloque o projeto no diretório público do seu servidor (ex.: `htdocs` do XAMPP) ou use o servidor embutido do PHP:
   ```bash
   php -S localhost:8000 -t .
   ```
3. Acesse `admin.php` para criar páginas e cadastrar produtos.
4. Acesse `index.php?slug=SLUG_DA_PAGINA` para visualizar a página pública.

Os QR codes são gerados através do serviço gratuito `api.qrserver.com`.
