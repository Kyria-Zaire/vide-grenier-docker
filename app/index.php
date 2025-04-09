<?php
try {
    $dsn = "mysql:host=db;dbname=cubes5_db;charset=utf8mb4";
    $username = "cubes5_user";
    $password = "cubes5_password";

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Connexion à la base de données réussie!</h1>";
    
    // Créer la table si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Insérer des données de test si la table est vide
    $count = $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO items (name) VALUES 
            ('Item 1'),
            ('Item 2'),
            ('Item 3')");
    }

    // Afficher le contenu
    $stmt = $pdo->query("SELECT * FROM items ORDER BY created_at DESC");
    echo "<h2>Liste des items :</h2>";
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>" . htmlspecialchars($row['name']) . " (créé le " . $row['created_at'] . ")</li>";
    }
    echo "</ul>";

} catch (PDOException $e) {
    echo "<h1>Erreur de connexion</h1>";
    echo "<p>Message d'erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
