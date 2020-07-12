CREATE TABLE Account (
  username varchar(255) NOT NULL UNIQUE,
  psswrd varchar(255) NOT NULL,
  aid varchar(10) DEFAULT NULL UNIQUE,
  email varchar(255) NOT NULL,
  b_date date NOT NULL,
  name varchar(255) NOT NULL,
  lid int(255) NOT NULL UNIQUE AUTO_INCREMENT,
  PRIMARY KEY (lid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE locations (
    city varchar(255),
    user_lid int(255) NOT NULL,
    FOREIGN KEY (user_lid) REFERENCES Account(lid)
    );

INSERT INTO Account ( username, psswrd, aid, email, b_date, name ) VALUES
('AdminTest', 'test987', '1', 'test12@test.testwork', '2020-7-11', 'yessir');


INSERT INTO Account ( username, psswrd, email, b_date, name ) VALUES
('shadowbarker', 'darkness1', 'okok@yesyes.cool', '2020-5-13', 'bil'),
('lightbringer', 'holyone0', 'tippytop@one.two', '2020-3-8', 'kyle'),
('avidgamer', 'funzi', 'gumgum@three.tree', '2020-1-3', 'cucumber');