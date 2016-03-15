INSERT INTO `Accommodation` (accom_id, name, clean_name, email, telnr, authtoken, addr, plz, city) VALUES
  (1, 'Demo Unterkunft', 'demo-unterkunft', 'demo@inventeerio.de', NULL, 'demo-token',
   NULL, NULL, NULL);
--  (2, 'NUK Moabit', 'test-moabit', 'nonofyour@business.de', '0123/456789', "erde-erde-erde"),
--  (3, 'NUK Mitte', 'test-mitte', 'nonofyour@business.de', '0123/456789', "tanz-koch-kurs"),
--  (4, 'NUK Steglitz', 'test-steglitz', 'nonofyour@business.de', '0123/456789', "feuer-wasser-luft");

INSERT INTO `Request` (req_id, accom_id, item_id, expiration, description) VALUES
  (12, 1, 17, NULL, NULL),
  (15, 1, 41, NULL, NULL),
  (16, 1, 50, NULL, NULL),
  (17, 1, 95, NULL, NULL),
  (18, 1, 2, NULL, NULL),
  (19, 1, 44, NULL, NULL),
  (20, 1, 101, NULL, NULL),
  (21, 1, 102, NULL, NULL),
  (22, 1, 103, NULL, NULL),
  (23, 1, 104, NULL, NULL);