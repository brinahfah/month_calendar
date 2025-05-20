<?php
//Informations de connexion Supabase
$host = 'aws-0-eu-west-3.pooler.supabase.com';//Remplacez par l'hôte de votre base de données
$port = '5432'; // Port par défaut pour PostgreSQL
$database = 'postgres';
$user ='postgres.mtamvxsqjgqieeciyhjh' ;  // Nom d'utilisateur
$password = 'brinahfah23';             // Mot de passe

try{
    //DSN (Data Source Name) pour PostgreSQL
    $dsn= "pgsql:host=$host;port=$port;dbname=$database";

    //Création d'une nouvelle instance de PDO
    $pdo = new PDO($dsn,$user,$password);

    //Définir le mode d'erreur de PDO sur Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connexion réussie à la base de données Supabase!";
}
catch (PDOException $e){
    //En cas d'erreu, affiche un message
    echo "Erreur de connexion : " . $e->getMessage();
}

//Charger les informations de configurations
  $config = require_once 'config.php';

try{
    //DSN pour PostgreSQL
    $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

    //Création de la connexion PDO
    $pdo = new PDO($dsn,$config['user'], $config['password']);

    //Configuration du mode d'erreur de PDO
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}
catch (PDOException $e){
    //Gestion des erreurs de connexion
    die("Erreur de connexion : " . $e->getMessage());
}


?>