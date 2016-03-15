CREATE TABLE IF NOT EXISTS `Accommodation` (
  `accom_id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  `name`       TEXT    NOT NULL UNIQUE,
  `clean_name` TEXT    NOT NULL UNIQUE,
  `email`      TEXT,
  `telnr`      TEXT,
  `authtoken`  TEXT UNIQUE,
  `addr`       TEXT,
  `plz`        TEXT,
  `city`       TEXT
);
CREATE TABLE IF NOT EXISTS `Item` (
  `item_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  `name`    TEXT    NOT NULL UNIQUE,
  `image`   TEXT
);
CREATE TABLE IF NOT EXISTS `Request` (
  `req_id`     INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  `accom_id`    INTEGER NOT NULL,
  `item_id`    INTEGER NOT NULL,
  `expiration` TEXT,
  `description` TEXT,
  FOREIGN KEY (`accom_id`) REFERENCES Accommodation (accom_id),
  FOREIGN KEY (`item_id`) REFERENCES Item (item_id)
);
