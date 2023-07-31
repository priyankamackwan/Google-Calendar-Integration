<?php

namespace App\Services\Google;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

class CalendarService
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $scopes = [
        Calendar::CALENDAR,
    ];

    private $calendarId;

    private $client;
    private $calendar;

    public function __construct()
    {
        // Get config variables
        $this->clientId = config('services.google.client_id');
        $this->clientSecret = config('services.google.client_secret');
        $this->redirectUri = config('services.google.redirect');
        $this->calendarId = config('services.google.calendar_id');

        // Create client
        $this->client = new Client();
        $this->client->setApplicationName('Google Calendar API PHP');
        $this->client->setClientId($this->clientId);
        $this->client->setClientSecret($this->clientSecret);
        $this->client->setRedirectUri($this->redirectUri);
        $this->client->setScopes($this->scopes);

        // Set access type and prompt
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent select_account');

        // Create calendar
        $this->calendar = new Calendar($this->client);
    }

    public function getTokensFromCode($code)
    {
        $this->client->fetchAccessTokenWithAuthCode($code);

        return $this->client->getAccessToken();
    }

    public function status()
    {
        return !$this->client->isAccessTokenExpired();
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function setTokens($accessToken, $refreshToken)
    {
        $this->client->setAccessToken($accessToken);
        $this->client->refreshToken($refreshToken);
    }

    public function getEvents()
    {
        $optParams = [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        ];

        $results = $this->calendar->events->listEvents($this->calendarId, $optParams);

        return $results->getItems();
    }

    public function getEvent($eventId)
    {
        return $this->calendar->events->get($this->calendarId, $eventId);
    }

    public function deleteEvent($eventId)
    {
        return $this->calendar->events->delete($this->calendarId, $eventId);
    }

    public function createEvent($event)
    {
        $event = new Event($event);

        return $this->calendar->events->insert($this->calendarId, $event);
    }

    public function updateEvent($eventId, $eventDetails)
    {
        $eventDetails = new Event($eventDetails);
        
        return $this->calendar->events->update($this->calendarId, $eventId, $eventDetails);
    }
}
