## Source
https://openclassrooms.com/en/courses/4670706-adoptez-une-architecture-mvc-en-php/7847610-decouvrez-du-code-professionnel

## Notes
MVC = Modele, Vue, Controle

__Qu'est-ce qui fait qu'un code est "professionnel" ?  🤔__

Contrairement à ce qu'on pourrait croire, ce n'est pas parce qu'un code "marche" qu'il est "professionnel".

Voici quelques caractéristiques d'un code professionnel que l'on entend souvent :

**Il est modulaire** : généralement découpé en de nombreux fichiers, où chaque fichier a un rôle et un seul à la fois.

**Il est découplé** : les fichiers sont conçus pour fonctionner indépendamment les uns des autres.

**Il est documenté** : la documentation prend généralement la forme de commentaires spéciaux placés au-dessus des méthodes et classes publiques, pouvant être réutilisées dans d'autres projets (renseignez-vous sur la PHPdoc). On peut générer automatiquement une page web de documentation à partir de ces commentaires.

**Il est en anglais** : c'est la langue des développeurs et développeuses partout sur la planète. Les variables et les noms des fonctions sont en anglais et peuvent être compris par tous.

**Il est clair** : et pour ça, il respecte très souvent les normes de formatage. En PHP, la plupart des développeurs recommandent de suivre la PSR-12. Je vous conseille d'y jeter un œil, si vous êtes curieux. Dans tous les cas, on commencera à se l'imposer dès nos premières lignes de code dans ce cours !

### Docker
Create a `docker-compose.yml` file

Initiate a docker: `docker-compose up`
    It downloads the images and creates a linux, with a php and apache server
Or `docker-compose up -d `
    the `-d` will detach us from the initialized terminal

Close a docker: `docker-compose down`

Check container: `docker ps`

Check container: `docker ps -a`

Delete a container: `docker rm CONTAINER ID`

Check specific image: `docker image ls`

Delete specific image: `docker image rm IMAGE ID --force`

Le code sur la page `index.php` est très melange dans le sens ou on utilisent du HTML, SQL et PHP avec que des commentaires pour les separer.


I need a better way to organize my code, making it easier to add or remove new features.

---

Separer le code PHP du code HTML, celui qui correspond a la requete de données, donc qui fait partie du Modele

Code corrected:
```PHP
<?php
// Connexion à la base de données
try {
    //$bdd = new PDO('mysql:host=localhost;dbname=blog;charset=utf8', 'blog', 'password');
    $database = new PDO('mysql:host=db;dbname=demo;charset=utf8', 'user', 'pass');
} catch (Exception $e) {
    die('Error : ' . $e->getMessage());
}

// On récupère les 5 derniers billets
// Utiliser " pour la requete et ' pour l'interieur
$statement = $database->query(
    "SELECT id, titre, contenu, DATE_FORMAT(date_creation, '%d/%m/%Y à %Hh%imin%ss') AS date_creation_fr FROM billets ORDER BY date_creation DESC LIMIT 0, 5"
);
// we need to create a new variable, in order to organize our code, by creating a table
// we will loop and recover data using a fetch function, that returns a row
$posts = [];
while ($row = $statement->fetch()) {
    $post = [
        'title' => $row['titre'],
        'content' => $row['contenu'],
        'frenchCreationDate' => $row['date_creation_fr']
    ];
    $posts[] = $post;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Le blog de l'AVBN</title>
    <link href="style.css" rel="stylesheet" />
</head>

<body>
    <h1>Le super blog de l'AVBN !</h1>
    <p>Derniers billets du blog :</p>

    <?php
    // while ($donnees = $req->fetch()) {
    // replace the while by a foreach since we are working with a table
    foreach ($posts as $post) {
    ?>
    <div class="news">
        <h3>
            <?php echo htmlspecialchars($post['title']); ?>
            <em>le <?php echo $post['frenchCreationDate']; ?></em>
        </h3>
        <p>
            <?php
                // On affiche le contenu du billet
                echo nl2br(htmlspecialchars($post['content']));
                ?>
            <br />
            <em><a href="#">Commentaires</a></em>
        </p>
    </div>
    <?php
    } // Fin de la boucle des billets
    // remore this since we are no longer in a while loop
    // $req->closeCursor();
    ?>
</body>
```
The next idea is to separte both parts into two different files. Avoiding conflits, notice the ``required`
We will need to share the different variables between the pages.

- Control\
`index.php`
```PHP
<?php

// We connect to the database.
try {
    // $database = new PDO('mysql:host=localhost;dbname=blog;charset=utf8', 'blog', 'password');
    // This on is for another type of db, since I am using docker the names change
    $database = new PDO('mysql:host=db;dbname=demo;charset=utf8', 'user', 'pass');
} catch (Exception $e) {
    die('Error : ' . $e->getMessage());
}

