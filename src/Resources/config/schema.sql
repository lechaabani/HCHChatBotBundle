CREATE TABLE chatbot_quota_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider VARCHAR(50) NOT NULL,
    count INT NOT NULL DEFAULT 0,
    date DATE NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_provider_date (provider, date)
);

CREATE TABLE chatbot_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    response TEXT NOT NULL,
    provider VARCHAR(50) NOT NULL,
    tokens INT NOT NULL DEFAULT 0,
    duration FLOAT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    INDEX idx_provider (provider),
    INDEX idx_created_at (created_at)
);

CREATE TABLE chatbot_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    context JSON,
    created_at DATETIME NOT NULL,
    INDEX idx_level (level),
    INDEX idx_created_at (created_at)
); 