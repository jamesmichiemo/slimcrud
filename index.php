<?php
  require 'vendor/autoload.php';

  // database
  $sqluser = "root";
  $sqlpass = "root";
  $dbh = new PDO('mysql:host=localhost;dbname=asl;port=8889', $sqluser, $sqlpass);

  $app = new \Slim\Slim();

  $app->get('/', function() use ($dbh, $app) {
    $sth= $dbh->prepare('SELECT id, message
          FROM lab1');
    $sth->execute();
    $results = $sth->fetchall(PDO::FETCH_ASSOC);

    foreach ($results as $result) {
      echo "<li>".$result['message']."<a href ='/editmessage/".$result['id']."'>edit</a><a href='deletemessage/".$result['id']."'>x</a></li>";
    }

    echo "<fieldset>
  <form action='/newpost' method='post'>
  <label for='message'>Message:</label>
  <input type='text' name='message' id='message' placeholder='Type Twittra message' />
  <input type='submit' value='Submit Message' />
  </form>
</fieldset>";

  });

 $app->post('/newpost', function () use ($dbh, $app) {
    $message = $app->request()->post('message');
    $sth = $dbh->prepare('INSERT INTO lab1 (message) VALUES (:message);');
    $sth->bindParam(':message', $message);
    $sth->execute();
    $app->response->redirect("/", 303);
    //$app->response->header("Location: index.php");
    //exit;
 });

  $app->get('/editmessage/:postid', function ($postid) {
    echo "<fieldset>
  <form action='/edit/".$postid."' method='post'>
  <label for='editmessage'>Edit Message:</label>
  <input type='text' name='editmessage' id='editmessage' placeholder='Type Twittra edit' />
  <input type='submit' value='Edit Message' />
  </form>
</fieldset>";
   });

 $app->post('/edit/:postid', function ($postid) use ($dbh, $app) {
    $editmessage = $app->request()->post('editmessage');
    $sth = $dbh->prepare('UPDATE lab1 SET message = :message WHERE id = :id;');
    $sth->bindParam(':message', $editmessage);
    $sth->bindParam(':id', $postid);
    $sth->execute();
    $app->response->redirect("/", 303);
 });

  $app->get('/deletemessage/:postid', function ($postid) use ($dbh, $app) {
    $deletemessage = $app->request()->post('deletemessage');
    $sth = $dbh->prepare('DELETE from lab1 WHERE id = :id;');
    $sth->bindParam(':id', $postid);
    $sth->execute();
    $app->response->redirect("/", 303);
 });


  $app->run();

