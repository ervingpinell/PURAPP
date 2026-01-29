<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplateContent extends Model
{
    protected $fillable = [
        'email_template_id',
        'locale',
        'subject',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Get the template that owns this content.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }

    /**
     * Replace variables in content with actual data.
     *
     * @param array $data Key-value pairs for variable replacement
     * @return array Content with variables replaced
     */
    public function replaceVariables(array $data): array
    {
        $content = $this->content;

        foreach ($content as $key => $value) {
            $content[$key] = $this->replaceVariablesInString($value, $data);
        }

        return $content;
    }

    /**
     * Replace variables in subject line.
     */
    public function getSubjectWithVariables(array $data): string
    {
        return $this->replaceVariablesInString($this->subject, $data);
    }

    /**
     * Replace {{variable}} placeholders in a string.
     */
    protected function replaceVariablesInString(string $text, array $data): string
    {
        foreach ($data as $key => $value) {
            // Convert value to string if it's not already
            $stringValue = is_scalar($value) ? (string) $value : '';

            // Replace {{key}} with value
            $text = str_replace('{{' . $key . '}}', $stringValue, $text);
        }

        return $text;
    }

    /**
     * Get all available variables for templates.
     */
    public static function getAvailableVariables(): array
    {
        return [
            'customer_name' => 'Customer\'s full name',
            'customer_email' => 'Customer\'s email address',
            'booking_reference' => 'Booking reference number',
            'booking_id' => 'Booking ID',
            'product_name' => 'Name of the product',
            'product_date' => 'Date of the product (formatted)',
            'product_time' => 'Time of the product',
            'product_language' => 'Product language',
            'total_amount' => 'Total booking amount (formatted)',
            'subtotal' => 'Subtotal amount',
            'taxes' => 'Tax amount',
            'payment_url' => 'Link to payment page',
            'booking_url' => 'Link to booking details',
            'company_name' => 'Green Vacations CR',
            'support_email' => 'Support email address',
            'company_phone' => 'Company phone number',
            'app_url' => 'Website URL',
        ];
    }
}
