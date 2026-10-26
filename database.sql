-- ========================================================
-- Digital Memory Jar - Database Schema & Seed Data
-- Database: digital_memory_jar
-- ========================================================

CREATE DATABASE IF NOT EXISTS `digital_memory_jar` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `digital_memory_jar`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `memories`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `memories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `memory_date` DATE NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `mood` VARCHAR(30) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_memories_users` 
    FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Optional Seed Data for Demo & College Presentation
-- Default demo account:
-- Email: demo@memoryjar.com
-- Password: password123
-- --------------------------------------------------------
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'Aria Montgomery', 'demo@memoryjar.com', '$2y$10$snyivB8J44DUCTqAk1Hq1unClCjkbRbBm6F9ayYiKR2xvH05tyCLW');

INSERT IGNORE INTO `memories` (`id`, `user_id`, `title`, `description`, `memory_date`, `category`, `mood`) VALUES
(1, 1, 'Late Night Stargazing with Best Friends', 'We spread a cozy blanket on the hill behind college, brought warm hot chocolate in thermoses, and spotted three shooting stars while listening to acoustic music.', '2026-08-14', 'Friendship', '🥰 Loved'),
(2, 1, 'Won 1st Prize in Hackathon', 'After 24 hours of non-stop coding, coffee, and debugging, our team won 1st prize for our community web app! The moment they called our team name will stay with me forever.', '2026-09-02', 'Achievement', '🎉 Excited'),
(3, 1, 'Sunday Morning Lavender Pancakes', 'Mom and I made fluffy blueberry pancakes with lavender honey from scratch. The whole kitchen smelled like sweet vanilla and warm sunshine.', '2026-09-18', 'Family', '😊 Happy'),
(4, 1, 'Train Journey through the Misty Hills', 'Watched the morning fog roll over the tea gardens while the gentle monsoon breeze drifted through the train window. Pure tranquility.', '2026-10-05', 'Travel', '😌 Peaceful'),
(5, 1, 'Words to Remember on Cloudy Days', '"You don\'t have to have everything figured out right now. Just take one gentle step forward, breathe deeply, and trust your journey."', '2026-10-12', 'Positive Message', '🌈 Hopeful'),
(6, 1, 'Surprise Birthday Letter Collection', 'My classmates secretly collected handwritten notes and funny childhood pictures from all my friends into a handmade scrapbook. I cried happy tears!', '2026-10-15', 'Happy Moment', '💖 Grateful');
