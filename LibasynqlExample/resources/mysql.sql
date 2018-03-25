-- #! mysql
-- #{ poggit.libasynql.example
-- #    { players
-- #        { init
CREATE TABLE players (
	name         VARCHAR(100),
	registerTime TIMESTAMP,
	loginTime    TIMESTAMP,
	lastIp       VARCHAR(100)
);
-- #        }
-- #        { bump
-- #            :name string
-- #            :registerTime timestamp
-- #            :loginTime timestamp
-- #            :lastIp string
INSERT INTO players (name, registerTime, loginTime, lastIp)
VALUES (:name, :registerTime, :loginTime, :lastIp);
-- #        }
-- #        { findNameByIp
-- #            :ips list?string
SELECT name
FROM players
WHERE lastIp IN :ips
-- #        }
-- #    }
-- #}
