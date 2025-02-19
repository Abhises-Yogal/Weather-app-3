<?php
$serverName = "sql302.infinityfree.com";
$userName= "if0_38247405";
$password = "fHyHtDyMJL";
$conn = mysqli_connect($serverName, $userName, $password);
if($conn){
    //echo "Connection Successful <br>";
}
else{
    echo "Failed to connect".mysqli_connect_error();
}

$createDatabase = "CREATE DATABASE IF NOT EXISTS if0_38247405_abhises";
if (mysqli_query($conn, $createDatabase)) {
    //echo "Database Created or already Exists <br>";
} else {
    echo "Failed to create database <br>" . mysqli_connect_error();
}

// Select the created database
mysqli_select_db($conn, 'if0_38247405_abhises');

$createTable = "CREATE TABLE IF NOT EXISTS weather (
    city varchar(200) NOT NULL, 
    DateAndTime varchar(200) NOT NULL,
    TimeZone varchar(200) NOT NULL,
    Country varchar(200) NOT NULL,
    MinTemp varchar(200) NOT NULL,
    MaxTemp varchar(200) NOT NULL,
    Weather_Status varchar(200) NOT NULL,
    Weather_Description varchar(200) NOT NULL,
    Pressure varchar(200) NOT NULL,
    Humidity varchar(200) NOT NULL,
    Windspeed varchar(200) NOT NULL,
    Icon varchar(200) NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";
if (mysqli_query($conn, $createTable)) {
    //echo "Table Created or already Exists <br>";
} else {
    echo "Failed to create table <br>" . mysqli_connect_error();
}

if(isset($_GET['q'])){
    $cityName = $_GET['q'];
    //echo $cityName;
}else{
    $cityName = "Cardiff";
}

$dt = "";
$timezone = "";
$country = "";
$maxtemp = "";
$mintemp = "";
$windspeed = "";
$status = "";
$detailstatus = "";
$icon = "";
$humidity = "";
$pressure = "";

$selectAllData = "SELECT * FROM weather WHERE city = '$cityName' AND TIMESTAMPDIFF(HOUR, last_updated, NOW()) < 2";
$result = mysqli_query($conn, $selectAllData);
if (mysqli_num_rows($result) == 0) {
    $url = "https://api.openweathermap.org/data/2.5/weather?q=".urlencode($cityName)."&appid=96bad30255ae1e2e1c79b43ca5cf1a0f&units=metric";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    $weather = $data['weather'];
    $cityName = $data['name'];
    $dt = $data['dt'];
    $timezone = $data['timezone'];
    $country = $data['sys']['country'];
    $maxtemp = $data['main']['temp_max'];
    $mintemp = $data['main']['temp_min'];
    $windspeed = $data['wind']['speed'];
    $status = $weather[0]['main'];
    $detailstatus = $weather[0]['description'];
    $icon = $weather[0]['icon'];
    $humidity = $data['main']['humidity'];
    $pressure = $data['main']['pressure'];


    $insertData = "REPLACE INTO weather (city, DateAndTime, TimeZone, Country, MinTemp, MaxTemp, Weather_Status, Weather_Description, Pressure, Humidity, Windspeed, Icon) 
    VALUES ('$cityName', '$dt', '$timezone', '$country', '$mintemp', '$maxtemp', '$status', '$detailstatus', '$pressure', '$humidity', '$windspeed', '$icon')";
    if (mysqli_query($conn, $insertData)) {
        //echo "Data inserted Successfully";
    } else {
        echo "Failed to insert data" . mysqli_error($conn);
    }

    // Fetching data from weather table based on city name again after insertion
    $result = mysqli_query($conn, $selectAllData);
}

$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

// Encoding fetched data to JSON and sending as response
$json_data = json_encode($rows);
header('Content-Type: application/json');
echo $json_data;
?>