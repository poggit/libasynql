-- #! mysql
-- #{ poggit.libasynql.example
-- #    { players
-- #        { init
CREATE TABLE $tbl$players (
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
INSERT INTO $tbl$players (name, registerTime, loginTime, lastIp) VALUES (:name, :registerTime, :loginTime, :lastIp)
-- #        }
-- #    }
-- #}
