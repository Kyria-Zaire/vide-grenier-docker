<?php
echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>Vide Grenier</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px; }
        h1, h2 { color: #333; }
        .item { background: white; margin-bottom: 10px; padding: 15px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: relative; }
        .item h3 { margin: 0 0 10px 0; color: #2c3e50; }
        .item p { margin: 0 0 10px 0; color: #666; }
        .timestamp { color: #888; font-size: 0.9em; }
        .price { color: #27ae60; font-weight: bold; font-size: 1.2em; }
        .category { background: #e8f5e9; color: #2e7d32; padding: 3px 8px; border-radius: 3px; font-size: 0.9em; }
        form { margin-top: 30px; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        input[type='text'], input[type='number'], textarea, select { padding: 8px; width: 100%; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        input[type='submit'], .btn { padding: 8px 20px; background-color: #007BFF; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        input[type='submit']:hover, .btn:hover { background-color: #0056b3; }
        .btn-danger { background-color: #dc3545; }
        .btn-danger:hover { background-color: #c82333; }
        .success { color: #28a745; padding: 10px; background: #e8f5e9; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; padding: 10px; background: #fbe9e7; border-radius: 4px; margin: 10px 0; }
        .search-box { background: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; }
        .delete-btn { position: absolute; top: 10px; right: 10px; }
        .filters { display: flex; gap: 10px; align-items: center; margin-top: 10px; }
        .sort-btn { background: #f8f9fa; border: 1px solid #ddd; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .sort-btn.active { background: #007BFF; color: white; border-color: #0056b3; }
    </style>
</head>
<body>";

try {
    // Connexion à la base de données
    $dsn = "mysql:host=db;dbname=cubes5_db;charset=utf8mb4";
    $username = getenv('MYSQL_USER') ?: 'cubes5_user';
    $password = getenv('MYSQL_PASSWORD') ?: 'cubes5_password';
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Vide Grenier - Base de données connectée ✅</h1>";

    // Supprimer les tables si elles existent
    $pdo->exec("DROP TABLE IF EXISTS items");
    $pdo->exec("DROP TABLE IF EXISTS categories");

    // Créer la table des catégories
    $pdo->exec("CREATE TABLE categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL
    )");

    // Créer la table des items avec les nouvelles colonnes
    $pdo->exec("CREATE TABLE items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        category_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )");

    // Insérer les catégories de base
    $pdo->exec("INSERT INTO categories (name) VALUES 
        ('Électronique'),
        ('Vêtements'),
        ('Livres'),
        ('Meubles'),
        ('Sports'),
        ('Autres')");

    // Insérer des données de test
    $pdo->exec("INSERT INTO items (name, description, price, category_id) VALUES 
        ('Vélo vintage', 'Vélo des années 80 en bon état', 75.00, 5),
        ('Collection de livres', 'Série complète Harry Potter', 45.00, 3),
        ('Machine à café', 'Cafetière italienne traditionnelle', 15.00, 1)");

    // Traitement de la suppression
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        echo "<p class='success'>Item supprimé avec succès</p>";
    }

    // Traitement du formulaire d'ajout
    if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['item_name']) && isset($_POST['action']) && $_POST['action'] === 'add') {
        $itemName = htmlspecialchars(trim($_POST['item_name']));
        $itemDesc = htmlspecialchars(trim($_POST['item_description'] ?? ''));
        $itemPrice = floatval($_POST['item_price'] ?? 0);
        $categoryId = intval($_POST['category_id'] ?? 6); // 6 = Autres par défaut
        
        $stmt = $pdo->prepare("INSERT INTO items (name, description, price, category_id) VALUES (:name, :description, :price, :category_id)");
        $stmt->execute([
            'name' => $itemName,
            'description' => $itemDesc,
            'price' => $itemPrice,
            'category_id' => $categoryId
        ]);
        echo "<p class='success'>Item ajouté avec succès : $itemName</p>";
    }

    // Barre de recherche et filtres
    $sort = $_GET['sort'] ?? 'date';
    echo "<div class='search-box'>
        <form method='GET' style='margin: 0; padding: 0; flex-grow: 1;'>
            <input type='text' name='search' placeholder='Rechercher un item...' value='" . htmlspecialchars($_GET['search'] ?? '') . "'>
            <div class='filters'>
                <span>Trier par :</span>
                <a href='?" . http_build_query(array_merge($_GET, ['sort' => 'date'])) . "' class='sort-btn" . ($sort === 'date' ? ' active' : '') . "'>Date</a>
                <a href='?" . http_build_query(array_merge($_GET, ['sort' => 'price_asc'])) . "' class='sort-btn" . ($sort === 'price_asc' ? ' active' : '') . "'>Prix ↑</a>
                <a href='?" . http_build_query(array_merge($_GET, ['sort' => 'price_desc'])) . "' class='sort-btn" . ($sort === 'price_desc' ? ' active' : '') . "'>Prix ↓</a>
            </div>
        </form>
    </div>";

    // Formulaire d'ajout
    echo "<form method='POST'>
        <h2>Ajouter un nouvel item</h2>
        <input type='hidden' name='action' value='add'>
        <div>
            <label for='item_name'>Nom de l'item :</label>
            <input type='text' id='item_name' name='item_name' required />
        </div>
        <div>
            <label for='item_description'>Description :</label>
            <textarea id='item_description' name='item_description' rows='3'></textarea>
        </div>
        <div>
            <label for='item_price'>Prix (€) :</label>
            <input type='number' id='item_price' name='item_price' step='0.01' min='0' required />
        </div>
        <div>
            <label for='category_id'>Catégorie :</label>
            <select id='category_id' name='category_id' required>";
            
    // Liste des catégories
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
    foreach ($categories as $category) {
        echo "<option value='" . $category['id'] . "'>" . htmlspecialchars($category['name']) . "</option>";
    }
    
    echo "</select>
        </div>
        <input type='submit' value='Ajouter' />
    </form>";

    // Affichage des données avec filtre de recherche et tri
    $search = $_GET['search'] ?? '';
    $query = "SELECT i.*, c.name as category_name 
              FROM items i 
              LEFT JOIN categories c ON i.category_id = c.id";
    
    if ($search) {
        $query .= " WHERE i.name LIKE :search OR i.description LIKE :search";
    }

    // Ajout du tri
    $query .= " ORDER BY ";
    switch ($sort) {
        case 'price_asc':
            $query .= "i.price ASC";
            break;
        case 'price_desc':
            $query .= "i.price DESC";
            break;
        default:
            $query .= "i.created_at DESC";
    }

    $stmt = $pdo->prepare($query);
    if ($search) {
        $stmt->execute(['search' => "%$search%"]);
    } else {
        $stmt->execute();
    }

    echo "<h2>Liste des items :</h2>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<div class='item'>";
        echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
        if (!empty($row['description'])) {
            echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        }
        echo "<p class='price'>" . number_format($row['price'], 2, ',', ' ') . " €</p>";
        echo "<span class='category'>" . htmlspecialchars($row['category_name']) . "</span>";
        echo "<span class='timestamp'>Créé le " . $row['created_at'] . "</span>";
        
        // Bouton de suppression
        echo "<form method='POST' style='display:inline;' class='delete-btn'>
            <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
            <input type='submit' value='Supprimer' class='btn btn-danger' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet item ?\");'>
        </form>";
        
        echo "</div>";
    }

} catch (PDOException $e) {
    // Gestion d'erreur propre
    echo "<div class='error'>";
    echo "<h1>Erreur de connexion</h1>";
    echo "<p>Message : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
    file_put_contents('error_log.txt', date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, FILE_APPEND);
}

echo "</body></html>";
?>
