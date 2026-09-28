<?php

use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\EnumInfoController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\MainTargetController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SignatureController;
use App\Http\Controllers\Api\SocialiteUserController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TargetController;
use App\Http\Controllers\Api\TranscriptController;
use App\Http\Controllers\Api\TranscriptStudentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\McpAuthController;
use App\Http\Middleware\AuthenticateMcp;
use Illuminate\Support\Facades\Route;

/* ── MCP Auth ─────────────────────────────────── */

Route::post('/mcp/login', [McpAuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('mcp.login');

Route::post('/mcp/logout', [McpAuthController::class, 'logout'])
    ->middleware(AuthenticateMcp::class)
    ->name('mcp.logout');

/* ── Enum Info (public) ──────────────────── */

Route::get('/enums', [EnumInfoController::class, 'index']);
Route::get('/enums/semester', [EnumInfoController::class, 'semester']);
Route::get('/enums/teaching-status', [EnumInfoController::class, 'teachingStatus']);
Route::get('/enums/attendance-status', [EnumInfoController::class, 'attendanceStatus']);
Route::get('/enums/gender', [EnumInfoController::class, 'gender']);

/* ── Authenticated API group ──────────────────── */

Route::middleware(AuthenticateMcp::class)->group(function () {

    /* ── Academic Years ──────────────────────── */
    Route::get('/academic-years', [AcademicYearController::class, 'index']);
    Route::post('/academic-years', [AcademicYearController::class, 'store']);
    Route::get('/academic-years/{id}', [AcademicYearController::class, 'show']);
    Route::put('/academic-years/{id}', [AcademicYearController::class, 'update']);
    Route::delete('/academic-years/{id}', [AcademicYearController::class, 'destroy']);
    Route::put('/academic-years/{id}/restore', [AcademicYearController::class, 'restore']);
    Route::post('/academic-years/{id}/activate', [AcademicYearController::class, 'activate']);

    /* ── Grades ──────────────────────────────── */
    Route::get('/grades', [GradeController::class, 'index']);
    Route::post('/grades', [GradeController::class, 'store']);
    Route::get('/grades/{id}', [GradeController::class, 'show']);
    Route::put('/grades/{id}', [GradeController::class, 'update']);
    Route::delete('/grades/{id}', [GradeController::class, 'destroy']);
    Route::put('/grades/{id}/restore', [GradeController::class, 'restore']);

    /* ── Subjects ────────────────────────────── */
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::post('/subjects', [SubjectController::class, 'store']);
    Route::get('/subjects/{id}', [SubjectController::class, 'show']);
    Route::put('/subjects/{id}', [SubjectController::class, 'update']);
    Route::delete('/subjects/{id}', [SubjectController::class, 'destroy']);
    Route::put('/subjects/{id}/restore', [SubjectController::class, 'restore']);

    /* ── Students ────────────────────────────── */
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::put('/students/{id}', [StudentController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    Route::put('/students/{id}/restore', [StudentController::class, 'restore']);

    /* ── Journals ────────────────────────────── */
    Route::get('/journals', [JournalController::class, 'index']);
    Route::get('/journals/my', [JournalController::class, 'myJournals']);
    Route::post('/journals', [JournalController::class, 'store']);
    Route::get('/journals/{id}', [JournalController::class, 'show']);
    Route::put('/journals/{id}', [JournalController::class, 'update']);
    Route::delete('/journals/{id}', [JournalController::class, 'destroy']);
    Route::put('/journals/{id}/restore', [JournalController::class, 'restore']);

    /* ── Main Targets ────────────────────────── */
    Route::get('/main-targets', [MainTargetController::class, 'index']);
    Route::post('/main-targets', [MainTargetController::class, 'store']);
    Route::get('/main-targets/{id}', [MainTargetController::class, 'show']);
    Route::put('/main-targets/{id}', [MainTargetController::class, 'update']);
    Route::delete('/main-targets/{id}', [MainTargetController::class, 'destroy']);
    Route::put('/main-targets/{id}/restore', [MainTargetController::class, 'restore']);

    /* ── Targets ─────────────────────────────── */
    Route::get('/targets', [TargetController::class, 'index']);
    Route::post('/targets', [TargetController::class, 'store']);
    Route::get('/targets/{id}', [TargetController::class, 'show']);
    Route::put('/targets/{id}', [TargetController::class, 'update']);
    Route::delete('/targets/{id}', [TargetController::class, 'destroy']);
    Route::put('/targets/{id}/restore', [TargetController::class, 'restore']);

    /* ── Transcripts ─────────────────────────── */
    Route::get('/transcripts', [TranscriptController::class, 'index']);
    Route::post('/transcripts', [TranscriptController::class, 'store']);
    Route::get('/transcripts/{id}', [TranscriptController::class, 'show']);
    Route::put('/transcripts/{id}', [TranscriptController::class, 'update']);
    Route::delete('/transcripts/{id}', [TranscriptController::class, 'destroy']);
    Route::put('/transcripts/{id}/restore', [TranscriptController::class, 'restore']);

    /* ── Transcript Students ─────────────────── */
    Route::get('/transcript-students', [TranscriptStudentController::class, 'index']);
    Route::post('/transcript-students', [TranscriptStudentController::class, 'store']);
    Route::get('/transcript-students/{id}', [TranscriptStudentController::class, 'show']);
    Route::put('/transcript-students/{id}', [TranscriptStudentController::class, 'update']);
    Route::delete('/transcript-students/{id}', [TranscriptStudentController::class, 'destroy']);
    Route::put('/transcript-students/{id}/restore', [TranscriptStudentController::class, 'restore']);

    /* ── Signatures ──────────────────────────── */
    Route::get('/signatures', [SignatureController::class, 'index']);
    Route::post('/signatures', [SignatureController::class, 'upload']);
    Route::get('/signatures/{id}', [SignatureController::class, 'show']);
    Route::put('/signatures/{id}', [SignatureController::class, 'update']);
    Route::delete('/signatures/{id}', [SignatureController::class, 'destroy']);
    Route::put('/signatures/{id}/restore', [SignatureController::class, 'restore']);

    /* ── Attendance ──────────────────────────── */
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::get('/attendance/{id}', [AttendanceController::class, 'show']);
    Route::put('/attendance/{id}', [AttendanceController::class, 'update']);
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy']);
    Route::put('/attendance/{id}/restore', [AttendanceController::class, 'restore']);
    Route::get('/attendance/student/{studentId}', [AttendanceController::class, 'studentAttendance']);

    /* ── Users ───────────────────────────────── */
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::get('/users/me', [UserController::class, 'me']);

    /* ── MCP Tokens ──────────────────────────── */

    /* ── Permissions (read-only) ─────────────── */
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/permissions/{id}', [PermissionController::class, 'show']);

    /* ── Roles (read-only) ───────────────────── */
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);

    /* ── Socialite Users ─────────────────────── */
    Route::get('/socialite-users', [SocialiteUserController::class, 'index']);
    Route::post('/socialite-users', [SocialiteUserController::class, 'store']);
    Route::get('/socialite-users/{id}', [SocialiteUserController::class, 'show']);
    Route::put('/socialite-users/{id}', [SocialiteUserController::class, 'update']);
    Route::delete('/socialite-users/{id}', [SocialiteUserController::class, 'destroy']);
    Route::put('/socialite-users/{id}/restore', [SocialiteUserController::class, 'restore']);
});