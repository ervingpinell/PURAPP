<?php

namespace App\Http\Requests\Concerns;

trait NormalizesRequestInput
{
    /**
     * Devuelve string recortado (si no hay valor -> '' o null si $nullable=true)
     */
    protected function str(string $key, bool $nullable = false, ?string $default = null): ?string
    {
        $value = $this->input($key, $default);

        // Null/empty
        if ($value === null || $value === '') {
            return $nullable ? null : '';
        }

        // Escalares u objetos casteables
        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            $s = trim((string) $value);
            return ($nullable && $s === '') ? null : $s;
        }

        // Si es array/obj no casteable
        return $nullable ? null : '';
    }

    protected function bool(string $key, ?bool $default = null): ?bool
    {
        return $this->has($key) ? $this->boolean($key) : $default;
    }

    protected function intVal(string $key, ?int $default = null): ?int
    {
        $v = $this->input($key);
        return is_numeric($v) ? (int) $v : $default;
    }
}
