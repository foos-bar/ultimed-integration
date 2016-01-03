<?php namespace Ultimed\Presenters;

use DateTime;

class EmberDate
{
    /**
     * @var DateTime
     */
    private $date;

    public function __construct(DateTime $date = null)
    {
        $this->date = $date;
    }

    public function format()
    {
        if ($this->date === null)
        {
            return null;
        }

        return $this->date->format('Y-m-d\TH:i:s.000\Z');
    }
}
