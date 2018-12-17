<?php

$response = array();
$data = file_get_contents('../data/posts.json');
$posts = json_decode($data, true);
$count = sizeof($posts);

$method = $_SERVER['REQUEST_METHOD'];

$response = '';
$update = false;

if($method == 'POST'){
    if(isset($_POST['id'])){
        if(isset($_POST['text']) && isset($_POST['mail']) && isset($_POST['name'])){
            for ($i = 0; $i < $count; $i++) {
                // iterate over all posts and compare id's
                if($_POST['id'] == $posts[$i]['id']) {
                    // save index for later use
                    $foundIndex = $i;

                    $posts[$i]['comments'][] = array(
                        'name' => $_POST['name'],
                        'mail' => $_POST['mail'],
                        'text' => $_POST['text'],
                        'timestamp' => time()
                    );

                    $update = true;
                    $response = 'Commment created successfully';
                    break;
                }
            }
        } else {
            $response = "not enough comment data given";
        }
    } else {
        $response = 'No ID given';
    }
}

if($update == true) {
    $fp = fopen('../data/posts.json', 'w');
    fwrite($fp, json_encode($posts));
    fclose($fp);
}

echo json_encode($response);
?>