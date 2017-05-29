<?php

use ORM\EntityManager;

function getLastEmInstance()
{
    return EntityManager::getInstance();
}
