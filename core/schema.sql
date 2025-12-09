-- TABLE FOR USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    is_super TINYINT(1) DEFAULT 0,
    suspended TINYINT(1) DEFAULT 0,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLE FOR PROJECTS AND CERTIFICATION
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    type ENUM('Project','Certification') DEFAULT 'Project',
    image VARCHAR(255) DEFAULT NULL,
    added_by INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL
);

-- TABLE FOR RESUME (THE VALUES ARE ON JSON FORMAT)
CREATE TABLE resume_sections (
    section VARCHAR(50) PRIMARY KEY,
    value TEXT
);

