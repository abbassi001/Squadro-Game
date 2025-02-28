<?php
// if (!isset($_ENV['sgbd'])) {
//     $_ENV['sgbd'] = 'pgsql'; 
// }
// if (!isset($_ENV['host'])) {
//     $_ENV['host'] = 'localhost'; 
// }
// if (!isset($_ENV['database'])) {
//     $_ENV['database'] = 'postgres'; // nom de la base de données
// }
// if (!isset($_ENV['user'])) {
//     $_ENV['user'] = 'postgres'; // utilisateur de la base de données
// }
// if (!isset($_ENV['password'])) {
//     $_ENV['password'] = '12345678'; // mot de passe de l'utilisateur
// }

if (!isset($_ENV['sgbd'])) {
    $_ENV['sgbd'] = 'pgsql'; 
}
if (!isset($_ENV['host'])) {
    $_ENV['host'] = 'localhost'; 
}
if (!isset($_ENV['database'])) {
    $_ENV['database'] = 'squadrodb'; // nom de la base de données
}
if (!isset($_ENV['user'])) {
    $_ENV['user'] = 'user2'; // utilisateur de la base de données
}
if (!isset($_ENV['password'])) {
    $_ENV['password'] = '12345678'; // mot de passe de l'utilisateur
}
?>

