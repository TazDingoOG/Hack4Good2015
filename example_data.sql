INSERT INTO `Accommodation` (accom_id, name, clean_name, email, telnr, authtoken, addr, plz, city) VALUES
  (1, 'NUK Brennpunkt', 'test-brennpunkt', 'nonofyour@business.de', '0123/456789', 'besser-wisser-martin',
   'Klosterstraße 62', '10179', 'Berlin');
--  (2, 'NUK Moabit', 'test-moabit', 'nonofyour@business.de', '0123/456789', "erde-erde-erde"),
--  (3, 'NUK Mitte', 'test-mitte', 'nonofyour@business.de', '0123/456789', "tanz-koch-kurs"),
--  (4, 'NUK Steglitz', 'test-steglitz', 'nonofyour@business.de', '0123/456789', "feuer-wasser-luft");

INSERT INTO `Request` (req_id, accom_id, item_id, expiration) VALUES
  (12, 1, 17, NULL),
  (15, 1, 41, NULL),
  (16, 1, 50, NULL),
  (17, 1, 95, NULL),
  (18, 1, 2, NULL),
  (19, 1, 44, NULL),
  (20, 1, 101, NULL),
  (21, 1, 102, NULL),
  (22, 1, 103, NULL),
  (23, 1, 104, NULL);