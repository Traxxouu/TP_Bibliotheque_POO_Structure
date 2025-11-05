
create database if not exists bibliotheque character set utf8mb4 collate utf8mb4_unicode_ci;
USE bibliotheque;

create table if not exists livres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    auteur VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

insert into livres (titre, auteur) values
('Memory Piece', 'Lisa Ko'),
('Misery', 'Stephen King'),
('The Silent Patient', 'Alex Michaelides'),
('Where the Crawdads Sing', 'Delia Owens'),
('The Night Circus', 'Erin Morgenstern'),
('Circe', 'Madeline Miller'),
('The Goldfinch', 'Donna Tartt'),
('The Alchemist', 'Paulo Coelho');


-- ETAPE 2 : les utilisatueurs
USE bibliotheque;
create table if not exists utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;