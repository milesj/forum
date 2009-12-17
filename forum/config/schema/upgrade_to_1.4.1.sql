--
-- Upgrading Cupcake to version 1.4.1 - Timezone support
--

ALTER TABLE `users`
	ADD `timezone` VARCHAR(4) NOT NULL DEFAULT '-8' AFTER locale;