<?php

require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Calendar;

session_start();
function configClient($client)
{
    try {
        $client->setAuthConfig('client_secret.json');
    } catch (\Google\Exception $e) {
        // Deal with auth error
    }
    $client->setAccessType('offline');
    $client->addScope(Calendar::CALENDAR);
    $client->setRedirectUri("http://localhost:8080/");


}

function logout()
{
    session_destroy();
    file_put_contents("refreshToken.txt", "");
}

function handleLogin(Client $client)
{
    $authCode = $_GET["code"];
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    $refreshToken = $client->getRefreshToken();
    file_put_contents('refreshToken.txt', $refreshToken);
    $_SESSION['token'] = json_encode($accessToken);
    header('Location: /');
}

function storeAccessToken(Client $client)
{
    $accessToken = json_decode($_SESSION["token"], true);
    $client->setAccessToken($accessToken);
}

function requestLogin(Client $client)
{
    $authUrl = $client->createAuthUrl();
    echo sprintf("Abra o seguinte link no seu navegador e conceda permissões:%s", PHP_EOL);
    echo "<a href='$authUrl'>Link autorização<a/>";
}

function refreshAccessToken(Client $client, string $refreshToken)
{
    $client->fetchAccessTokenWithRefreshToken($refreshToken);
    if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION["token"])) {
        header('Location: /');
    }
}

function createEvent(Client $client)
{
    $service = new Calendar($client);
    $event = new Calendar\Event([
        'summary' => $_POST["summary"],
        'description' => $_POST["description"],
        'start' => [
            'dateTime' => $_POST["starts"] . ":00",
            'timeZone' => 'America/Sao_Paulo',
        ],
        'end' => [
            'dateTime' => $_POST["ends"] . ":00",
            'timeZone' => 'America/Sao_Paulo',
        ],
        'attendees' => [
            ['email' => $_POST["doctor"]],
            ['email' => $_POST["patient"]],
        ],
    ]);

    $calendarId = 'primary';
    try {
        $event = $service->events->insert($calendarId, $event);
    } catch (\Google\Service\Exception $e) {
        print $e . PHP_EOL;
    }

    echo 'Evento criado: ';
    echo "<a href='$event->htmlLink' target='_blank'>Clique aqui para visualizar<a/>";
}

$client = new Client();
configClient($client);
if (isset($_GET["logout"])) {
    logout();
}

if (isset($_GET["code"])) {
    handleLogin($client);
}

if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION["token"])) {
    storeAccessToken($client);
}

if ($client->isAccessTokenExpired()) {
    if (!empty($refreshToken)) {
        refreshAccessToken($client, $refreshToken);
    } else {
        requestLogin($client);
    }
} else {
    if (!empty($_POST)) {
        createEvent($client);
    } else {
        require "./form.php";
    }
}