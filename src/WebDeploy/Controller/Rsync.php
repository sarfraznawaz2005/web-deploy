<?php
namespace WebDeploy\Controller;

use Exception;
use WebDeploy\Processor;
use WebDeploy\Repository;
use WebDeploy\Shell\Shell;

class Rsync extends Controller
{
    private function check($module = true, $repository = true)
    {
        try {
            if ($module) {
                self::checkModule('rsync');
            }

            if ($repository) {
                Repository\Rsync::check();
            }
        } catch (Exception $e) {
            return self::error('rsync', $e->getMessage());
        }
    }

    public function rsync()
    {
        return new Repository\Rsync;
    }

    public function index()
    {
        meta()->meta('title', 'RSYNC Status');

        if (is_object($error = $this->check(true, false))) {
            return $error;
        }

        return self::content('rsync.index', array(
            'whoami' => (new Shell)->exec('whoami')->getLog(),
            'config' => config('rsync'),
            'path' => array('success' => config('project')['path']),
            'processor' => (new Processor\Rsync)->index()
        ));
    }

    public function update()
    {
        meta()->meta('title', 'RSYNC Update');

        if (is_object($response = $this->check())) {
            return $response;
        }

        if (input('find') === 'true') {
            $files = (new Repository\Rsync)->getUpdatedFiles();
        } else {
            $files = array();
        }

        return self::content('ftp.update', array(
            'config' => config('ftp'),
            'files' => $files,
            'processor' => (new Processor\Rsync)->update()
        ));
    }
}