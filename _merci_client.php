<?php
require '_settings.php'; // Inclut les paramètres comme dans index.php (base de données, Redis, etc.)
$title = "Merci | Aquavelo"; // Titre de la page

// Démarrer la session pour récupérer les données si nécessaire
session_start();

// Récupérer le message ou les données depuis la session (optionnel, selon ton traitement)
$message = isset($_SESSION['message']) ? $_SESSION['message'] : "Merci pour votre inscription ! Nous vous contacterons bientôt.";

// Nettoyer la session après usage
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> | Aquavelo</title>
    <!-- Inclusion des styles et scripts de index.php -->
    <link rel="stylesheet" type="text/css" href="/css/animate.css">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="icon" href="images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Courgette&display=swap" rel="stylesheet">
    <style type="text/css">
        body, td, th { font-family: 'Open Sans', sans-serif; }
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
        }
        #boxedWrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .message-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .message-container {
            max-width: 600px;
            width: 100%;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #47c3e6;
        }
        p {
            font-size: 18px;
            color: #333;
        }
    </style>
    <script src="/js/modernizr.custom.js"></script>
</head>
<body class="withAnimation">
    <div id="boxedWrapper">
        <!-- Barre de navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="http://aquavelo.com/"><img src="/images/content/logo.png" alt="Aquabiking collectif"></a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li<?php if ($page == 'home') echo ' class="active"'; ?>> <a href="http://aquavelo.com/">Accueil</a> </li>
                        <li class="dropdown<?php if ($page == 'aquabiking') echo ' active'; ?>"> <a href="/aquabiking" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Aquabiking</a>
                            <ul class="dropdown-menu">
                                <li><a href="/aquabiking">Le vélo dans l'eau</a></li>
                                <li><a href="/aquabiking#bienfaits">Les bienfaits</a></li>
                                <li><a href="/aquabiking#questions">Vos questions</a></li>
                            </ul>
                        </li>
                        <li class="dropdown<?php if ($page == 'centres') echo ' active'; ?>"> <a href="/centres" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Centres</a>
                            <ul class="dropdown-menu
