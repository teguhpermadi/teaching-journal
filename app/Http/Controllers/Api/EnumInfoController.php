<?php

namespace App\Http\Controllers\Api;

use App\GenderEnum;
use App\SemesterEnum;
use App\StatusAttendanceEnum;
use App\TeachingStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class EnumInfoController extends Controller
{
    /** GET /enums — daftar semua enum tersedia */
    public function index(): JsonResponse
    {
        return response()->json([
            'enums' => [
                'semester'         => $this->enumMeta(SemesterEnum::class),
                'teaching-status'  => $this->enumMeta(TeachingStatusEnum::class),
                'attendance-status'=> $this->enumMeta(StatusAttendanceEnum::class),
                'gender'           => $this->enumMeta(GenderEnum::class),
            ],
        ]);
    }

    /** GET /enums/semester */
    public function semester(): JsonResponse
    {
        return response()->json($this->enumMeta(SemesterEnum::class));
    }

    /** GET /enums/teaching-status */
    public function teachingStatus(): JsonResponse
    {
        return response()->json($this->enumMeta(TeachingStatusEnum::class));
    }

    /** GET /enums/attendance-status */
    public function attendanceStatus(): JsonResponse
    {
        return response()->json($this->enumMeta(StatusAttendanceEnum::class));
    }

    /** GET /enums/gender */
    public function gender(): JsonResponse
    {
        return response()->json($this->enumMeta(GenderEnum::class));
    }

    /**
     * Build meta info for any BackedEnum implementing HasLabel / HasColor.
     */
    private function enumMeta(string $enumClass): array
    {
        $cases = [];
        foreach ($enumClass::cases() as $case) {
            $info = [
                'value' => $case->value,
                'label' => method_exists($case, 'getLabel') ? $case->getLabel() : $case->name,
            ];
            if (method_exists($case, 'getColor')) {
                $info['color'] = $case->getColor();
            }
            $cases[] = $info;
        }

        return [
            'enum'  => $enumClass,
            'cases' => $cases,
        ];
    }
}
