<?php
include 'VistaSoft.php';

$VistaSoft = VistaSoft::getInstance();

$retorno = $VistaSoft->ping();

print_r($retorno);
