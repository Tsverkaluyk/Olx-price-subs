<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('olx:check-prices')->everyFiveMinutes();

