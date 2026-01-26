<?php

namespace App\Services\Reviews\Drivers\Contracts;

interface ReviewSource
{
    /**
     * @param array{product_id?:int|string|null,language?:string|null,limit?:int} $opts
     * @return array<int, array{
     *   rating:int, title?:string, body:string,
     *   author_name?:string, date?:string, product_id?:int|string, provider_review_id?:string
     * }>
     */
    public function fetch(array $opts = []): array;
}
