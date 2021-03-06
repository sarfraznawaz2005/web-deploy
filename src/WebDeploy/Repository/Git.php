<?php
namespace WebDeploy\Repository;

use WebDeploy\Exception;
use WebDeploy\Shell\Shell;

class Git extends Repository
{
    public static function check()
    {
        Shell::check();

        if (!(new Shell)->exec('which git')->getLog()['success']) {
            throw new Exception\BadFunctionCallException(__('<strong>git</strong> command is not installed'));
        }
    }

    public function currentBranch()
    {
        return $this->exec('git rev-parse --abbrev-ref HEAD');
    }

    public function getCurrentBranch()
    {
        return $this->currentBranch()->getShellAndLog();
    }

    public function branches()
    {
        return $this->exec('git branch');
    }

    public function getBranches()
    {
        return $this->branches()->getShellAndLog();
    }

    public function lastCommit()
    {
        return $this->exec('git log --name-status HEAD^..HEAD');
    }

    public function getLastCommit()
    {
        return $this->lastCommit()->getShellAndLog();
    }

    public function status()
    {
        return $this->exec('git status');
    }

    public function getStatus()
    {
        return $this->status()->getShellAndLog();
    }

    public function pull()
    {
        return $this->exec('git pull');
    }

    public function fetch()
    {
        return $this->exec('git fetch --all');
    }

    public function reset()
    {
        return $this->exec('git reset --hard');
    }

    public function checkout($hash)
    {
        return $this->exec('git checkout '.$hash);
    }

    public function show($hash)
    {
        return $this->exec('git show '.$hash);
    }

    public function diff($file)
    {
        return $this->exec('git diff '.$file);
    }

    public function logSimple($limit)
    {
        return $this->exec('git log --max-count='.(int)$limit.' --date=iso --pretty=format:"%h %cd [%an] %s"');
    }

    public function getLogSimple($limit)
    {
        return $this->logSimple($limit)->getShellAndLog();
    }

    public function logStat($limit)
    {
        return $this->exec('git log --stat --max-count='.(int)$limit);
    }

    public function getLogStat($limit)
    {
        return $this->logStat($limit)->getShellAndLog();
    }

    public function getLogSimpleList($limit = 100)
    {
        $log = (new self($this->path))->getLogSimple($limit)['success'];

        if (empty($log)) {
            return array();
        }

        return array_map(function ($line) {
            $line = explode(' ', $line, 2);

            return array(
                'hash' => $line[0],
                'message' => preg_replace('/\s\+[0-9]{4}\s/', ' ', $line[1])
            );
        }, explode("\n", $log));
    }

    public function getBranchesList()
    {
        $branches = (new self($this->path))->getBranches()['success'];

        if (empty($branches)) {
            return array();
        }

        return array_map(function ($line) {
            return array(
                'current' => preg_match('/^\*/', $line),
                'name' => trim(preg_replace('/^\* /', '', $line))
            );
        }, explode("\n", $branches));
    }

    public function setLinks(array $log)
    {
        return self::setDiffLinks(self::setCommitLinks($log));
    }

    public function setCommitLinks(array $log)
    {
        if (empty($log['success'])) {
            return $log;
        }

        $log['success'] = preg_replace_callback('#commit\s+([a-z0-9]{40})#', function ($matches) {
            return 'commit <a href="'.route('/git/commit').'?h='.urlencode($matches[1]).'">'.$matches[1].'</a>';
        }, $log['success']);

        return $log;
    }

    public function setDiffLinks(array $log)
    {
        if (empty($log['success'])) {
            return $log;
        }

        $log['success'] = preg_replace_callback('#(\s+[a-z]+:\s+)([\w\-/\.]+)#', function ($matches) {
            return $matches[1].'<a href="'.route('/git/diff').'?f='.urlencode($matches[2]).'">'.$matches[2].'</a>';
        }, $log['success']);

        return $log;
    }
}
