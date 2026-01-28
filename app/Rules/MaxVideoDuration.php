<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use FFMpeg\FFProbe;

class MaxVideoDuration implements Rule
{
    protected $maxSeconds;
    protected $actualDuration;

    public function __construct(int $maxSeconds)
    {
        $this->maxSeconds = $maxSeconds;
    }

    public function passes($attribute, $value)
    {
        if (!$value instanceof \Illuminate\Http\UploadedFile) {
            return false;
        }

        try {
            $ffprobe = FFProbe::create([
                'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
                'ffprobe.binaries' => config('media-library.ffprobe_path'),
            ]);

            $this->actualDuration = $ffprobe
                ->format($value->getRealPath())
                ->get('duration');

            return $this->actualDuration <= $this->maxSeconds;
        } catch (\Exception $e) {
            \Log::error('Video duration validation failed', [
                'error' => $e->getMessage(),
                'file' => $value->getClientOriginalName(),
            ]);
            return false;
        }
    }

    public function message()
    {
        $maxMinutes = round($this->maxSeconds / 60, 1);
        $actualMinutes = round($this->actualDuration / 60, 1);
        
        return "El video no debe durar más de {$maxMinutes} minutos (duración actual: {$actualMinutes} min).";
    }
}
