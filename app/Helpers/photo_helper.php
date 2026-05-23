<?php

if (!function_exists('employee_photo')) {
    /**
     * Returns the profile photo HTML for an employee.
     * Falls back to initials avatar if no photo.
     */
    function employee_photo(array $employee, string $size = '48px', string $fontSize = '1rem'): string
    {
        if (!empty($employee['profile_photo'])) {
            $src = base_url('uploads/profiles/' . $employee['profile_photo']);
            return "<img src=\"{$src}\" alt=\"Profile\"
                        style=\"width:{$size};height:{$size};border-radius:50%;object-fit:cover;\">";
        }

        $initials = strtoupper(
            substr($employee['first_name'], 0, 1) .
            substr($employee['last_name'],  0, 1)
        );

        return "<div class=\"rounded-circle bg-primary text-white d-flex align-items-center justify-content-center\"
                     style=\"width:{$size};height:{$size};font-size:{$fontSize};font-weight:700;flex-shrink:0;\">
                    {$initials}
                </div>";
    }
}