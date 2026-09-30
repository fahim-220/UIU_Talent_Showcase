USE uiu_talent_showcase;

-- Admin: Admin@12345
-- Users: User@12345

INSERT INTO users (name, email, password_hash, role, department, bio, avatar) VALUES
('Admin', 'admin@uiu.ac.bd', '$2y$10$2RTv2qkg81vMNaMey6etouYa9GKrOEiekegS0XlEZX83ydSUV0gdS', 'admin', 'Administration', 'Site Administrator', 'https://i.pravatar.cc/150?img=11'),
('Omar Faruq', 'omar@uiu.ac.bd', '$2y$10$Q0FBSU8MrV0AAEnnCuDEFOFMsFnzf0dStDdy6jQ.LRpNs04EfBk/q', 'user', 'CSE', 'Passionate about coding and tech blogging.', 'https://i.pravatar.cc/150?img=40'),
('Priya Sharma', 'priya@uiu.ac.bd', '$2y$10$Q0FBSU8MrV0AAEnnCuDEFOFMsFnzf0dStDdy6jQ.LRpNs04EfBk/q', 'user', 'BBA', 'Singer and guitarist.', 'https://i.pravatar.cc/150?img=12'),
('Mehedi Khan', 'mehedi@uiu.ac.bd', '$2y$10$Q0FBSU8MrV0AAEnnCuDEFOFMsFnzf0dStDdy6jQ.LRpNs04EfBk/q', 'user', 'EEE', 'Cinematography enthusiast.', 'https://i.pravatar.cc/150?img=9'),
('Zara Ahmed', 'zara@uiu.ac.bd', '$2y$10$Q0FBSU8MrV0AAEnnCuDEFOFMsFnzf0dStDdy6jQ.LRpNs04EfBk/q', 'user', 'English', 'Creative writer and poet.', 'https://i.pravatar.cc/150?img=45'),
('Tanvir Hossain', 'tanvir@uiu.ac.bd', '$2y$10$Q0FBSU8MrV0AAEnnCuDEFOFMsFnzf0dStDdy6jQ.LRpNs04EfBk/q', 'user', 'CSE', 'Video editor and animator.', 'https://i.pravatar.cc/150?img=5');
