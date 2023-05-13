<?php
// Prepend a base path if Predis is not available in your "include_path".
require 'vendor/autoload.php';

Predis\Autoloader::register();

$redis = new Predis\Client();

// echo $redis->ping();

// $redis->set('foo', 'bar');
// echo $value = $redis->get('foo');


//Time to caching the Data using DB
$getCachedValue = $redis->get('tracks');

if($getCachedValue){

    //show data from Redis
    echo "<h1>Data showing from Redis</h1>"."<br>";
    $t0 = microtime(true) * 1000;
    echo $getCachedValue;
    $t1 = microtime(true) * 1000;
    echo "Time taken ".round($t1 - $t0, 4);
    exit();

}else{
    //show data from DB
    $conn = new mysqli('localhost:3308', 'root', 'root', 'gyro_db');

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
      }
    //   else{
    //     echo "connected";
    //     exit();
    //   }

    $sql = $conn->query("Select track_name from track_list limit 50");
    $track_name = '';
    
    if($sql){
        echo "<h1>Data showing from Database</h1>"."<br>";
        $t0 = microtime(true) * 1000;
        while($data = $sql->fetch_assoc()){

           $track_name .= $data['track_name']."<br>";            

        }
        echo $track_name;
        $t1 = microtime(true) * 1000;
        echo "Time taken ".round($t1 - $t0, 4);
        $redis->set('tracks',$track_name);
        $redis->expire('tracks', 10); //to expire cached data from the server
        exit();
    }

}



?>