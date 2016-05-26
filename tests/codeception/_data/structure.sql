SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `testdb`
--

--
-- Table structure for table `activeuser_profiles`
--

DROP TABLE IF EXISTS `activeuser_profiles`;
CREATE TABLE `activeuser_profiles` (
    `user_id` int(11) NOT NULL,
    `site` varchar(255) NOT NULL DEFAULT '',
    `location` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `activeuser_users`
--

DROP TABLE IF EXISTS `activeuser_users`;
CREATE TABLE `activeuser_users` (
    `id` int(11) NOT NULL,
    `status` int(11) NOT NULL DEFAULT '0',
    `email` varchar(200) NOT NULL DEFAULT '',
    `pass_hash` varchar(60) NOT NULL DEFAULT '',
    `name` varchar(200) NOT NULL DEFAULT '',
    `gender` int(11) NOT NULL DEFAULT 0,
    `birth` date DEFAULT NULL,
    `avatar` varchar(45) NOT NULL DEFAULT '',
    `access_token` varchar(40) DEFAULT NULL,
    `auth_key` varchar(40) DEFAULT NULL,
    `token` varchar(40) DEFAULT NULL,
    `token_created_at` INT NOT NULL DEFAULT 0,
    `registered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activeuser_profiles`
--
ALTER TABLE `activeuser_profiles`
    ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `activeuser_users`
--
ALTER TABLE `activeuser_users`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `unique_email` (`email`),
    ADD UNIQUE KEY `unique_access_token` (`access_token`),
    ADD UNIQUE KEY `unique_auth_key` (`auth_key`),
    ADD UNIQUE KEY `unique_token` (`token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activeuser_profiles`
--
ALTER TABLE `activeuser_profiles`
    MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `activeuser_users`
--
ALTER TABLE `activeuser_users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `activeuser_profiles`
--
ALTER TABLE `activeuser_profiles`
    ADD CONSTRAINT `fk__profiles__users` FOREIGN KEY (`user_id`) REFERENCES `activeuser_users` (`id`) ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
