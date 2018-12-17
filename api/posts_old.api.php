<?php
include_once 'Michelf/Markdown.inc.php';

use Michelf\Markdown;

$response = array();
$data = file_get_contents('../data/posts.json');
$posts = json_decode($data, true);
$count = sizeof($posts);

if(isset($_GET['count'])){
    if($count < $_GET['count']){
        $diff = 0;
    } else {
        $diff = $count - $_GET['count'];
    }
    for ($i = $count-1; $i >= $diff; $i--) {
        $response[] = array(
            'id' => $posts[$i]['id'],
            'title' => $posts[$i]['title'],
            'timestamp' => $posts[$i]['timestamp'],
            'htmlText' => Markdown::defaultTransform($posts[$i]['text']),
            'commentCount' => sizeOf($posts[$i]['comments'])
        );
    }
} else if(isset($_GET["id"])) {
    for ($i=0; $i < $count; $i++) {
        echo json_encode($posts);
        $response[] = array(
            'id' => $posts[$i]['id'],
            'title' => $posts[$i]['title'],
            'timestamp' => $posts[$i]['timestamp']
        );
        if($posts[$i]['id'] == $_GET['id']){
            if(isset($_GET["action"]) && $_GET["action"] == 'delete') {
                // remove post from array
                unset($posts[$i]);      
            } else {
                $response = (object) [
                    'id' => $post[$i]['id'],
                    'title' => $post[$i]['title'],
                    'timestamp' => $post[$i]['timestamp'],
                    'text' => $post[$i]['text'],
                    'htmlText' => Markdown::defaultTransform($post[$i]['text']),
                    'comments' => $post[$i]['comments']
                ];
                break;
            }
            
        }
    }
    if(isset($_GET["action"]) && $_GET["action"] == 'delete') {
        // write to file
        $fp = fopen('../data/posts.json', 'w');
        fwrite($fp, json_encode($posts));
        fclose($fp);
    }
} else if(isset($_POST['post'])) {
    // update/create post
    $sendPost = json_decode($_POST['post'], true);
    $foundPost = null;
    $foundIndex = null;
    
    // check if send post has id 
    if(array_key_exists('id', $sendPost)) {
        // find index of post to update
        for ($i = 0; $i < $count; $i++) {
            // iterate over all posts and compare id's
            if($sendPost['id'] == $posts[$i]['id']) {
                // save found post in variable
                $foundPost = $posts[$i];
                // save index for later use
                $foundIndex = $i;
                break;/*  */
            }
        }
    }

    // if a post with identical id is found
    if($foundPost != null) {
        // update post
        // set changed title and text
        $posts[$foundIndex]['title'] = $sendPost['title'];
        $posts[$foundIndex]['text'] = $sendPost['text'];
    } else {
        // create post
        $posts[] = array(
            'id' => uniqid(), // create new random guid
            'title' => $sendPost['title'],
            'timestamp' => time(), // set current time as timestamp (unix-timestamp)
            'text' => $sendPost['text'],
            'comments' => [] // empty because new post
        );
    }
    
    // write to file
    $fp = fopen('../data/posts.json', 'w');
    fwrite($fp, json_encode($posts));
    fclose($fp);
    
    foreach ($posts as $post) {
        $response[] = array(
            'id' => $post['id'],
            'title' => $post['title'],
            'timestamp' => $post['timestamp']
        );
    }

} else {
    foreach ($posts as $post) {
        $response[] = array(
            'id' => $post['id'],
            'title' => $post['title'],
            'timestamp' => $post['timestamp']
        );
    }
}

echo json_encode($response);
?>