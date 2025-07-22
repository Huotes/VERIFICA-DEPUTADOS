<?php

protected function schedule(Schedule $schedule)
{
    $schedule->command('sincronizar:deputados')->daily(); // roda todo dia
}
