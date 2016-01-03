<?php namespace Ultimed\Responses;

class PatientSearchbyCustomFields extends ApiResponse
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
