<?php

namespace QaSystem\CoreBundle\Git;

use GitElephant\Objects\Commit;
use GitElephant\Repository as BaseRepository;

class Repository extends BaseRepository
{
    public function countCommitsBehind($from, $to)
    {

        $commitFrom = Commit::pick($this, $from);
        $commitTo   = Commit::pick($this, $to);

        $command = RevListCommand::getInstance($this)->commitsBehind($commitFrom, $commitTo);

        return count($this->getCaller()->execute($command)->getOutputLines(true));
    }
}
