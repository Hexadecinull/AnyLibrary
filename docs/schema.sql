-- AnyLibrary — Free, open-source book library
-- Copyright (C) 2026  AnyLibrary Contributors
-- (GPL-3.0 license)
--
-- Database schema for AnyLibrary
-- Requires MySQL 5.7+ or MariaDB 10.3+
-- Run once after importing config.php credentials.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ─── Users ───────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(80)     NOT NULL,
    `email`         VARCHAR(180)    NOT NULL,
    `password_hash` VARCHAR(255)    NOT NULL,
    `avatar_url`    VARCHAR(512)        NULL DEFAULT NULL,
    `role`          ENUM('user','mod','admin') NOT NULL DEFAULT 'user',
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE  KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Open Library cache ───────────────────────────────────────────────────────
-- Caches all external API responses (Open Library, MangaDex, LibriVox) to
-- avoid repeated network calls and respect upstream rate limits.

CREATE TABLE IF NOT EXISTS `ol_cache` (
    `cache_key`  VARCHAR(255) NOT NULL,
    `data`       MEDIUMTEXT   NOT NULL,
    `expires_at` DATETIME     NOT NULL,
    PRIMARY KEY (`cache_key`),
    KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Favorites ───────────────────────────────────────────────────────────────
-- Supports both authenticated users and anonymous guest tokens.
-- item_type: 'book' | 'manga' | 'audiobook' | 'imported'
-- item_id:   Open Library work ID, MangaDex UUID, LibriVox ID, or imported book ID

CREATE TABLE IF NOT EXISTS `favorites` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED        NULL DEFAULT NULL,
    `guest_token` VARCHAR(64)         NULL DEFAULT NULL,
    `item_id`     VARCHAR(128)    NOT NULL,
    `item_type`   ENUM('book','manga','audiobook','imported') NOT NULL DEFAULT 'book',
    `cover_url`   VARCHAR(512)        NULL DEFAULT NULL,
    `title`       VARCHAR(512)    NOT NULL DEFAULT '',
    `authors`     JSON                NULL,
    `added_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_item`  (`user_id`,    `item_id`),
    UNIQUE KEY `uq_guest_item` (`guest_token`, `item_id`),
    KEY `idx_user_id`     (`user_id`),
    KEY `idx_guest_token` (`guest_token`),
    CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Reading history ──────────────────────────────────────────────────────────
-- Tracks reading progress. progress_pct: 0–100 float.
-- last_position: JSON blob — { scroll_pct, chapter_id, track, time } depending on type.
-- This replaces StreamSuite's watch_history (which tracked video seconds).

CREATE TABLE IF NOT EXISTS `reading_history` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED        NULL DEFAULT NULL,
    `guest_token`   VARCHAR(64)         NULL DEFAULT NULL,
    `item_id`       VARCHAR(128)    NOT NULL,
    `item_type`     ENUM('book','manga','audiobook','imported') NOT NULL DEFAULT 'book',
    `cover_url`     VARCHAR(512)        NULL DEFAULT NULL,
    `title`         VARCHAR(512)    NOT NULL DEFAULT '',
    `authors`       JSON                NULL,
    `progress_pct`  FLOAT           NOT NULL DEFAULT 0 COMMENT '0–100 percentage read/listened',
    `last_position` JSON                NULL COMMENT 'Type-specific position object',
    `last_read_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_item`  (`user_id`,    `item_id`),
    UNIQUE KEY `uq_guest_item` (`guest_token`, `item_id`),
    KEY `idx_user_id`       (`user_id`),
    KEY `idx_guest_token`   (`guest_token`),
    KEY `idx_last_read_at`  (`last_read_at`),
    CONSTRAINT `fk_hist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Imported books ───────────────────────────────────────────────────────────
-- Stores metadata for user-uploaded EPUB / PDF / TXT / CBZ files.
-- The actual file is stored on disk at the path configured in IMPORT_STORAGE_PATH.

CREATE TABLE IF NOT EXISTS `imported_books` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED        NULL DEFAULT NULL,
    `guest_token`   VARCHAR(64)         NULL DEFAULT NULL,
    `filename`      VARCHAR(128)    NOT NULL COMMENT 'Stored filename (random slug.ext)',
    `original_name` VARCHAR(512)    NOT NULL COMMENT 'Original filename from the user',
    `file_ext`      VARCHAR(10)     NOT NULL,
    `file_size`     INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT 'In bytes',
    `title`         VARCHAR(512)    NOT NULL DEFAULT '',
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id`     (`user_id`),
    KEY `idx_guest_token` (`guest_token`),
    CONSTRAINT `fk_imp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Bookmarks ────────────────────────────────────────────────────────────────
-- Named bookmarks within a book (e.g. a specific scroll position or chapter).

CREATE TABLE IF NOT EXISTS `bookmarks` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED        NULL DEFAULT NULL,
    `guest_token`VARCHAR(64)         NULL DEFAULT NULL,
    `item_id`    VARCHAR(128)    NOT NULL,
    `item_type`  ENUM('book','manga','audiobook','imported') NOT NULL DEFAULT 'book',
    `label`      VARCHAR(256)        NULL DEFAULT NULL,
    `position`   JSON                NULL COMMENT 'Scroll %, chapter_id, track index, etc.',
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_item`  (`user_id`,     `item_id`),
    KEY `idx_guest_item` (`guest_token`, `item_id`),
    CONSTRAINT `fk_bm_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Cleanup event ───────────────────────────────────────────────────────────
-- Automatically purges expired API cache rows daily.
-- Enable the MySQL Event Scheduler with: SET GLOBAL event_scheduler = ON;

CREATE EVENT IF NOT EXISTS `purge_ol_cache`
    ON SCHEDULE EVERY 1 DAY
    STARTS CURRENT_TIMESTAMP
    DO DELETE FROM `ol_cache` WHERE `expires_at` < NOW();
