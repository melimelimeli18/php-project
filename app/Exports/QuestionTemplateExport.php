<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuestionTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            // Sample row to guide the user
            [
                '1',
                'What is the capital of Indonesia?',
                'Jakarta',
                'Bandung',
                'Surabaya',
                'Medan',
                'A'
            ],
            [
                '2',
                '1 + 1 equals?',
                '1',
                '2',
                '3',
                '4',
                'B'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Question',
            'Option A',
            'Option B',
            'Option C',
            'Option D',
            'Correct Answer'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
