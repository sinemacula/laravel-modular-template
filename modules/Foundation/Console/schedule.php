<?php

use Illuminate\Support\Facades\Schedule;

/*
|-------------------------------------------------------------------------------
| Model Pruning
|-------------------------------------------------------------------------------
|
| Define the model pruning scheduled commands.
|
*/
Schedule::command('model:prune')->onOneServer()->daily();
