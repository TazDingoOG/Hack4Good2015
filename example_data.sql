INSERT INTO `Accommodation` (accom_id, name, clean_name, email, telnr, authtoken, addr, plz, city) VALUES
  (1, 'NUK Brennpunkt', 'test-brennpunkt', 'nonofyour@business.de', '0123/456789', "besser-wisser-martin", "Klosterstraße 62", "10179", "Berlin");
--  (2, 'NUK Moabit', 'test-moabit', 'nonofyour@business.de', '0123/456789', "erde-erde-erde"),
--  (3, 'NUK Mitte', 'test-mitte', 'nonofyour@business.de', '0123/456789', "tanz-koch-kurs"),
--  (4, 'NUK Steglitz', 'test-steglitz', 'nonofyour@business.de', '0123/456789', "feuer-wasser-luft");

INSERT INTO `Request` (req_id, accom_id, item_id, expiration) VALUES
  (1, 1, 1, NULL),
  (2, 1, 13, NULL),
  (3, 1, 16, NULL),
  (4, 1, 11, NULL),
  (5, 1, 22, NULL),
  (6, 1, 35, NULL),
  (7, 1, 47, NULL),
  (8, 1, 22, NULL),
  (9, 1, 11, NULL),
  (10, 1, 12, NULL),
  (11, 1, 31, NULL),
  (12, 1, 17, NULL),
  (13, 1, 19, NULL),
  (14, 1, 27, NULL),
  (15, 1, 41, NULL),
  (17, 1, 95, NULL),
  (16, 1, 50, NULL);
