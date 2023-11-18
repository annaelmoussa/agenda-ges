<?php

use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once 'vendor/autoload.php';
require_once 'config.php';

// Configuration du logger
$logger = new Logger('agendaGES');
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/app.log', Logger::WARNING));

// Ajout des en-têtes de sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header("Content-Security-Policy: default-src 'self'; script-src 'none'; object-src 'none';");

try {
    $token = validateToken();
    $client = createClient();
    $agenda = fetchAgenda($client);
    outputCalendar($agenda);
} catch (Exception $e) {
    $logger->error($e->getMessage());
    handleError($e->getMessage());
}

function validateToken()
{
    $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if (!$token || strlen($token) !== 32) {
        throw new Exception('Paramètre token manquant ou invalide');
    }
    if ($token !== USER_CONFIG['TOKEN']) {
        throw new Exception('Accès non autorisé');
    }
    return $token;
}

function createClient()
{
    return new MyGes\Client('skolae-app', USER_CONFIG['MYGES_USERNAME'], USER_CONFIG['MYGES_PASSWORD']);
}

function fetchAgenda($client)
{
    $me = new MyGes\Me($client);
    $start = new DateTime();
    $start->setTime(0, 0, 0);
    $end = (new DateTime())->setTime(0, 0, 0)->add(DateInterval::createFromDateString('1 month'));
    return $me->getAgenda($start->getTimestamp() * 1000, $end->getTimestamp() * 1000);
}

function outputCalendar($agenda)
{
    $calendar = new Calendar();

    foreach ($agenda as $event) {
        $start = (new DateTime())->setTimestamp($event->start_date / 1000);
        $end = (new DateTime())->setTimestamp($event->end_date / 1000);
        $address = isset($event->rooms[0]->name) ? $event->rooms[0]->campus . ' - ' . $event->rooms[0]->name : '';

        $calendar->event(
            Event::create($event->name)
                ->startsAt($start)
                ->endsAt($end)
                ->address($address)
                ->description($event->discipline->teacher)
        );
    }

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="cal.ics"');
    echo $calendar->get();
}

function handleError($message)
{
    global $logger;
    $logger->error($message);
    header('Location: error_page.php');
    exit();
}
