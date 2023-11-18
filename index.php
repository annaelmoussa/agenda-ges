<?php

use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

require_once 'vendor/autoload.php';
require_once 'config.php';

// Ajout des en-têtes de sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

// Validation et Sanitisation de l'entrée
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!$token) {
    handleError('Paramètre token manquant');
}

// Vérification du jeton
if ($token !== USER_CONFIG['TOKEN']) {
    handleError('Accès non autorisé');
}

try {
    $client = new MyGes\Client('skolae-app', USER_CONFIG['MYGES_USERNAME'], USER_CONFIG['MYGES_PASSWORD']);
    $agenda = fetchAgenda($client);
    outputCalendar($agenda);
} catch (Exception $e) {
    handleError('Erreur lors de la connexion ou de la récupération des données : ' . $e->getMessage());
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

    foreach ($agenda as $a) {
        $start = (new DateTime())->setTimestamp($a->start_date / 1000);
        $end = (new DateTime())->setTimestamp($a->end_date / 1000);
        $address = isset($a->rooms[0]->name) && isset($a->rooms[0]->campus) ? $a->rooms[0]->campus . ' - ' . $a->rooms[0]->name : '';

        $calendar->event(
            Event::create($a->name)
                ->startsAt($start)
                ->endsAt($end)
                ->address($address)
                ->description($a->discipline->teacher)
        );
    }

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="cal.ics"');
    echo $calendar->get();
}

function handleError($message)
{
    die($message);
}
