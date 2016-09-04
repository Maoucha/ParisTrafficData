DROP DATABASE IF EXISTS ParisTraffic;

CREATE DATABASE ParisTraffic;

USE ParisTraffic;

--
-- Table Sensor
--
CREATE TABLE Sensor (
  id INT UNSIGNED AUTO_INCREMENT,
  shape_len FLOAT,
  id_arc FLOAT,
  id_arc_trafic INT UNSIGNED NOT NULL,
  latitude DOUBLE,
  longitude DOUBLE,
  PRIMARY KEY (id)
);

CREATE UNIQUE INDEX idx_id_arc_trafic on Sensor (id_arc_trafic);

--
-- Table Coordinates
--
CREATE TABLE Coordinates (
  id INT UNSIGNED AUTO_INCREMENT,
  sensor INT UNSIGNED NOT NULL,
  latitude DOUBLE,
  longitude DOUBLE,
  PRIMARY KEY (id),
  FOREIGN KEY (sensor) REFERENCES Sensor (id) ON DELETE NO ACTION
);

--
-- Table Extract
--
CREATE TABLE Extract (
  id INT UNSIGNED AUTO_INCREMENT,
  sensor INT UNSIGNED NOT NULL,
  horodate DATETIME NOT NULL,
  flow INT UNSIGNED,
  rate FLOAT UNSIGNED,
  PRIMARY KEY (id),
  FOREIGN KEY (sensor) REFERENCES Sensor (id_arc_trafic)
);