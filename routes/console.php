<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('jobs:archive-expired')->daily();