// We retrieve the 5 last blog posts.
$statement = $database->query(
    "SELECT id, titre, contenu, DATE_FORMAT(date_creation, '%d/%m/%Y à %Hh%imin%ss') AS date_creation_fr FROM billets ORDER BY date_creation DESC LIMIT 0, 5"
);
$posts = [];
while (($row = $statement->fetch())) {
    $post = [
        'title' => $row['titre'],
        'french_creation_date' => $row['date_creation_fr'],
        'content' => $row['contenu'],
    ];

    $posts[] = $post;
}

require('templates/homepage.php');
```

- View\
`homepage.php`

```PHP
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Le blog de l'AVBN</title>
    <link href="style.css" rel="stylesheet" />
</head>

<body>
    <h1>Le super blog de l'AVBN !</h1>
    <p>Derniers billets du blog :</p>

    <?php
    foreach ($posts as $post) {
    ?>
    <div class="news">
        <h3>
            <?php echo htmlspecialchars($post['title']); ?>
            <em>le <?php echo $post['french_creation_date']; ?></em>
        </h3>
        <p>
            <?php
                // We display the post content.
                echo nl2br(htmlspecialchars($post['content']));
                ?>
            <br />
            <em><a href="#">Commentaires</a></em>
        </p>
    </div>
    <?php
    } // The end of the posts loop.
    ?>
</body>

</html>
```

We can go even further and create a function on that will be called by index

- Model\
`model.php`
```PHP
<?php
// This is part of the model since it deals with database querry 
function getPosts()
{
    // We connect to the database.
    try {
        // $database = new PDO('mysql:host=localhost;dbname=blog;charset=utf8', 'blog', 'password');
        $database = new PDO('mysql:host=db;dbname=demo;charset=utf8', 'user', 'pass');
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }

    // We retrieve the 5 last blog posts.
    $statement = $database->query(
        "SELECT id, titre, contenu, DATE_FORMAT(date_creation, '%d/%m/%Y à %Hh%imin%ss') AS date_creation_fr FROM billets ORDER BY date_creation DESC LIMIT 0, 5"
    );
    $posts = [];
    while (($row = $statement->fetch())) {
        $post = [
            'title' => $row['titre'],
            'french_creation_date' => $row['date_creation_fr'],
            'content' => $row['contenu'],
        ];

        $posts[] = $post;
    }

    return $posts;
}
```
The `index.php` will then be:

```PHP
<?php
require('src/model.php');

$posts = getPosts();

require('templates/homepage.php');
?>;
```
---
    https://openclassrooms.com/en/courses/4670706-adoptez-une-architecture-mvc-en-php/7847877-soignez-la-cosmetique-du-code

Notre code est maintenant découpé en 3 fichiers :

- Un pour le **traitement PHP** : il récupère les données de la base. On l'appelle le modèle.

- Un pour **l'affichage** : il affiche les informations dans une page HTML. On l'appelle la vue.

- Un pour **faire le lien entre les deux** : on l'appelle le contrôleur.

Explications: 

- **Modèle** : cette partie gère ce qu'on appelle la logique métier de votre site. Elle comprend notamment la gestion des données qui sont stockées, mais aussi tout le code qui prend des décisions autour de ces données. Son objectif est de fournir une interface d'action la plus simple possible au contrôleur. On y trouve donc entre autres des algorithmes complexes et des requêtes SQL.

- **Vue** : cette partie se concentre sur l'affichage. Elle ne fait presque aucun calcul et se contente de récupérer des variables pour savoir ce qu'elle doit afficher. On y trouve essentiellement du code HTML mais aussi quelques boucles et conditions PHP très simples, pour afficher par exemple une liste de messages.

- **Contrôleur** : cette partie gère les échanges avec l'utilisateur. C'est en quelque sorte l'intermédiaire entre l'utilisateur, le modèle et la vue. Le contrôleur va recevoir des requêtes de l'utilisateur. Pour chacune, il va demander au modèle d'effectuer certaines actions (lire des articles de blog depuis une base de données, supprimer un commentaire) et de lui renvoyer les résultats (la liste des articles, si la suppression est réussie). Puis il va adapter ce résultat et le donner à la vue. Enfin, il va renvoyer la nouvelle page HTML, générée par la vue, à l'utilisateur.

---

For the layout and homepage connection the following was used:

Ce code fait 3 choses :

- Il définit le **titre** de la page dans`$title`. Celui-ci sera intégré dans la balise < title>dans le template.

- Il définit le **contenu** de la page dans`$content`. Il sera intégré dans la balise< body>du template.
Comme ce contenu est un peu gros, on utilise une astuce pour le mettre dans une variable. On appelle la foncti`onob_start()`(ligne 3) qui "mémori   se" toute la sortie HTML qui suit. Puis, à la fin, on récupère le contenu généré avec`ob_get_clean()`(ligne 28) et on met le tout dans$content.

- Enfin, il **appelle le template** avec un `require`. Celui-ci va récupérer les variables$titleet$contentqu'on vient de créer... pour afficher la page !