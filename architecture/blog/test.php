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