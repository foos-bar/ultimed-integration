<?php namespace Ultimed\Responses;

class PatientSearchByCustomFields extends ApiResponse
{
    private $patients;

    protected function init()
    {
        $data = $this->parseJson();
        $this->patients = $data['patients'];
    }

    public function getPatients()
    {
        return $this->patients;
    }
}
