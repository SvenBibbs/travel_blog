<?php
include_once 'Michelf/Markdown.inc.php';
use Michelf\Markdown;

$response = array();
$data = file_get_contents('../data/posts.json');
$posts = json_decode($data, true);
$count = sizeof($posts);

$diff = 0;

$method = $_SERVER['REQUEST_METHOD'];

$status = '';
$message = '';
$response = '';
$update = false;


if($method == 'GET'){
    if(isset($_GET['id'])){
        for ($i=0; $i < $count; $i++) {
            if($posts[$i]['id'] == $_GET['id']){
                $post = $posts[$i];
                $response = (object) [
                    'id' => $post['id'],
                    'title' => $post['title'],
                    'timestamp' => $post['timestamp'],
                    'text' => $post['text'],
                    'htmlText' => Markdown::defaultTransform($post['text']),
                    'comments' => $post['comments']
                ];
            }
       }
    } else {
        $totalCount = $count;
        $result = [];

        if(isset($_GET['page'])){
            $count = $count - ($_GET['count'] * ($_GET['page'] - 1));
        }

        if(isset($_GET['count'])){
            if($count > $_GET['count']) {
                $diff = $count - $_GET['count'];
            }
        }

        for ($i = $count-1; $i >= $diff; $i--) {
            $result[] = array(
                'id' => $posts[$i]['id'],
                'title' => $posts[$i]['title'],
                'timestamp' => $posts[$i]['timestamp'],
                'htmlText' => Markdown::defaultTransform($posts[$i]['text']), // trim text for preview
                'commentCount' => sizeOf($posts[$i]['comments'])
            );
        }

        $response = (object) [
            'totalCount' => $totalCount,
            'result' => $result
        ];
    }
}
if($method == 'POST'){
    if(isset($_POST['post'])) {
        $sendPost = json_decode($_POST['post'], true);
        $posts[] = array(
            'id' => uniqid(), // create new random guid
            'title' => $sendPost['title'],
            'timestamp' => time(), // set current time as timestamp (unix-timestamp)
            'text' => $sendPost['text'],
            'comments' => [] // empty because new post
        );
        $update = true;
        $response = 'Create successfull';
    } else {
        $response = 'no post found to save';
        $status = 500; // oder sowas
    }
}

if($method == 'PUT'){
    parse_str(file_get_contents('php://input'), $_PUT);
    if(isset($_PUT['post'])){
        $sendPost = json_decode($_PUT['post'],true);
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
                    break;
                }
            }
        }

        // if a post with identical id is found
        if($foundPost != null) {
            // update post
            // set changed title and text
            $posts[$foundIndex]['title'] = $sendPost['title'];
            $posts[$foundIndex]['text'] = $sendPost['text'];
            
            $update = true;
            $response = 'Update successfull';
        } else {
            $response = 'Post not found';
        }
    } else {
        $response = 'Wrong format or no post data';
    }
}
if($method == 'DELETE'){
    // Get id from url
    parse_str(file_get_contents('php://input'), $_DELETE);
    if(isset($_DELETE['id'])) {
        $foundIndex = null;

        for ($i = 0; $i < $count; $i++) {
            // iterate over all posts and compare id's
            if($_DELETE['id'] == $posts[$i]['id']) {
                // save index for later use
                $foundIndex = $i;
                array_splice($posts, $i, 1);
                $update = true;
                $response = 'Delete successfull';
                break;
            }
        }

        if($foundIndex == null) {
            $response = 'ID not found';
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
// send response
echo json_encode($response);
?>