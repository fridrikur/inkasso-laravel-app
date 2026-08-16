-- ==========================================
-- DEMO-DATA SQL DUMP
-- Kan køres lynhurtigt uden 504 timeouts
-- ==========================================

SET FOREIGN_KEY_CHECKS = 0;

-- Tøm evt. eksisterende data hvis du vil starte helt rent (fjern kommentering hvis nødvendigt)
-- TRUNCATE TABLE sager;
-- TRUNCATE TABLE kreditorer;
-- TRUNCATE TABLE users;
-- TRUNCATE TABLE sagsbehandlere;

-- 1. Indsæt demo-kreditorer
INSERT INTO kreditorer (id, navn, lotusID, created_at, updated_at) VALUES 
(1, 'Nordea Inkasso A/S', 'LOTUS-1001', NOW(), NOW()),
(2, 'YouSee Erhverv', 'LOTUS-1002', NOW(), NOW()),
(3, 'Ejendomsselskabet Dania', 'LOTUS-1003', NOW(), NOW());

-- 2. Indsæt demo-brugere 
-- (Adgangskoden til disse brugere er 'password123' hashet med Bcrypt)
INSERT INTO users (id, name, email, password, created_at, updated_at) VALUES 
(1, 'System Administrator', 'admin@example.com', '$2y$12$8W2t5X.Z9... (indsæt gyldig hash ellers opret via seeder)', NOW(), NOW()),
(2, 'Kreditor Bruger', 'kreditor@example.com', '$2y$12$8W2t5X.Z9...', NOW(), NOW());

-- 3. Indsæt sagsbehandlere
INSERT INTO sagsbehandlere (id, navn, email, tlf, mobil, created_at, updated_at) VALUES 
(1, 'Lars Jensen', 'lars@example.com', '12345678', '87654321', NOW(), NOW()),
(2, 'Mette Hansen', 'mette@example.com', '23456789', '98765432', NOW(), NOW());

-- 4. Indsæt sager (tilknyttet systemet)
INSERT INTO sagers (id, sagsnr, status, created_at, updated_at) VALUES 
(1, 'SAG-2026-001', 'Aktiv', NOW(), NOW()),
(2, 'SAG-2026-002', 'Aktiv', NOW(), NOW()),
(3, 'SAG-2026-003', 'Afsluttet', NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
