#!/usr/bin/env php
<?php
require __DIR__.'/src/WebDeploy/bootstrap.php';

array_shift($argv);

WebDeploy\Command\Command::execute(array_shift($argv), $argv);

exit;