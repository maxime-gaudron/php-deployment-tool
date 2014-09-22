<?php

namespace QaSystem\CoreBundle\Git;

use GitElephant\Command\RevListCommand as BaseRevListCommand;
use GitElephant\Objects\Commit;

class RevListCommand extends BaseRevListCommand
{
    /**
     * get the commits path to the passed commit. Useful to count commits in a repo
     *
     * @param Commit $from
     * @param Commit $to
     *
     * @return string
     */
    public function commitsBehind(Commit $from, Commit $to)
    {
        $this->clearAll();
        $this->addCommandName(static::GIT_REVLIST);
        $this->addCommandSubject(sprintf('%s..%s', $from->getSha(), $to->getSha()));

        return $this->getCommand();
    }
}
