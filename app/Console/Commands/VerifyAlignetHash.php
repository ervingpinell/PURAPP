<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerifyAlignetHash extends Command
{
    protected $signature = 'alignet:verify-hash 
                            {operationNumber : Operation number}
                            {amount : Amount in cents}';

    protected $description = 'Verify Alignet hash generation';

    public function handle()
    {
        $acquirerId = config('payment.gateways.alignet.acquirer_id');
        $commerceId = config('payment.gateways.alignet.commerce_id');
        $secretKey = config('payment.gateways.alignet.secret_key');
        $operationNumber = $this->argument('operationNumber');
        $amount = $this->argument('amount');
        $currency = '840';

        $this->info("🔍 Verificando hash de Alignet\n");

        $string = $acquirerId . $commerceId . $operationNumber . $amount . $currency . $secretKey;

        $this->line("Acquirer ID: {$acquirerId}");
        $this->line("Commerce ID: {$commerceId}");
        $this->line("Operation Number: {$operationNumber}");
        $this->line("Amount (cents): {$amount}");
        $this->line("Currency: {$currency}");
        $this->line("Secret Key: " . substr($secretKey, 0, 10) . "...");
        $this->newLine();

        $this->line("String to hash:");
        $this->info($string);
        $this->newLine();

        $hash = hash('sha512', $string);

        $this->line("Generated Hash (SHA-512):");
        $this->info($hash);
        $this->newLine();

        // Verify length
        if (strlen($hash) !== 128) {
            $this->error("❌ Hash length incorrect! Expected 128, got " . strlen($hash));
        } else {
            $this->info("✅ Hash length correct (128 characters)");
        }

        return 0;
    }
}
