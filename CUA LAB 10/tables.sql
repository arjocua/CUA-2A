-- 1. Pizzas Table
CREATE TABLE pizzas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- 2. Toppings Table
CREATE TABLE toppings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- 3. Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer VARCHAR(100) NOT NULL,
    pizza VARCHAR(50) NOT NULL,
    toppings TEXT,
    qty INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending'
);

-- 4. Insert Default Menu Items
INSERT INTO pizzas (name, price) VALUES ('Cheese', 150.00), ('Pepperoni', 180.00);
INSERT INTO toppings (name, price) VALUES ('Onions', 15.00), ('Extra Cheese', 30.00);