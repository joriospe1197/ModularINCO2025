<?php 

require 'funciones.php';
require 'database.php';
require __DIR__ . '/../vendor/autoload.php';

// Conectarnos a la base de datos
use Model\ActiveRecord;
use Model\ManifiestosActiveRecord;
use Model\ManifiestosRecord;
use Model\WeeklyRecord;
use Model\WeeklyHistory;

ActiveRecord::setDB($db);
WeeklyRecord::setDB($db);
WeeklyHistory::setDB($db);
ManifiestosRecord::setDB($db);
ManifiestosActiveRecord::setDB($db);
