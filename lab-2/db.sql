-- SEC//LAB — import lewat phpMyAdmin atau: mysql -u root < db.sql
CREATE DATABASE IF NOT EXISTS lab_app CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE lab_app;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  username   VARCHAR(64)  NOT NULL,
  password   VARCHAR(64)  NOT NULL,
  role       VARCHAR(16)  NOT NULL DEFAULT 'user',
  email      VARCHAR(128) NOT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password, role, email, created_at) VALUES
  ('admin', 'admin123', 'admin', 'admin@seclab.local', '2024-01-05 09:12:00'),
  ('budi',  'budi123',  'user',  'budi@seclab.local',  '2024-01-06 10:30:00'),
  ('siti',  'siti123',  'user',  'siti@seclab.local',  '2024-01-09 14:02:00'),
  ('root',  'toor',     'admin', 'root@seclab.local',  '2024-01-10 22:41:00'),
  ('guest', 'guest',    'guest', 'guest@seclab.local', '2024-02-01 08:00:00'),
  ('ceo',   'ceo123',   'admin', 'ceo@seclab.local',   '2024-02-11 16:45:00'),
  ('yui',   'yui123',   'staff', 'yui@seclab.local',   '2024-02-14 11:20:00'),
  ('kazuo', 'kazuo123', 'user',  'kazuo@seclab.local', '2024-02-20 19:05:00');

DROP TABLE IF EXISTS secrets;
CREATE TABLE secrets (
  id    INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(64)  NOT NULL,
  value VARCHAR(128) NOT NULL
);

INSERT INTO secrets (label, value) VALUES
  ('flag',      'FLAG{sql1_1nj3ct10n_101}'),
  ('api_key',   'sk-live-5ECLAB-8f2a1c9d'),
  ('db_backup', '/backup/lab_app_2024.sql');
