<?php
require_once __DIR__.'/../includes/db.php';
$method=$_SERVER['REQUEST_METHOD'];$id=$_GET['id']??null;
if($method==='GET'){if($id){$stmt=db()->prepare('SELECT * FROM tours WHERE id=?');$stmt->execute([$id]);json_response(['data'=>$stmt->fetch()]);}json_response(['data'=>db()->query('SELECT * FROM tours ORDER BY id DESC')->fetchAll()]);}
json_response(['error'=>'Method not allowed'],405);
