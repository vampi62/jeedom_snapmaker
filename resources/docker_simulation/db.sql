#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


#------------------------------------------------------------
# Table: apikey
#------------------------------------------------------------

CREATE TABLE apikey(
        id_apikey Int  Auto_increment  NOT NULL ,
        keyg       Varchar (50) NOT NULL ,
        valide    tinyint(1) NOT NULL,
        connect    tinyint(1) NOT NULL,
        time    bigint(20) NOT NULL
	,CONSTRAINT apikey_PK PRIMARY KEY (id_apikey)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: log
#------------------------------------------------------------

CREATE TABLE log(
        id_log    Int  Auto_increment  NOT NULL ,
        date      Datetime NOT NULL ,
        status    Int NOT NULL ,
        id_apikey varchar(50) NULL
	,CONSTRAINT id_log_PK PRIMARY KEY (id_log)
)ENGINE=InnoDB;



CREATE DEFINER=`root`@`%` EVENT `test_api` ON SCHEDULE EVERY 5 MINUTE_SECOND STARTS '2023-02-28 21:31:24' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE apikey SET connect = 0, time = strftime('%s','now') WHERE connect = 1 AND time < (strftime('%s','now') + strftime('%s', 5))