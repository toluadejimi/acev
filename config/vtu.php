<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VTU category IDs (optional)
    |--------------------------------------------------------------------------
    |
    | Point each key to your `categories` table id for quick links on the
    | dashboard. If unset, the app tries to match category titles automatically
    | (e.g. title contains "airtime", "data", "cable", "electric").
    |
    */

    'categories' => [
        'airtime' => env('VTU_CAT_AIRTIME'),
        'data' => env('VTU_CAT_DATA'),
        'cable_tv' => env('VTU_CAT_CABLE_TV'),
        'electricity' => env('VTU_CAT_ELECTRICITY'),
    ],

];
