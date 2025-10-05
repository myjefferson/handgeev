CREATE TABLE subscriptions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    plan_id TINYINT UNSIGNED NOT NULL,
    stripe_subscription_id VARCHAR(255) NOT NULL,
    stripe_price_id VARCHAR(255) NOT NULL,
    status ENUM('active', 'canceled', 'past_due', 'unpaid', 'incomplete') NOT NULL,
    current_period_start TIMESTAMP NOT NULL,
    current_period_end TIMESTAMP NOT NULL,
    canceled_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id),
    UNIQUE KEY unique_stripe_subscription (stripe_subscription_id),
    INDEX idx_user_status (user_id, status),
    INDEX idx_period_end (current_period_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=UTF8MB4_UNICODE_CI;

SELECT * FROM subscriptions;

-- Tabela para logs de pagamento
CREATE TABLE payment_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    stripe_event_id VARCHAR(255) NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    payload JSON NOT NULL,
    processed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_stripe_event (stripe_event_id),
    INDEX idx_event_type (event_type),
    INDEX idx_processed_at (processed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=UTF8MB4_UNICODE_CI;


-- Adicionar colunas para controle de upgrades
ALTER TABLE `subscriptions` 
ADD `previous_subscription_id` BIGINT UNSIGNED NULL AFTER `plan_id`,
ADD `upgrade_type` ENUM('new', 'upgrade', 'downgrade', 'crossgrade') DEFAULT 'new' AFTER `status`,
ADD `proration_amount` DECIMAL(10,2) NULL AFTER `stripe_subscription_id`,
ADD `billing_cycle_anchor` TIMESTAMP NULL AFTER `ends_at`;

-- Foreign key para histórico
ALTER TABLE `subscriptions` 
ADD FOREIGN KEY (`previous_subscription_id`) REFERENCES `subscriptions`(`id`) ON DELETE SET NULL;

-- Índice para histórico
ALTER TABLE `subscriptions` 
ADD INDEX `idx_previous_subscription` (`previous_subscription_id`),
ADD INDEX `idx_upgrade_type` (`upgrade_type`);