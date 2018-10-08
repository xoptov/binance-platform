CREATE TABLE IF NOT EXISTS accounts (
  id INTEGER UNSIGNED,
  name VARCHAR(16) NOT NULL,
  apiKey CHAR(64) NOT NULL,
  secret CHAR(64) NOT NULL,
  CONSTRAINT `primary` PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS currencies (
  id INTEGER UNSIGNED,
  symbol CHAR(4) NOT NULL,
  name VARCHAR(16),
  CONSTRAINT `primary` PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS currency_pairs (
  id INTEGER UNSIGNED,
  base_id INTEGER UNSIGNED NOT NULL,
  quote_id INTEGER UNSIGNED NOT NULL,
  CONSTRAINT `primary` PRIMARY KEY (id),
  CONSTRAINT fk_currency_pairs_base FOREIGN KEY (base_id) REFERENCES currencies(id) ON DELETE CASCADE,
  CONSTRAINT fk_currency_pairs_base FOREIGN KEY (quote_id) REFERENCES currencies(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
  id INTEGER UNSIGNED,
  account_id INTEGER UNSIGNED NOT NULL,
  currency_pair_id INTEGER UNSIGNED NOT NULL,
  side CHAR(3) NOT NULL,
  status CHAR(4) NOT NULL,
  price DOUBLE NOT NULL,
  volume DOUBLE NOT NULL,
  created_at TIMESTAMP NOT NULL,
  updated_at TIMESTAMP,
  CONSTRAINT `primary` PRIMARY KEY (id),
  CONSTRAINT fk_orders_account FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
  CONSTRAINT fk_orders_currency_pair FOREIGN KEY (currency_pair_id) REFERENCES currency_pairs ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS trades (
  id INTEGER UNSIGNED,
  order_id INTEGER UNSIGNED NOT NULL,
  type CHAR(4) NOT NULL,
  price DOUBLE NOT NULL,
  volume DOUBLE NOT NULL,
  `timestamp` TIMESTAMP NOT NULL,
  CONSTRAINT `primary` PRIMARY KEY (id),
  CONSTRAINT fk_trades_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS actives (
  id INTEGER UNSIGNED,
  account_id INTEGER UNSIGNED NOT NULL,
  currency_id INTEGER UNSIGNED NOT NULL,
  volume DOUBLE NOT NULL,
  CONSTRAINT `primary` PRIMARY KEY (id),
  CONSTRAINT fk_actives_account FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
  CONSTRAINT fk_actives_currency FOREIGN KEY (currency_id) REFERENCES currencies(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS positions (
  id INTEGER UNSIGNED,
  price DOUBLE NOT NULL,
  volume DOUBLE NOT NULL
);

CREATE TABLE IF NOT EXISTS active_position (
  active_id INTEGER UNSIGNED NOT NULL,
  position_id INTEGER UNSIGNED NOT NULL,
  CONSTRAINT fk_active_position_1 FOREIGN KEY (active_id) REFERENCES actives(id) ON DELETE CASCADE,
  CONSTRAINT fk_active_position_2 FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE CASCADE,
  CONSTRAINT unq_active_position UNIQUE (active_id, position_id)
);
