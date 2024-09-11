<?php

// Une classe commence pas une majuscule par convention
class Comment
{
    // Ici le type est precisé, aussi le public qui permet la utilisation des variables autrepart
    public string $author;
    public string $frenchCreationDate;
    public string $comment;
}
// "instaciation" d'un object de la classe Comment
// instancier un nouvelle object par le `new`
$comment = new Comment();
// assigner l'obj a une var

// acceder aux proprietes d'un obj par le `->` et renseigne une valeur

$comment->frenchCreationDate = "15/09/2024 à 10h58";
$comment->author = "NotMe";
$comment->comment = "A comment";
// on peut par la suite creer une function que prends comme type de variable la classe
function test(Comment $comment)
{
    // `var_dump` permet de mieux voir des obj sur une page
    var_dump($comment);
}

test($comment);
