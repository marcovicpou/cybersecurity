PRAGMA foreign_keys = ON;
CREATE TABLE IF NOT EXISTS visitors (
 id TEXT PRIMARY KEY, address_json TEXT NOT NULL DEFAULT '{}', created_at TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS restaurants (
 id TEXT PRIMARY KEY, slug TEXT NOT NULL UNIQUE, name TEXT NOT NULL, category TEXT NOT NULL,
 kind TEXT NOT NULL, tag TEXT NOT NULL, description TEXT NOT NULL, fee INTEGER NOT NULL CHECK(fee>=0),
 minimum INTEGER NOT NULL CHECK(minimum>=0), eta_min INTEGER NOT NULL, eta_max INTEGER NOT NULL,
 is_open INTEGER NOT NULL CHECK(is_open IN(0,1)), color TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS products (
 id TEXT PRIMARY KEY, restaurant_id TEXT NOT NULL REFERENCES restaurants(id) ON DELETE RESTRICT,
 name TEXT NOT NULL, description TEXT NOT NULL, price INTEGER NOT NULL CHECK(price>=0),
 image TEXT NOT NULL, group_name TEXT NOT NULL, available INTEGER NOT NULL CHECK(available IN(0,1)), options_json TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS products_restaurant ON products(restaurant_id);
CREATE TABLE IF NOT EXISTS favorites (
 visitor_id TEXT NOT NULL REFERENCES visitors(id) ON DELETE CASCADE,
 restaurant_id TEXT NOT NULL REFERENCES restaurants(id) ON DELETE CASCADE,
 PRIMARY KEY(visitor_id,restaurant_id)
);
CREATE TABLE IF NOT EXISTS carts (
 visitor_id TEXT PRIMARY KEY REFERENCES visitors(id) ON DELETE CASCADE,
 restaurant_id TEXT REFERENCES restaurants(id) ON DELETE RESTRICT,
 version INTEGER NOT NULL DEFAULT 0, coupon TEXT NOT NULL DEFAULT ''
);
CREATE TABLE IF NOT EXISTS cart_items (
 id TEXT PRIMARY KEY, visitor_id TEXT NOT NULL REFERENCES carts(visitor_id) ON DELETE CASCADE,
 product_id TEXT NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
 quantity INTEGER NOT NULL CHECK(quantity BETWEEN 1 AND 20), option_index INTEGER NOT NULL,
 note TEXT NOT NULL DEFAULT '', signature TEXT NOT NULL,
 UNIQUE(visitor_id,signature)
);
CREATE INDEX IF NOT EXISTS cart_items_visitor ON cart_items(visitor_id);
CREATE TABLE IF NOT EXISTS orders (
 id TEXT PRIMARY KEY, visitor_id TEXT NOT NULL REFERENCES visitors(id) ON DELETE CASCADE,
 restaurant_id TEXT NOT NULL REFERENCES restaurants(id) ON DELETE RESTRICT,
 created_at TEXT NOT NULL, status TEXT NOT NULL CHECK(status IN('confirmed','cancelled')),
 address_json TEXT NOT NULL, subtotal INTEGER NOT NULL CHECK(subtotal>=0),
 fee INTEGER NOT NULL CHECK(fee>=0), discount INTEGER NOT NULL CHECK(discount>=0),
 total INTEGER NOT NULL CHECK(total>=0), coupon TEXT NOT NULL,
 payment TEXT NOT NULL CHECK(payment IN('demo_pix','demo_card','demo_cash')),
 idempotency_key TEXT NOT NULL, cancelled_at TEXT,
 UNIQUE(visitor_id,idempotency_key)
);
CREATE INDEX IF NOT EXISTS orders_visitor_created ON orders(visitor_id,created_at);
CREATE TABLE IF NOT EXISTS order_items (
 id TEXT PRIMARY KEY, order_id TEXT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
 name TEXT NOT NULL, option_name TEXT NOT NULL, note TEXT NOT NULL,
 quantity INTEGER NOT NULL CHECK(quantity>0), unit_price INTEGER NOT NULL CHECK(unit_price>=0)
);
CREATE INDEX IF NOT EXISTS order_items_order ON order_items(order_id);
CREATE TABLE IF NOT EXISTS checkout_attempts (
 visitor_id TEXT NOT NULL REFERENCES visitors(id) ON DELETE CASCADE, attempted_at INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS attempts_visitor_time ON checkout_attempts(visitor_id,attempted_at);
