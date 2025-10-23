-- -- Application users
-- CREATE TABLE IF NOT EXISTS users (
--     id SERIAL PRIMARY KEY,
--     email VARCHAR(255) UNIQUE NOT NULL,
--     name VARCHAR(255) NOT NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );
--
-- -- Tokens (e.g. for JWT)
-- CREATE TABLE IF NOT EXISTS tokens (
--     id SERIAL PRIMARY KEY,
--     user_id INTEGER REFERENCES users(id),
--     token TEXT NOT NULL,
--     expires_at TIMESTAMP NOT NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );
--
-- -- Background tasks (for status monitoring)
-- CREATE TABLE IF NOT EXISTS background_tasks (
--     id SERIAL PRIMARY KEY,
--     type VARCHAR(100) NOT NULL,
--     status VARCHAR(50) DEFAULT 'pending',
--     payload JSONB,
--     result JSONB,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );
--
-- -- Indexes for performance
-- CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
-- CREATE INDEX IF NOT EXISTS idx_tokens_user_id ON tokens(user_id);
-- CREATE INDEX IF NOT EXISTS idx_tasks_status ON background_tasks(status);
--
-- -- Initial data
-- INSERT INTO users (email, name) VALUES
-- ('admin@example.com', 'Administrator'),
-- ('test@example.com', 'Test User')
-- ON CONFLICT (email) DO NOTHING;

-- ==========================================================
--  PostgreSQL schema for Bitrix24 integration (Doctrine ORM)
-- ==========================================================

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-------------------------------------------------------------
-- Table: bitrix24account
-------------------------------------------------------------
CREATE TABLE bitrix24account (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    b24_user_id INTEGER NOT NULL,
    is_b24_user_admin BOOLEAN NOT NULL,
    member_id VARCHAR NOT NULL,
    is_master_account BOOLEAN,
    domain_url VARCHAR NOT NULL,
    status VARCHAR NOT NULL,
    application_token VARCHAR,
    created_at_utc TIMESTAMP(3) NOT NULL,
    updated_at_utc TIMESTAMP(3) NOT NULL,
    application_version INTEGER NOT NULL,
    comment TEXT,

    -- Embedded AuthToken fields
    access_token VARCHAR,
    refresh_token VARCHAR,
    expires INTEGER,
    expires_in INTEGER,

    -- Embedded Scope field
    current_scope JSON
);

-------------------------------------------------------------
-- Table: application_installation
-------------------------------------------------------------
CREATE TABLE application_installation (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    status VARCHAR NOT NULL,
    created_at_utc TIMESTAMP(3) NOT NULL,
    update_at_utc TIMESTAMP(3) NOT NULL,
    bitrix_24_account_id UUID NOT NULL UNIQUE,
    contact_person_id UUID,
    bitrix_24_partner_contact_person_id UUID,
    bitrix_24_partner_id UUID,
    external_id VARCHAR,
    portal_license_family VARCHAR NOT NULL,
    portal_users_count INTEGER,
    application_token VARCHAR,
    comment TEXT,

    -- Embedded ApplicationStatus field
    status_code JSON,

    CONSTRAINT fk_application_installation_account
        FOREIGN KEY (bitrix_24_account_id)
        REFERENCES bitrix24account (id)
        ON DELETE CASCADE
);

-------------------------------------------------------------
-- Indexes (optional, but good for performance)
-------------------------------------------------------------
CREATE INDEX idx_bitrix24account_member_id ON bitrix24account (member_id);
CREATE INDEX idx_bitrix24account_domain_url ON bitrix24account (domain_url);

CREATE INDEX idx_application_installation_status ON application_installation (status);
CREATE INDEX idx_application_installation_portal_license_family ON application_installation (portal_license_family);
