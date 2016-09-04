DROP DATABASE IF EXISTS ParisTraffic;

CREATE DATABASE ParisTraffic;

USE ParisTraffic;

--
-- Table Compteur
--
CREATE TABLE Compteur (
  id INT UNSIGNED AUTO_INCREMENT,
  shape_len FLOAT,
  id_arc FLOAT,
  id_arc_tra INT,
  latitude DOUBLE,
  longitude DOUBLE,
  PRIMARY KEY (id)
);

--
-- Table ArcPoints
--
CREATE TABLE ArcPoints (
  id INT UNSIGNED AUTO_INCREMENT,
  compteur INT UNSIGNED NOT NULL,
  latitude DOUBLE,
  longitude DOUBLE,
  PRIMARY KEY (id),
  FOREIGN KEY (compteur) REFERENCES Compteur (id)
);

--
-- Table Comptage
--
CREATE TABLE Comptage (
  id INT UNSIGNED AUTO_INCREMENT,
  compteur INT UNSIGNED NOT NULL,
  horodate DATETIME NOT NULL,
  debit INT UNSIGNED,
  taux FLOAT UNSIGNED,
  PRIMARY KEY (id),
  FOREIGN KEY (compteur) REFERENCES Compteur (id)
);