<?php
namespace WebDeploy\Controller;

use WebDeploy\Processor;
use WebDeploy\Repository;

class Ftp extends Controller
{
    public function index()
    {
        meta()->set('title', 'FTP Status');

        if (is_object($response = $this->check(true, false))) {
            return $response;
        }

        return self::content('ftp.index', array(
            'config' => config('ftp'),
            'path' => array('success' => config('project')['path']),
            'processor' => (new Processor\Ftp)->index()
        ));
    }

    public function update()
    {
        meta()->set('title', 'FTP Update');

        if (is_object($response = $this->check(true, true))) {
            return $response;
        }

        $config = config('ftp');
        $days = (int)input('days') ?: $config['days_history'];

        if (input('find') === 'true') {
            $files = (new Repository\Ftp)->getRecentFiles($days);
        } else {
            $files = array();
        }

        return self::content('ftp.update', array(
            'config' => $config,
            'days' => $days,
            'files' => $files,
            'processor' => (new Processor\Ftp)->update()
        ));
    }
}
