hola mundo
<?php
    $imgtemp= $path;
    
    $count=1;
    require_once './vendor/autoload.php';
    use Google\Cloud\Vision\V1\ImageAnnotatorClient;
    use Google\Cloud\Vision\V1\Feature\Type;
    use Google\Cloud\Vision\V1\Likelihood;
    
    
    /*$VisionClien = new VisionClient([
        'credentials' => json_decode(file_get_contents('app/Views/create.json'), true)
        
    ]);*/
    //$VisionClien = new VisionClient(key = AIzaSyCGVdCH9ipqf8h11vOU0SGNBxbgjeH_dpU);
    

     $imageAnnotatorClient = new ImageAnnotatorClient([
        'credentials' => 'app/Views/key.json'
    ]);
    $path = 'https://thumbs.dreamstime.com/b/ejemplo-determinado-del-vector-de-los-muebles-de-madera-31023412.jpg';
        /*$image = fopen($path, 'r');
        $foto = $imageAnnotatorClient->image($image, ['FACE_DETECTION', 'LABEL_DETECTION']);
        //$resultado = $imageAnnotatorClient->AnnotateImage(fopen($path,'r'),[Type::'FACE_DETECTION']);
        
        $client = new ImageAnnotatorClient(
            [
                'credentials' => 'app/Views/key.json'
            ]
        );*/
        // Annotate an image, detecting faces. 
        /*$annotation = $imageAnnotatorClient->annotateImage( fopen($path, 'r'), [Type::LABEL_DETECTION] );
        $response = $imageAnnotatorClient->labelDetection($imageContent);
        // Determine if the detected faces have headwear. 
        foreach ($annotation->getLabelAnnotations() as $faceAnnotation) { 	
            $likelihood = Likelihood::name($faceAnnotation->getName()); 
            echo "Likelihood of headwear: $likelihood" . PHP_EOL; }*/

        $imglabel = file_get_contents($path);
        $response =$imageAnnotatorClient->labelDetection($imglabel);
        $labels = $response->getLabelAnnotations();
        if($labels){
            echo('Label :'.PHP_EOL);
            foreach($labels as $label){
                print($label->getDescription().PHP_EOL);
                

            }
        }else{
            print('no label found'.PHP_EOL);
        }
        $imageAnnotator = new ImageAnnotatorClient(
            [
                'credentials' => 'app/Views/key.json'
            ]
        );
        ?>
       <?php
    
        
    # annotate the image
 
   
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
        print(PHP_EOL);} ?>
         <?php
        /*$response =$imageAnnotatorClient->objectLocalization($imglabel);
        $labels = $response->getLocalizedObjectAnnotations();
        if($labels){
            echo('Label :'.PHP_EOL);
            foreach($labels as $label){
                print($label->get().PHP_EOL);
                

            }
        }else{
            print('no label found'.PHP_EOL);
        }*/
       /* $annotation = $imageAnnotatorClient->annotateImage( fopen($path, 'r'), [Type::LABEL_DETECTION] );
        foreach ($annotation->getLabelAnnotations() as $faceAnnotation) { 	
            $likelihood = Likelihood::name($faceAnnotation->getName()); 
            echo "Likelihood of headwear: $likelihood" . PHP_EOL; }*/
/*
    try {
        $img_path="https://graffica.info/wp-content/uploads/2020/10/nuevo-logo-de-gmail-1200x675.jpg";
        $imaContent = file_get_contents($img_path);
        $respuesta = $imageAnnotatorClient->textDetection($imaContent);
        $text = $respuesta->getTextAnnotations();
        echo  $text[0]->getDescription();
        $imageAnnotatorClient->close();
    
    } catch(Exception $e) {
        echo $e->getMessage();
        
    }
    */
    
    /*try {

        $imageAnnotatorClient = new ImageAnnotatorClient([
            'credentials' => 'app/Views/key.json'
        ]);
        
        
        $path = 'https://tse1.mm.bing.net/th?id=OIP.QoklOYgkvZO5Umyv-siXrgHaE8&pid=Api&P=0';
        $image = file_get_contents($path);
        $response = $imageAnnotatorClient->landmarkDetection($image);
        $landmarks = $response->getLandmarkAnnotations();
        printf('%d landmark found:' . "<br>", count($landmarks));
        foreach ($landmarks as $landmark) {
        print($landmark->getDescription() . "<br>");
        
        }
        
        
        
        $imageAnnotatorClient->close();
        
        } catch(Exception $e) {
        
        echo $e->getMessage();
        
        }*/

        

        

?>