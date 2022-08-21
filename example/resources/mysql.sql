-- #!mysql
-- #{ init
CREATE TABLE chatcount (
	player VARCHAR(255) PRIMARY KEY,
	count INT,
	KEY (count)
);
-- #}
-- #{ add
-- #	:player string
INSERT INTO chatcount (player, count) VALUES (:player, 1)
ON DUPLICATE KEY UPDATE count = count + 1;
-- #}
-- #{ count
-- #	:player string
SELECT count FROM chatcount WHERE player = :player;
-- #}
-- #{ top
-- #	:limit int
SELECT player, count FROM chatcount ORDER BY count DESC LIMIT :limit;
-- #}
