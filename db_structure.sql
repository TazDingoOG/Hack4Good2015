CREATE TABLE IF NOT EXISTS `Accommodation` (
  `id`         INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  `name`       TEXT    NOT NULL UNIQUE,
  `clean_name` TEXT    NOT NULL UNIQUE,
  `email`      TEXT,
  `telnr`      TEXT,
  `authtoken`  TEXT UNIQUE
);
CREATE TABLE IF NOT EXISTS `Item` (
  `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  `name`  TEXT    NOT NULL UNIQUE,
  `image` TEXT
);
CREATE TABLE IF NOT EXISTS `Request` (
  `id`               INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  `accommodation_id` INTEGER NOT NULL,
  `item_id`          INTEGER NOT NULL,
  `expiration`       TEXT,
  FOREIGN KEY (`accommodation_id`) REFERENCES Accommodation (id),
  FOREIGN KEY (`item_id`) REFERENCES Item (id)
);
