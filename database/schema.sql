-- ==============================
-- BANCO DE DADOS: OrgFiscal
-- ==============================

-- USUÁRIOS
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- OBRIGAÇÕES FISCAIS (MODELO FIXO)
CREATE TABLE obrigacoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  tipo ENUM('mensal', 'anual') NOT NULL,
  descricao TEXT NOT NULL,
  importancia TEXT NOT NULL,
  portal_nome VARCHAR(100),
  portal_url VARCHAR(255),
  passo_a_passo TEXT
);

-- LEMBRETES (INSTÂNCIA DA OBRIGAÇÃO)
CREATE TABLE lembretes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  obrigacao_id INT NOT NULL,
  competencia_mes INT,
  competencia_ano INT NOT NULL,
  status ENUM('pendente', 'concluido') DEFAULT 'pendente',
  data_conclusao DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (obrigacao_id) REFERENCES obrigacoes(id)
);

-- FATURAMENTO (FUTURO - BLING)
CREATE TABLE faturamento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  mes INT NOT NULL,
  ano INT NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  origem ENUM('manual', 'bling') DEFAULT 'manual',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
