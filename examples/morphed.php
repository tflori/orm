<?php

use ORM\EntityManager;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/entities.php';

/** @var EntityManager $em */

// create an article and an image
$article = new Article([
    'author' => 'iRaS',
    'title' => 'The advantages of tflori/orm',
    'text' => 'it is pretty obvious why this orm is better than others',
]);
$article->save();
$image = new Image([
    'author' => 'iRaS',
    'url' => 'https://cdn.business2community.com/wp-content/uploads/2013/09/best-press-release-example.jpg',
    'caption' => 'This is just an example',
]);
$image->save();

// create some comments
$texts = [
    'Quod erat demonstrandum.',
    'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.',
    'Aenean commodo ligula eget dolor. Aenean massa.',
    'Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.',
    'Er hörte leise Schritte hinter sich. Das bedeutete nichts Gutes.',
    'Weit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte.',
    'Abgeschieden wohnen sie in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans.',
    '204 § ab dem Jahr 2034 Zahlen in 86 der Texte zur Pflicht werden.',
    'Dies ist ein Typoblindtext.',
    'Vogel Quax zwickt Johnys Pferd Bim.',
];
$authors = [
    'iRaS',
    'cat',
    's1mple',
];
foreach ([$article, $image] as $parent) {
    $count = mt_rand(2, 5);
    $em->useBulkInserts(Comment::class);
    for ($i = 0; $i < $count; $i++) {
        $comment = new Comment();
        $comment->text = $texts[array_rand($texts)];
        $comment->author = $authors[array_rand($authors)];
        $comment->setRelated('parent', $parent);
        $comment->save();
    }
    $em->finishBulkInserts(Comment::class);
}

printf('Article "%s" has %d comments:' . PHP_EOL, $article->title, count($article->comments));
foreach ($article->comments as $comment) {
    printf('  %s: %s' . PHP_EOL, $comment->author, $comment->text);
}

printf('Image "%s" has %d comments:'.  PHP_EOL, $image->caption, count($image->comments));
foreach ($image->comments as $comment) {
    printf('  %s: %s'. PHP_EOL, $comment->author, $comment->text);
}
