<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Session\Handlers\BaseHandler;
use CodeIgniter\Session\Handlers\FileHandler;

class Session extends BaseConfig
{
public string $driver = 'CodeIgniter\Session\Handlers\DatabaseHandler';
public string $cookieName = 'ems_session';
public int $expiration = 7200;
public string $savePath = 'ci_sessions';
public bool $matchIP = false;
public int $timeToUpdate = 300;
public bool $regenerateDestroy = false;
}
