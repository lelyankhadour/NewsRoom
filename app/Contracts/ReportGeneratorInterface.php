<?php

namespace App\Contracts;

interface ReportGeneratorInterface
{
    public function generate(): array;
    public function getReportName(): string;
}