<?php
require_once __DIR__.'/../includes/db.php';
if($_SERVER['REQUEST_METHOD']==='GET'){json_response(['data'=>db()->query('SELECT * FROM reviews WHERE approved=1 ORDER BY created_at DESC')->fetchAll()]);}
if($_SERVER['REQUEST_METHOD']==='POST'){$name=trim($_POST['name']??'');$email=trim($_POST['email']??'');$rating=(int)($_POST['rating']??0);$review=trim($_POST['review']??'');if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||$rating<1||$rating>5||$review==='')json_response(['error'=>'Invalid input'],422);$stmt=db()->prepare('INSERT INTO reviews (guest_name,guest_email,rating,review_text,approved) VALUES (?,?,?,?,0)');$stmt->execute([$name,$email,$rating,$review]);json_response(['message'=>'Review submitted for moderation']);}
json_response(['error'=>'Method not allowed'],405);
