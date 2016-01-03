<?php namespace Ultimed\Responses;

use Ultimed\Parsers\EmberDate;

class PictureCreate extends ApiResponse
{
    private $picture;

    protected function init()
    {
        $data = $this->parseJson();
        $this->picture = $data['picture'];

        if (array_key_exists('date', $this->picture)) {
            $parser = new EmberDate($this->picture['date']);
            $this->picture['date'] = $parser->date();
        }
    }

    public function getPicture()
    {
        return $this->picture;
    }
}
