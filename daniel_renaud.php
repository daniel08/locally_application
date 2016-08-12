<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/*
* Locally, Web Developer Questionnaire
* Author: Daniel Renaud
*/

/*Excericise 1*/
  $items       = array();
  $n_items     = 24;
  $letters     = 'zyxwvutsrqponmlkjihgfedcba'; 

  for ($i=0; $i < $n_items; $i++) { 
       
      $items[] = substr($letters, $i, 1);
  }

    //print the values of this array in alphabetical order and divided evenly into 3 unordered lists
    
    //Sort the array
    sort($items);
    
    //Figure out how to divide into 3 lists
    $divisor = floor($n_items/3); //Should be 8
    
    //Build up out HTML
    $out = "";
    for($i=0; $i < count($items); $i++){
        if( $i == 0 ){
            $out  .= "<ul>\n";
        }
        
        $out .= "<li>$i. {$items[$i]}</li>\n";
        
        if( $i > 0 AND ($i+1) % $divisor == 0 ){
            $out .= "</ul>"; // End this list
            if($i < count($items)){
                $out .= "<ul>\n"; //Start a new list
            }
        }   
    }
    
    echo $out;
/*END Excercise 1*/

/*Exercise 2*/
$dbConf = [
    'host' => '127.0.0.1',
    'user' => 'dev',
    'pass' => 'devpass',
    'db' => 'temp'
];
//Pretend the user is already configured
$db = new PDO("mysql:host={$dbConf['host']}", $dbConf['user']);

//Make DB
$db->exec('CREATE DATABASE IF NOT EXISTS temp');

$db->exec('USE temp');

//Make Tables
$db->exec('DROP TABLE IF EXISTS people');
$db->exec('DROP TABLE IF EXISTS fruits');

$db->exec('CREATE TABLE IF NOT EXISTS people (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), favorite_fruit_ids VARCHAR(255))');
$db->exec('CREATE TABLE IF NOT EXISTS fruits (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255))');

//Populate Tables
$db->exec("INSERT INTO people(id, name, favorite_fruit_ids) VALUES
    (1,'Jacob','1,5,7'),
    (2,'Ava','2,6,1,7'),
    (3,'Noah','7'),
    (4,'Olivia','10'),
    (5,'Jayden','9'),
    (6,'Emma','2,4'),
    (7,'William','9,6'),
    (8,'Isabella','3'),
    (9,'Mason','10'),
    (10,'Sophia','3,4,8')");

$db->exec("INSERT INTO fruits(id, name) VALUES
    (1,'Apple'),
    (2,'Banana'),
    (3,'Lemon'),
    (4,'Blueberry'),
    (5,'Strawberry'),
    (6,'Orange'),
    (7,'Pineapple'),
    (8,'Mango'),
    (9,'Watermelon'),
    (10,'Pear')");
    
//Now we got the DB setup, lets do the excercise
$people_fruits = [];

$stmt = $db->prepare('SELECT * FROM people ORDER BY id');
if( $stmt->execute() ){
    $people = $stmt->fetchALL(PDO::FETCH_OBJ);
}
//We could easily just fetch all the fruits and work with arrays,
// but we'll do the querying for the sake of demonstration
foreach($people as $i=>$objP){
    $n_fruits = count(explode(',', $objP->favorite_fruit_ids));
    //Query the favorite fruits
    $stmt = $db->prepare('SELECT * FROM fruits WHERE id IN (:fruits)');
    if($stmt->execute([':fruits'=>$objP->favorite_fruit_ids])){
        $rawFruits = $stmt->fetchAll(PDO::FETCH_OBJ);
        $fruits = array_map(function($f){return $f->name;}, $rawFruits);        
    }
    $people_fruits[] = [
        'name' => $objP->name,
        'fruits' => implode(',', $fruits),
        'n_fruits' => $n_fruits
    ];
}

//Sort
uasort($people_fruits, function($a, $b){return $a['n_fruits'] < $b['n_fruits'];});

echo '<pre>';
var_dump($people_fruits);
echo '</pre>';

/*END Excercise 2*/