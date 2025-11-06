<?php

namespace App\Services\Policies;

class PolicySnapshotService
{
    /**
     * Lee config('policies'), arma el snapshot y calcula un sha256 determinístico.
     */
    public function make(): array
    {
        $pol = config('policies');

        $snapshot = [
            'terms'        => (string) ($pol['terms_full'] ?? ''),
            'privacy'      => (string) ($pol['privacy_full'] ?? ''),
            'cancellation' => (string) ($pol['cancellation_full'] ?? ''),
            'refunds'      => (string) ($pol['refunds_full'] ?? ''),
            'warranty'     => (string) ($pol['warranty_full'] ?? ''),
            'payments'     => (string) ($pol['payment_methods_full'] ?? ''),
        ];

        // normalizamos espacios para evitar hashes distintos por minucias
        $normalized = preg_replace('/\s+/', ' ', implode('|', $snapshot)) ?? '';
        $sha = hash('sha256', $normalized);

        return [
            'versions' => [
                'terms'   => (string) ($pol['versions']['terms'] ?? 'v1'),
                'privacy' => (string) ($pol['versions']['privacy'] ?? 'v1'),
            ],
            'snapshot' => $snapshot,
            'sha256'   => $sha,
        ];
    }
}
