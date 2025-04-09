<?php
echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>Vide Grenier</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px; }
        h1, h2 { color: #333; }
        .item { background: white; margin-bottom: 10px; padding: 15px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .item h3 { margin: 0 0 10px 0; color: #2c3e50; }
        .item p { margin: 0 0 10px 0; color: #666; }
        .timestamp { color: #888; font-size: 0.9em; }
        form { margin-top: 30px; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        input[type='text'], textarea { padding: 8px; width: 100%; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        input[type='submit'] { padding: 8px 20px; background-color: #007BFF; color: white; border: none; border-radius: 4px; cursor: pointer; }
        input[type='submit']:hover { background-color: #0056b3; }
        .success { color: #28a745; padding: 10px; background: #e8f5e9; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; padding: 10px; background: #fbe9e7; border-radius: 4px; margin: 10px 0; }
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

    // Supprimer la table si elle existe
    $pdo->exec("DROP TABLE IF EXISTS items");

    // Créer la table avec la nouvelle structure
    $pdo->exec("CREATE TABLE items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Insérer des données de test
    $pdo->exec("INSERT INTO items (name, description) VALUES 
        ('Vélo vintage', 'Vélo des années 80 en bon état'),
        ('Collection de livres', 'Série complète Harry Potter'),
        ('Machine à café', 'Cafetière italienne traditionnelle')");

    // Traitement du formulaire d'ajout
    if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['item_name'])) {
        $itemName = htmlspecialchars(trim($_POST['item_name']));
        $itemDesc = htmlspecialchars(trim($_POST['item_description'] ?? ''));
        $stmt = $pdo->prepare("INSERT INTO items (name, description) VALUES (:name, :description)");
        $stmt->execute(['name' => $itemName, 'description' => $itemDesc]);
        echo "<p class='success'>Item ajouté avec succès : $itemName</p>";
    }

    // Formulaire d'ajout
    echo "<form method='POST'>
        <h2>Ajouter un nouvel item</h2>
        <div>
            <label for='item_name'>Nom de l'item :</label>
            <input type='text' id='item_name' name='item_name' placeholder='Nom de l'item' required />
        </div>
        <div>
            <label for='item_description'>Description :</label>
            <textarea id='item_description' name='item_description' rows='3' placeholder='Description de l'item'></textarea>
        </div>
        <input type='submit' value='Ajouter' />
    </form>";

    // Affichage des données
    $stmt = $pdo->query("SELECT * FROM items ORDER BY created_at DESC");
    echo "<h2>Liste des items :</h2>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<div class='item'>";
        echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
        if (!empty($row['description'])) {
            echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        }
        echo "<span class='timestamp'>Créé le " . $row['created_at'] . "</span>";
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
