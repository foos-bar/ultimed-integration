<?php namespace Ultimed\Parsers;

use Carbon\Carbon;

class EmberDate
{
    /**
     * @var DateTime
     */
    private $date;

    public function __construct($string)
    {
        $this->date = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $string);
    }

    public function date()
    {
        return $this->date;
    }
}
