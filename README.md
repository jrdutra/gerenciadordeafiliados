# Gerenciador de Afiliados

Sistema simples em PHP puro que gera páginas com produtos afiliados. Cada produto possui um link de afiliado, imagem em base64 e um QR code. As páginas também exibem um QR code para fácil compartilhamento.

## Banco de Dados
Crie um banco MariaDB/MySQL e execute os comandos:

```sql
CREATE TABLE pages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  page_id INT NOT NULL,
  nome VARCHAR(255) NOT NULL,
  link_afiliado TEXT NOT NULL,
  imagem_base64 LONGTEXT,
  FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
);
```

## Configuração e Execução
1. Ajuste as credenciais de banco em `index.php` e `admin.php`.
2. Coloque o projeto no diretório público do seu servidor (ex.: `htdocs` do XAMPP) ou use o servidor embutido do PHP:
   ```bash
   php -S localhost:8000 -t .
   ```
3. Acesse `admin.php` para criar páginas e cadastrar produtos.
4. Acesse `index.php?id=ID_DA_PAGINA` para visualizar a página pública.

Os QR codes são gerados através do serviço gratuito `api.qrserver.com`.
