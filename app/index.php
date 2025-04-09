<?php
try {
    $dsn = "mysql:host=db;dbname=cubes5_db;charset=utf8mb4";
    $username = getenv('MYSQL_USER') ?: 'cubes5_user';
    $password = getenv('MYSQL_PASSWORD') ?: 'cubes5_password';

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Créer la table si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo '<!DOCTYPE html>
<html>
<head>
    <title>CUBES5 - Application</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #2c3e50; }
        .item { margin: 10px 0; padding: 10px; background: #f7f9fc; border-radius: 4px; }
        .timestamp { color: #7f8c8d; font-size: 0.9em; }
    </style>
</head>
<body>';

    echo "<h1>Connexion à la base de données réussie!</h1>";

    // Insérer des données de test si la table est vide
    $count = $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO items (name, description) VALUES 
            ('Projet CUBES5', 'Notre premier projet avec Docker'),
            ('Configuration Docker', 'Environnements DEV et PROD'),
            ('Base de données', 'MySQL avec persistance des données')");
    }

    // Afficher le contenu
    $stmt = $pdo->query("SELECT * FROM items ORDER BY created_at DESC");
    echo "<h2>Liste des items :</h2>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<div class="item">';
        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
        echo '<span class="timestamp">Créé le ' . $row['created_at'] . '</span>';
        echo '</div>';
    }

    echo '</body></html>';

} catch (PDOException $e) {
    echo "<h1>Erreur de connexion</h1>";
    echo "<p>Message d'erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
