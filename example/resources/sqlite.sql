-- #!sqlite
-- #{ init
CREATE TABLE chatcount (
	player TEXT PRIMARY KEY COLLATE NOCASE,
	count INTEGER
);
-- #&
CREATE INDEX chatcount_count ON chatcount (count);
-- #}
-- #{ add
-- #	:player string
INSERT OR IGNORE INTO chatcount (player, count) VALUES (:player, 0);
-- #&
UPDATE chatcount SET count = count + 1 WHERE player = :player;
-- #}
-- #{ count
-- #	:player string
SELECT count FROM chatcount WHERE player = :player;
-- #}
-- #{ top
-- #	:limit int
SELECT player, count FROM chatcount ORDER BY count DESC LIMIT :limit;
-- #}
