-- PostgreSQL Schema for Supabase (Converted from MySQL)

-- Table structure for table "admin"
CREATE TABLE admin (
  admin_id SERIAL PRIMARY KEY,
  email VARCHAR(50) NOT NULL,
  password VARCHAR(100) NOT NULL,
  contact_no INT NOT NULL
);

INSERT INTO admin (admin_id, email, password, contact_no) VALUES
(29, 'admin@admin.com', '$2y$10$U/bvSZB1P9zBYgrouy1mWei3FlpBqaW8EoZvz/uLpsA431AW4DaeW', 12345);

-- Table structure for table "buyer_details"
CREATE TABLE buyer_details (
  Buyer_id SERIAL PRIMARY KEY,
  photo VARCHAR(255) NOT NULL,
  full_name CHARACTER(50) NOT NULL,
  email VARCHAR(50) NOT NULL,
  contact_no BIGINT NOT NULL, -- Changed to BIGINT for safety
  password VARCHAR(100) NOT NULL,
  created_on TIMESTAMP NOT NULL,
  address VARCHAR(200) NOT NULL,
  pin_code INT NOT NULL,
  state VARCHAR(255) NOT NULL,
  otp INT NOT NULL
);

INSERT INTO buyer_details (Buyer_id, photo, full_name, email, contact_no, password, created_on, address, pin_code, state, otp) VALUES
(17, 'image.jpg', 'shlok patel', 'shlok@gmail.com', 2147483647, '$2y$10$aGvqss9lICuR4O2KHOSUs.VSZQLFbHLArGKDxvGZnIrFFKqW2yBBa', '2024-02-17 18:07:55', '50,Sachin Park Society Jodhpur Gam Road Satellite Ahmedabad', 380015, 'Gujarat', 0);

-- Table structure for table "cart_details"
CREATE TABLE cart_details (
  cart_id SERIAL PRIMARY KEY,
  product_id INT NOT NULL,
  buyer_id INT NOT NULL,
  quantity INT NOT NULL
);

-- Table structure for table "contact_details"
CREATE TABLE contact_details (
  contact_id SERIAL PRIMARY KEY,
  buyer_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  message VARCHAR(255) NOT NULL,
  status BIGINT NOT NULL,
  created_on TIMESTAMP NOT NULL
);

-- Table structure for table "coupon_details"
CREATE TABLE coupon_details (
  coupon_id SERIAL PRIMARY KEY,
  coupon_code VARCHAR(255) NOT NULL,
  discount_percentage INT NOT NULL
);

-- Table structure for table "newsletter"
CREATE TABLE newsletter (
  id SERIAL PRIMARY KEY,
  email VARCHAR(255) NOT NULL
);

-- Table structure for table "order_details"
CREATE TABLE order_details (
  order_id SERIAL PRIMARY KEY,
  tracking_no VARCHAR(255) NOT NULL,
  order_no VARCHAR(50) NOT NULL,
  product_id VARCHAR(50) NOT NULL,
  buyer_id INT NOT NULL,
  seller_id INT NOT NULL,
  payment INT NOT NULL,
  price INT NOT NULL,
  quantity INT NOT NULL,
  status INT NOT NULL,
  order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table structure for table "product_details"
CREATE TABLE product_details (
  product_id SERIAL PRIMARY KEY,
  seller_id INT NOT NULL,
  name VARCHAR(50) NOT NULL,
  description VARCHAR(255) NOT NULL,
  mrp INT NOT NULL,
  price INT NOT NULL,
  quantity INT NOT NULL,
  photo VARCHAR(50) NOT NULL,
  photo2 VARCHAR(255) NOT NULL,
  photo3 VARCHAR(255) NOT NULL
);

-- Table structure for table "seller_details"
CREATE TABLE seller_details (
  seller_id SERIAL PRIMARY KEY,
  first_name CHARACTER(50) NOT NULL,
  last_name CHARACTER(50) NOT NULL,
  photo VARCHAR(255) NOT NULL,
  email VARCHAR(50) NOT NULL,
  password VARCHAR(100) NOT NULL,
  contact_no BIGINT NOT NULL,
  government_id VARCHAR(100) NOT NULL,
  gst_no BIGINT NOT NULL,
  status INT NOT NULL,
  created_on TIMESTAMP NOT NULL,
  otp INT NOT NULL,
  verify INT NOT NULL
);

-- Table structure for table "shop_details"
CREATE TABLE shop_details (
  shop_id SERIAL PRIMARY KEY,
  seller_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  address VARCHAR(255) NOT NULL,
  city VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  contact_no BIGINT NOT NULL,
  time VARCHAR(255) NOT NULL,
  contact_person VARCHAR(255) NOT NULL,
  location VARCHAR(255) NOT NULL,
  photo VARCHAR(255) NOT NULL
);

-- Table structure for table "testimonials"
CREATE TABLE testimonials (
  testimonial_id SERIAL PRIMARY KEY,
  buyer_id INT NOT NULL,
  feedback VARCHAR(150) NOT NULL
);
