<?php
include ("connection.php");

/**
 * Seeding Script for Agricart (PostgreSQL/MySQL Compatible)
 * Populates: 25 Sellers, 25 Shops, and 100+ Products.
 */

set_time_limit(0); // Ensure script doesn't timeout

// Sample Data Arrays
$first_names = ["Arjun", "Rajesh", "Suresh", "Manthan", "Shlok", "Prakash", "Amit", "Vikram", "Gopal", "Harish", "Deepak", "Sunil", "Anil", "Kartik", "Rahul", "Naveen", "Manoj", "Sanjay", "Vinay", "Santosh", "Ashok", "Bharat", "Dilip", "Ishwar", "Jagdish"];
$last_names = ["Patel", "Shah", "Mehta", "Sharma", "Verma", "Singh", "Yadav", "Gupta", "Desai", "Joshi", "Trivedi", "Pandya", "Kulkarni", "Chauhan", "Rathod", "Solanki", "Gohil", "Prajapati", "Mistry", "Thakkar", "Malhotra", "Kapoor", "Bhatt", "Dave", "Vyas"];
$cities = ["Ahmedabad", "Surat", "Vadodara", "Rajkot", "Bhavnagar", "Jamnagar", "Junagadh", "Gandhinagar", "Anand", "Nadiad", "Morbi", "Mehsana", "Surendranagar", "Bharuch", "Valsad", "Vapi", "Navsari", "Veraval", "Porbandar", "Godhra", "Palanpur", "Botad", "Jetpur", "Gondal", "Deesa"];
$products_pool = [
    ["name" => "Organic Wheat", "desc" => "Pure organic wheat from the fields of Saurashtra.", "mrp" => 60, "price" => 45],
    ["name" => "Basmati Rice", "desc" => "Long-grain aromatic basmati rice.", "mrp" => 120, "price" => 95],
    ["name" => "Natural Fertilizer", "desc" => "Chemical-free nitrogen-rich fertilizer.", "mrp" => 500, "price" => 350],
    ["name" => "Hybrid Seeds", "desc" => "High-yield seeds for cotton and groundnut.", "mrp" => 250, "price" => 180],
    ["name" => "Agriculture Tool Kit", "desc" => "Complete set of manual farming tools.", "mrp" => 1500, "price" => 1200],
    ["name" => "Neem Oil Extract", "desc" => "Organic pesticide for all crops.", "mrp" => 300, "price" => 220],
    ["name" => "Dried Chilies", "desc" => "Extra spicy Guntur variety dried chilies.", "mrp" => 200, "price" => 160],
    ["name" => "Raw Peanuts", "desc" => "Farm-fresh high-protein peanuts.", "mrp" => 150, "price" => 110],
    ["name" => "Cow Dung Manure", "desc" => "Traditional organic compost for soil health.", "mrp" => 400, "price" => 280],
    ["name" => "Soybean Seeds", "desc" => "Premium quality soybean seeds for monsoon.", "mrp" => 350, "price" => 270],
];

echo "Starting optimized seeding for 25 Sellers...\n";

try {
    $common_password = password_hash("seller123", PASSWORD_DEFAULT);
    $created_on = date('Y-m-d H:i:s');

    // Begin a single transaction for all operations
    $conn->beginTransaction();

    for ($i = 0; $i < 5; $i++) {
        $fname = $first_names[$i % count($first_names)];
        $lname = $last_names[$i % count($last_names)];
        $email = strtolower($fname . "." . $lname . $i . "@example.com");
        $city = $cities[$i % count($cities)];
        $contact = 9100000000 + $i;

        // 1. Insert Seller
        $stmt_seller = $conn->prepare("INSERT INTO seller_details (first_name, last_name, photo, email, password, contact_no, government_id, gst_no, status, created_on, otp, verify) 
                                       VALUES (:fname, :lname, 'seller_icon.png', :email, :pass, :contact, 'dummy_id.pdf', 0, 0, :date, 0, 1)");
        $stmt_seller->execute([
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'pass' => $common_password,
            'contact' => $contact,
            'date' => $created_on
        ]);
        
        $seller_id = $conn->lastInsertId();

        // 2. Insert Shop
        $stmt_shop = $conn->prepare("INSERT INTO shop_details (seller_id, name, address, city, email, contact_no, time, contact_person, location, photo) 
                                     VALUES (:sid, :sname, :addr, :city, :semail, :scontact, '9 AM - 6 PM', :cperson, 'https://maps.google.com', 'shop_placeholder.jpg')");
        $stmt_shop->execute([
            'sid' => $seller_id,
            'sname' => $fname . "'s " . "Agriculture Store",
            'addr' => "Sector " . rand(1, 20) . ", Main Market, $city",
            'city' => $city,
            'semail' => "shop." . $email,
            'scontact' => $contact,
            'cperson' => $fname . " " . $lname
        ]);

        // 3. Insert 4 Products per Seller
        for ($p = 0; $p < 4; $p++) {
            $prod = $products_pool[rand(0, count($products_pool) - 1)];
            $stmt_prod = $conn->prepare("INSERT INTO product_details (seller_id, name, description, mrp, price, quantity, photo, photo2, photo3) 
                                         VALUES (:sid, :pname, :pdesc, :mrp, :price, :qty, 'product_seed.jpg', '', '')");
            $stmt_prod->execute([
                'sid' => $seller_id,
                'pname' => $prod['name'],
                'pdesc' => $prod['desc'],
                'mrp' => $prod['mrp'],
                'price' => $prod['price'],
                'qty' => rand(50, 500)
            ]);
        }

        echo "Inserted Seller $i: $fname $lname ($email) with 4 products.\n";
    }

    $conn->commit();
    echo "\nSeeding Complete! 25 Sellers, 25 Shops, and 100 Products added.\n";

} catch (PDOException $e) {
    die("Seeding failed: " . $e->getMessage());
}
?>
