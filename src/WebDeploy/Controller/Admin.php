<?php
namespace WebDeploy\Controller;

use Exception;
use WebDeploy\Processor;
use WebDeploy\Repository;
use WebDeploy\Router\Route;

class Admin extends Controller
{
    private function check()
    {
        try {
            Repository\Git::check();
            Repository\Composer::check();
        } catch (Exception $e) {
            return self::error('admin', $e->getMessage());
        }
    }

    public function git()
    {
        return new Repository\Git(Route::getBasePath());
    }

    public function index()
    {
        meta()->meta('title', 'Web Deploy Status');

        if (is_object($error = $this->check())) {
            return $error;
        }

        $logs = $this->git()
            ->currentBranch()
            ->lastCommit()
            ->status()
            ->getShell()
            ->exec('pwd')
            ->getLogs();

        return self::content('admin.index', array(
            'branch' => array_shift($logs),
            'commit' => array_shift($logs),
            'status' => array_shift($logs),
            'path' => array_shift($logs)
        ));
    }

    public function update()
    {
        meta()->meta('title', 'Web Deploy Update');

        if (is_object($error = $this->check())) {
            return $error;
        }

        $processor = (new Processor\Admin)->update();

        $git = $this->git();

        return self::content('admin.update', array(
            'log' => $git->getLogSimpleList(),
            'branches' => $git->getBranchesList(),
            'processor' => $processor
        ));
    }

    public function log()
    {
        meta()->meta('title', 'Web Deploy Log');

        if (is_object($error = $this->check())) {
            return $error;
        }

        $last = (int)input('last') ?: config('git')['log_history'];

        return self::content('admin.log', array(
            'last' => $last,
            'log' => $this->git()->getLogStat($last)
        ));
    }
}
