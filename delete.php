<?php
session_start();


if (isset($_GET['calendarId'])) {
    $id = $_GET['calendarId'];
    $url = "https://www.googleapis.com/calendar/v3/calendars/$id";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $accessToken = $_SESSION['access_token'];
    $headers = array(
        "Authorization: Bearer $accessToken",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    var_dump($resp);
    header("Location: allcalenders.php");
}


if (isset($_GET['eventId'])) {
    $eventId = $_GET['eventId'];
    $calendarId = $_GET['calendarId'];
    $url = "https://www.googleapis.com/calendar/v3/calendars/$calendarId/events/$eventId";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $accessToken = $_SESSION['access_token'];
    $headers = array(
        "Authorization: Bearer $accessToken",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    var_dump($resp);
    header("Location: allevents.php?id=" . $calendarId);
}
