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

INSERT INTO posts (user_id, type, title, description, file_path, cover_image) VALUES
(2, 'text', 'My Journey into Tech', 'Programming changed the way I see the world. It started with simple logic puzzles and grew into building web applications that solve real-world problems.', '', NULL),
(5, 'text', 'The Art of Poetry', 'Words flow like a river when you let your emotions guide the pen. Here is a short reflection on how writing heals the soul.', '', NULL),
(3, 'text', 'Finding Rhythm', 'Music is not just about playing the right notes, it is about feeling the space between them.', '', NULL);

INSERT IGNORE INTO likes (post_id, user_id) VALUES
(1, 1), (1, 3), (1, 5),
(2, 2), (2, 4),
(3, 1), (3, 6);

INSERT INTO comments (post_id, user_id, body) VALUES
(1, 3, 'This is really inspiring! I want to learn more.'),
(1, 5, 'Great read. Thanks for sharing your journey.'),
(2, 2, 'Poetry is indeed the language of the soul.'),
(3, 4, 'Very well said! Music connects us all.');

INSERT INTO competitions (title, category, description, start_date, deadline, prize, status) VALUES
('International Dancing Championship 2026', 'video', 'Bring your unique dance style to the global stage. Open to every age group and experience level.', '2026-08-15', '2026-11-30', '$5,000', 'open'),
('UIU Music Fest - Emerging Artists', 'audio', 'From acoustic sets to full bands, if it moves you, it will move us.', '2026-08-05', '2026-11-15', '$4,000', 'open'),
('UIU Literary Excellence Awards', 'text', 'Submit a short story, poem, personal essay, or flash fiction and compete for recognition.', '2026-09-01', '2026-12-01', '$2,000', 'open'),
('Lens Masters Video Competition', 'video', 'Capture this year\'s theme, Human Connection, through authentic videos and emotional storytelling.', '2026-08-15', '2026-12-10', '$3,500', 'open'),
('Past Winter Talent Show', 'video', 'A show for the talents of the past winter. Closed competition.', '2026-01-01', '2026-09-15', '$1,000', 'closed');

INSERT IGNORE INTO competition_entries (competition_id, user_id, post_id) VALUES
(1, 2, NULL),
(1, 4, NULL);

