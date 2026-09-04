<?php

require_once __DIR__ . '/src/Grade.php';

class GPA
{
    /**
     * Calculate weighted GPA.
     *
     * @param array $courses
     * @return float
     * @throws InvalidArgumentException
     */
    public static function calculate($courses)
    {
        if (!is_array($courses) || empty($courses)) {
            throw new InvalidArgumentException(
                "At least one course is required to calculate GPA."
            );
        }

        $totalQualityPoints = 0;
        $totalCreditHours = 0;

        foreach ($courses as $course) {

            if (!isset($course['grade'], $course['credit_hours'])) {
                throw new InvalidArgumentException(
                    "Each course must have a grade and credit hours."
                );
            }

            $grade = strtoupper(trim($course['grade']));
            $creditHours = $course['credit_hours'];

            if (!is_numeric($creditHours) || $creditHours <= 0) {
                throw new InvalidArgumentException(
                    "Credit hours must be greater than zero."
                );
            }

            $creditHours = (float)$creditHours;

            $gradePoint = Grade::gradePoint($grade);

            $qualityPoints = $gradePoint * $creditHours;

            $totalQualityPoints += $qualityPoints;

            $totalCreditHours += $creditHours;
        }

        if ($totalCreditHours <= 0) {
            throw new InvalidArgumentException(
                "Total credit hours must be greater than zero."
            );
        }

        return round(
            $totalQualityPoints / $totalCreditHours,
            2
        );
    }
}