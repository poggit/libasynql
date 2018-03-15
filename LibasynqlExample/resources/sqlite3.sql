-- #dialect sqlite #
-- #{ poggit.libasynql.example #
-- #{   createTable #
-- #{     players #
CREATE TABLE $tbl$players (
	name         VARCHAR(100),
	registerTime TIMESTAMP,
	loginTime    TIMESTAMP,
	lastIp       VARCHAR(100)
);
-- #}     #
-- #}   #
-- #} #
