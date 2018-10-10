CREATE TABLE IF NOT EXISTS accounts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(16) NOT NULL,
  api_key CHAR(64) NOT NULL,
  secret CHAR(64) NOT NULL
);

CREATE UNIQUE INDEX unq_account_name ON accounts (name);
CREATE UNIQUE INDEX unq_account_credential ON accounts (api_key, secret);

CREATE TABLE IF NOT EXISTS currencies (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  symbol CHAR(5) NOT NULL,
  name VARCHAR(16)
);

CREATE UNIQUE INDEX unq_currency_symbol ON currencies(symbol);

CREATE TABLE IF NOT EXISTS currency_pairs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  base_id INTEGER NOT NULL,
  quote_id INTEGER NOT NULL,
  symbol VARCHAR(8) NOT NULL,
  CONSTRAINT fk_currency_pair_base FOREIGN KEY (base_id) REFERENCES currencies(id) ON DELETE CASCADE,
  CONSTRAINT fk_currency_pair_quote FOREIGN KEY (quote_id) REFERENCES currencies(id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX unq_currency_pair ON currency_pairs (base_id, quote_id);
CREATE UNIQUE INDEX unq_currency_pair_symbol ON currency_pairs (symbol);

CREATE TABLE IF NOT EXISTS orders (
  id INTEGER UNSIGNED NOT NULL,
  account_id INTEGER NOT NULL,
  pair_id INTEGER NOT NULL,
  side CHAR(3) NOT NULL,
  status CHAR(4) NOT NULL,
  price DOUBLE NOT NULL,
  volume DOUBLE NOT NULL,
  created_at TIMESTAMP NOT NULL,
  updated_at TIMESTAMP,
  CONSTRAINT fk_order_account FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_pair FOREIGN KEY (pair_id) REFERENCES currency_pairs ON DELETE CASCADE
);

CREATE UNIQUE INDEX unq_order_id ON orders(id);
CREATE INDEX indx_order_account ON orders(account_id);
CREATE INDEX indx_order_pair ON orders(pair_id);

CREATE TABLE IF NOT EXISTS trades (
  id INTEGER UNSIGNED NOT NULL,
  order_id INTEGER UNSIGNED NOT NULL,
  type CHAR(4) NOT NULL,
  price DOUBLE NOT NULL,
  volume DOUBLE NOT NULL,
  `timestamp` TIMESTAMP NOT NULL,
  CONSTRAINT fk_trade_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX unq_trade_id ON trades(id);
CREATE INDEX indx_trade_order ON trades(order_id);

CREATE TABLE IF NOT EXISTS actives (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  account_id INTEGER NOT NULL,
  currency_id INTEGER NOT NULL,
  position_id INTEGER,
  volume DOUBLE NOT NULL,
  CONSTRAINT fk_active_account FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
  CONSTRAINT fk_active_currency FOREIGN KEY (currency_id) REFERENCES currencies(id) ON DELETE CASCADE,
  CONSTRAINT fk_active_position FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE SET NULL
);

CREATE UNIQUE INDEX unq_active ON actives(account_id, currency_id);
CREATE INDEX indx_active_position ON actives(position_id);

CREATE TABLE IF NOT EXISTS positions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  active_id INTEGER NOT NULL,
  price DOUBLE NOT NULL,
  volume DOUBLE NOT NULL,
  CONSTRAINT fk_position_active FOREIGN KEY (active_id) REFERENCES actives(id) ON DELETE CASCADE
);

CREATE INDEX indx_position_active ON positions(active_id);