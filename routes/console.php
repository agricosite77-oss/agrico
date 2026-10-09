<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('users:expire-suspensions')->everyMinute();
