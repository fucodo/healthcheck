<?php

namespace fucodo\HealthCheck\Domain\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class HealthCheckResult
{
    protected Collection $diagnoses;

    public function __construct()
    {
        $this->diagnoses = new ArrayCollection();
    }

    public function add(HealthCheckInterface $diagnosis)
    {
        $this->diagnoses->add($diagnosis);
    }

    public function getAllDiagnosis(): Collection
    {
        return $this->diagnoses;
    }

    public static function fromArray(array $diagnosisArray)
    {
        $d = new HealthCheckResult();
        foreach ($diagnosisArray as $diagnosis) {
            $d->add($diagnosis);
        }
        return $d;
    }
}
