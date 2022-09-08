<?php

namespace App\Controllers;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\Likelihood;

class FaceController extends BaseController
{
    public function index()
    {
        return view('Facial/Index');
    }

    public function foto($url)
    {
        $url="https://tse1.mm.bing.net/th?id=OIP.QoklOYgkvZO5Umyv-siXrgHaE8&pid=Api&P=0";
        $imageAnnotatorClient = new ImageAnnotatorClient([
            'credentials' => 'app/Views/key.json'
        ]);
        $path = $url;
        $image = file_get_contents($path);
    //$image= fopen($_FILES['image']['tmp_name'],'r');
    $response = $imageAnnotator->objectLocalization($image);
    $objects = $response->getLocalizedObjectAnnotations();

    foreach ($objects as $object) {

        $name = $object->getName(); ?><br><br><hr> <?php
        $score = $object->getScore();
        $vertices = $object->getBoundingPoly()->getNormalizedVertices();
        echo $count++;
        printf('%s (confidence %f)):' . PHP_EOL, $name, $score);
        print('normalized bounding polygon vertices: ');
        foreach ($vertices as $vertex) {
            printf(' (%f, %f)', $vertex->getX(), $vertex->getY());
        }
        print(PHP_EOL);} 

    }
}
