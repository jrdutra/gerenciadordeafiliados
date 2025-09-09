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
    imagem VARCHAR(255) DEFAULT NULL
);

CREATE TABLE tb_pagina_produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pagina_id INT NOT NULL,
    produto_id INT NOT NULL,
    FOREIGN KEY (pagina_id) REFERENCES tb_pagina_afiliados(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES tb_produto(id) ON DELETE CASCADE
);
