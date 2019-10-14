<?php namespace Standardizer\Interfaces;

interface StepInterface 
{
    
    public function executeStep(string $string): string;
    public function getStepName(): string;

}