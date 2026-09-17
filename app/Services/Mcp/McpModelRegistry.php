<?php

namespace App\Services\Mcp;

use App\Models\AcademicCalendar;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Journal;
use App\Models\LessonPlan;
use App\Models\MainTarget;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\Signature;
use App\Models\SocialiteUser;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Target;
use App\Models\Transcript;
use App\Models\TranscriptStudent;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class McpModelRegistry
{
    /**
     * @return array<string, class-string<Model>>
     */
    public function models(): array
    {
        return [
            'academic_calendars' => AcademicCalendar::class,
            'academic_years' => AcademicYear::class,
            'attendances' => Attendance::class,
            'grades' => Grade::class,
            'journals' => Journal::class,
            'lesson_plans' => LessonPlan::class,
            'main_targets' => MainTarget::class,
            'permissions' => Permission::class,
            'roles' => Role::class,
            'schedules' => Schedule::class,
            'signatures' => Signature::class,
            'socialite_users' => SocialiteUser::class,
            'students' => Student::class,
            'subjects' => Subject::class,
            'targets' => Target::class,
            'transcripts' => Transcript::class,
            'transcript_students' => TranscriptStudent::class,
            'users' => User::class,
        ];
    }

    /** @return class-string<Model> */
    public function classFor(string $name): string
    {
        $model = $this->models()[$name] ?? null;

        if ($model === null) {
            throw new InvalidArgumentException("Unknown MCP model: {$name}");
        }

        return $model;
    }

    public function new(string $name): Model
    {
        $class = $this->classFor($name);

        return new $class();
    }
}
