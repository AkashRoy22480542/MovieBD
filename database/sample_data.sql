-- MovieDB Sample Data
-- Insert sample data for testing

USE moviedb;

-- =====================================================
-- INSERT SAMPLE MOVIES
-- =====================================================
INSERT INTO movies (id, title, overview, release_date, runtime, vote_average, vote_count, poster_path, tmdb_id, is_trending) VALUES
(1, 'The Matrix', 'A computer hacker learns from mysterious rebels about the true nature of his reality and his role in the war against its controllers.', '1999-03-30', 136, 8.7, 15432, 'https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg', 603, TRUE),
(2, 'Inception', 'A thief who steals corporate secrets through dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.', '2010-07-15', 148, 8.8, 28450, 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg', 27205, TRUE),
(3, 'Interstellar', 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.', '2014-11-05', 169, 8.6, 21300, 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg', 157336, TRUE),
(4, 'The Dark Knight', 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.', '2008-07-16', 152, 9.0, 31850, 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg', 155, TRUE),
(5, 'Pulp Fiction', 'The lives of two mob hitmen, a boxer, a gangster and his wife, and a pair of diner bandits intertwine in four tales of violence and redemption.', '1994-09-10', 154, 8.9, 25600, 'https://image.tmdb.org/t/p/w500/d5iIlFn5s0ImszYzBPb8JPIfbXD.jpg', 680, TRUE),
(6, 'Fight Club', 'An insomniac office worker and a devil-may-care soapmaker form an underground fight club that evolves into something much, much more.', '1999-10-15', 139, 8.8, 24100, 'https://image.tmdb.org/t/p/w500/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg', 550, TRUE),
(7, 'Forrest Gump', 'The presidencies of Kennedy and Johnson, the Vietnam War, the Watergate scandal and other historical events unfold from the perspective of an Alabama man with an IQ of 75.', '1994-06-23', 142, 8.8, 22850, 'https://image.tmdb.org/t/p/w500/arw2vcBveWOVZr6pxd9XTd1TdQa.jpg', 13, TRUE),
(8, 'The Godfather', 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.', '1972-03-14', 175, 9.2, 18750, 'https://image.tmdb.org/t/p/w500/3bhkrj58Vtu7enYsRolD1fZdja1.jpg', 238, TRUE),
(9, 'Matrix Reloaded', 'Six months after the events depicted in The Matrix, Neo has proved to be a good omen for the free humans, as more and more humans are being freed from the matrix and brought to Zion.', '2003-05-07', 138, 7.2, 12450, 'https://image.tmdb.org/t/p/w500/9TGHDvWrqKBzwDxDodHYXEmOE6J.jpg', 604, FALSE),
(10, 'Matrix Revolutions', 'The human city of Zion defends itself against the massive invasion of the machines as Neo fights to end the war at another front while also opposing the rogue Agent Smith.', '2003-10-27', 129, 6.7, 10200, 'https://image.tmdb.org/t/p/w500/sKogjhfs5q3aEG8VvLQT2IOo4s4.jpg', 605, FALSE),
(11, 'Mad Max: Fury Road', 'An apocalyptic story set in the furthest reaches of our planet, in a stark desert landscape where humanity is broken.', '2015-05-13', 120, 7.6, 19850, 'https://image.tmdb.org/t/p/w500/hA2ple9q4qnwxp3hKVNhroipsir.jpg', 76341, FALSE),
(12, 'Avengers: Endgame', 'After the devastating events of Avengers: Infinity War, the universe is in ruins due to the efforts of the Mad Titan, Thanos.', '2019-04-24', 181, 8.3, 35600, 'https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg', 299534, FALSE),
(13, 'Spider-Man: No Way Home', 'Peter Parker is unmasked and no longer able to separate his normal life from the high-stakes of being a super-hero.', '2021-12-15', 148, 8.1, 28900, 'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg', 634649, FALSE),
(14, 'Joker', 'During the 1980s, a failed stand-up comedian is driven insane and turns to a life of crime and chaos in Gotham City.', '2019-10-01', 122, 8.2, 31200, 'https://image.tmdb.org/t/p/w500/udDclJoHjfjb8Ekgsd4FDteOkCU.jpg', 475557, FALSE),
(15, 'Avatar', 'In the 22nd century, a paraplegic Marine is dispatched to the moon Pandora on a unique mission.', '2009-12-10', 162, 7.6, 29450, 'https://image.tmdb.org/t/p/w500/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg', 19995, FALSE);


INSERT INTO admins (username, email, password, full_name, role, status) 
VALUES ('superadmin', 'superadmin@moviedb.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Administrator', 'super_admin', 'active')
ON DUPLICATE KEY UPDATE username = username;