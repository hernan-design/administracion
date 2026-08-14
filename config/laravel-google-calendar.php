<?php

return [

    /**
     * Path to a json file containing the credentials of a Google Service account.
     */
    'client_secret_json' => storage_path('app/laravel-google-calendar/client_secret_580085693458-05s86pupvh4l3k9kahiu80s6qrcct3k1.apps.googleusercontent.com.json'),

    /**
     *  The id of the Google Calendar that will be used by default.
     */
    'calendar_id' => env('GOOGLE_CALENDAR_ID','tuproyecto.com_7dvlttntsnu0i8v8cd5qgop9u8@group.calendar.google.com'),

    /**
     *  This is where google will save the credentials
     */
    // 'credentials_path' => storage_path('app/laravel-google-calendar/credentials.json'),
    'credentials_path' => storage_path('app/laravel-google-calendar/quickstart-1587573860878-fdcb525870b8.json'),
];
