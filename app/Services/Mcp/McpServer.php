<?php

namespace App\Services\Mcp;

use App\Models\Journal;
use App\Models\MainTarget;
use App\Models\Target;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Throwable;

class McpServer
{
    public function __construct(private readonly McpModelRegistry $registry)
    {
    }

    public function handle(array $request): ?array
    {
        $id = $request['id'] ?? null;
        $method = $request['method'] ?? null;

        if (! is_string($method)) {
            return $this->error($id, -32600, 'Invalid JSON-RPC request.');
        }

        if ($method === 'notifications/initialized') {
            return null;
        }

        try {
            return match ($method) {
                'initialize' => $this->initialize($id),
                'ping' => $this->result($id, []),
                'tools/list' => $this->result($id, ['tools' => $this->tools()]),
                'tools/call' => $this->callTool($id, $request['params'] ?? []),
                'list_models',
                'describe_model',
                'list_records',
                'get_record',
                'create_record',
                'update_record',
                'delete_record' => $this->callDirectTool($id, $method, $request['params'] ?? []),
                default => $this->error($id, -32601, "Method not found: {$method}"),
            };
        } catch (InvalidArgumentException $exception) {
            return $this->error($id, -32602, $exception->getMessage());
        } catch (QueryException $exception) {
            Log::error('MCP database operation failed.', ['exception' => $exception]);

            return $this->error($id, -32000, 'Database operation failed.');
        } catch (Throwable $exception) {
            Log::error('MCP request failed.', ['exception' => $exception]);

            return $this->error($id, -32000, 'MCP request failed.');
        }
    }

    private function initialize(mixed $id): array
    {
        return $this->result($id, [
            'protocolVersion' => '2025-06-18',
            'capabilities' => ['tools' => ['listChanged' => false]],
            'serverInfo' => [
                'name' => config('app.name', 'Teaching Journal') . ' MCP Server',
                'version' => '1.0.0',
            ],
        ]);
    }

    /** @return list<array<string, mixed>> */
    private function tools(): array
    {
        return [
            [
                'name' => 'list_models',
                'description' => 'List all Eloquent models available through this MCP server.',
                'inputSchema' => ['type' => 'object', 'properties' => [], 'additionalProperties' => false],
            ],
            [
                'name' => 'describe_model',
                'description' => 'Describe an available model, its columns, fillable fields, and relationships.',
                'inputSchema' => $this->modelSchema(['model' => ['type' => 'string']]),
            ],
            [
                'name' => 'list_records',
                'description' => 'List records with exact-match filters and pagination.',
                'inputSchema' => $this->modelSchema([
                    'model' => ['type' => 'string'],
                    'filters' => ['type' => 'object'],
                    'page' => ['type' => 'integer', 'minimum' => 1],
                    'per_page' => ['type' => 'integer', 'minimum' => 1],
                    'with_trashed' => ['type' => 'boolean'],
                ], ['model']),
            ],
            [
                'name' => 'get_record',
                'description' => 'Get one record by its primary key.',
                'inputSchema' => $this->modelSchema([
                    'model' => ['type' => 'string'],
                    'id' => ['type' => ['string', 'integer']],
                    'with_trashed' => ['type' => 'boolean'],
                ], ['model', 'id']),
            ],
            [
                'name' => 'create_record',
                'description' => 'Create a record using the model fillable fields.',
                'inputSchema' => $this->modelSchema([
                    'model' => ['type' => 'string'],
                    'data' => ['type' => 'object'],
                ], ['model', 'data']),
            ],
            [
                'name' => 'update_record',
                'description' => 'Update a record using the model fillable fields.',
                'inputSchema' => $this->modelSchema([
                    'model' => ['type' => 'string'],
                    'id' => ['type' => ['string', 'integer']],
                    'data' => ['type' => 'object'],
                ], ['model', 'id', 'data']),
            ],
            [
                'name' => 'delete_record',
                'description' => 'Delete a record. Soft-deleted models remain recoverable by the application.',
                'inputSchema' => $this->modelSchema([
                    'model' => ['type' => 'string'],
                    'id' => ['type' => ['string', 'integer']],
                ], ['model', 'id']),
            ],
        ];
    }

    private function callTool(mixed $id, mixed $params): array
    {
        if (! is_array($params) || ! is_string($params['name'] ?? null)) {
            return $this->error($id, -32602, 'Tool name is required.');
        }

        $arguments = $params['arguments'] ?? [];
        if (! is_array($arguments)) {
            return $this->error($id, -32602, 'Tool arguments must be an object.');
        }

        $name = $params['name'];
        $data = match ($name) {
            'list_models' => $this->listModels(),
            'describe_model' => $this->describeModel($arguments),
            'list_records' => $this->listRecords($arguments),
            'get_record' => $this->getRecord($arguments),
            'create_record' => $this->mutate(fn () => $this->createRecord($arguments)),
            'update_record' => $this->mutate(fn () => $this->updateRecord($arguments)),
            'delete_record' => $this->mutate(fn () => $this->deleteRecord($arguments)),
            default => throw new InvalidArgumentException("Unknown MCP tool: {$name}"),
        };

        return $this->result($id, [
            'content' => [['type' => 'text', 'text' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]],
            'structuredContent' => $data,
        ]);
    }

    private function callDirectTool(mixed $id, string $name, mixed $arguments): array
    {
        if (! is_array($arguments)) {
            return $this->error($id, -32602, 'Method params must be an object.');
        }

        $data = match ($name) {
            'list_models' => $this->listModels(),
            'describe_model' => $this->describeModel($arguments),
            'list_records' => $this->listRecords($arguments),
            'get_record' => $this->getRecord($arguments),
            'create_record' => $this->mutate(fn () => $this->createRecord($arguments)),
            'update_record' => $this->mutate(fn () => $this->updateRecord($arguments)),
            'delete_record' => $this->mutate(fn () => $this->deleteRecord($arguments)),
        };

        return $this->result($id, [
            'content' => [['type' => 'text', 'text' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]],
            'structuredContent' => $data,
        ]);
    }

    private function listModels(): array
    {
        return ['models' => collect($this->registry->models())->map(
            fn (string $class, string $name): array => ['name' => $name, 'class' => $class]
        )->values()->all()];
    }

    private function describeModel(array $arguments): array
    {
        $model = $this->registry->new($this->requiredString($arguments, 'model'));
        $columns = Schema::getColumnListing($model->getTable());

        return [
            'name' => $arguments['model'],
            'class' => $model::class,
            'table' => $model->getTable(),
            'primary_key' => $model->getKeyName(),
            'fillable' => $model->getFillable(),
            'columns' => $columns,
            'soft_deletes' => in_array(SoftDeletes::class, class_uses_recursive($model), true),
        ];
    }

    private function listRecords(array $arguments): array
    {
        $model = $this->registry->new($this->requiredString($arguments, 'model'));
        $query = $model->newQuery();
        $columns = Schema::getColumnListing($model->getTable());
        $filters = $arguments['filters'] ?? [];

        if (! is_array($filters)) {
            throw new InvalidArgumentException('filters must be an object.');
        }

        foreach ($filters as $column => $value) {
            if (! is_string($column) || ! in_array($column, $columns, true) || is_array($value)) {
                throw new InvalidArgumentException("Invalid filter column: {$column}");
            }
            $query->where($column, $value);
        }

        if (($arguments['with_trashed'] ?? false) === true) {
            $this->withTrashed($model, $query);
        }

        $perPage = min(
            max((int) ($arguments['per_page'] ?? 25), 1),
            max((int) config('mcp.max_page_size', 100), 1)
        );

        $page = max((int) ($arguments['page'] ?? 1), 1);
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->getCollection()->map(fn (Model $record): array => $this->serialize($record))->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    private function getRecord(array $arguments): array
    {
        $model = $this->registry->new($this->requiredString($arguments, 'model'));
        $query = $model->newQuery();
        $this->withTrashedIfRequested($model, $query, $arguments);
        $record = $query->find($arguments['id'] ?? null);

        if (! $record) {
            throw new InvalidArgumentException('Record not found.');
        }

        return $this->serialize($record);
    }

    private function createRecord(array $arguments): array
    {
        $model = $this->registry->new($this->requiredString($arguments, 'model'));
        $data = $this->validatedData($model, $arguments['data'] ?? null);
        $this->validateModelRelations($model, $data);
        $record = $model->create($data);

        return $this->serialize($record->fresh());
    }

    private function updateRecord(array $arguments): array
    {
        $model = $this->registry->new($this->requiredString($arguments, 'model'));
        $record = $model->newQuery()->find($arguments['id'] ?? null);

        if (! $record) {
            throw new InvalidArgumentException('Record not found.');
        }

        $data = $this->validatedData($model, $arguments['data'] ?? null);
        $this->validateModelRelations($record, $data);
        $record->update($data);

        return $this->serialize($record->fresh());
    }

    private function deleteRecord(array $arguments): array
    {
        $model = $this->registry->new($this->requiredString($arguments, 'model'));
        $record = $model->newQuery()->find($arguments['id'] ?? null);

        if (! $record) {
            throw new InvalidArgumentException('Record not found.');
        }

        $record->delete();

        return ['deleted' => true, 'id' => $arguments['id']];
    }

    private function mutate(callable $callback): array
    {
        if (! (bool) config('mcp.allow_mutations', false)) {
            throw new InvalidArgumentException('MCP mutations are disabled. Set MCP_ALLOW_MUTATIONS=true to enable them.');
        }

        return $callback();
    }

    private function validatedData(Model $model, mixed $data): array
    {
        if (! is_array($data)) {
            throw new InvalidArgumentException('data must be an object.');
        }

        $fillable = $model->getFillable();
        $unknown = array_diff(array_keys($data), $fillable);

        if ($unknown !== []) {
            throw new InvalidArgumentException('Unknown or non-fillable fields: ' . implode(', ', $unknown));
        }

        return $data;
    }

    private function validateModelRelations(Model $model, array $data): void
    {
        $mapping = [
            \App\Models\Journal::class => [
                'main_target_id' => ['class' => MainTarget::class, 'array' => true, 'context' => true],
                'target_id' => ['class' => Target::class, 'array' => true, 'context' => true, 'belongs_to_main' => true],
                'academic_year_id' => ['class' => \App\Models\AcademicYear::class, 'array' => false],
                'subject_id' => ['class' => \App\Models\Subject::class, 'array' => false],
                'grade_id' => ['class' => \App\Models\Grade::class, 'array' => false],
                'user_id' => ['class' => \App\Models\User::class, 'array' => false],
            ],
            \App\Models\Target::class => [
                'main_target_id' => ['class' => MainTarget::class, 'array' => false, 'context' => true],
                'academic_year_id' => ['class' => \App\Models\AcademicYear::class, 'array' => false],
                'subject_id' => ['class' => \App\Models\Subject::class, 'array' => false],
                'grade_id' => ['class' => \App\Models\Grade::class, 'array' => false],
                'user_id' => ['class' => \App\Models\User::class, 'array' => false],
            ],
            \App\Models\MainTarget::class => [
                'academic_year_id' => ['class' => \App\Models\AcademicYear::class, 'array' => false],
                'subject_id' => ['class' => \App\Models\Subject::class, 'array' => false],
                'grade_id' => ['class' => \App\Models\Grade::class, 'array' => false],
                'user_id' => ['class' => \App\Models\User::class, 'array' => false],
            ],
            \App\Models\LessonPlan::class => [
                'target_id' => ['class' => Target::class, 'array' => false, 'context' => true],
                'academic_year_id' => ['class' => \App\Models\AcademicYear::class, 'array' => false],
                'subject_id' => ['class' => \App\Models\Subject::class, 'array' => false],
                'grade_id' => ['class' => \App\Models\Grade::class, 'array' => false],
                'user_id' => ['class' => \App\Models\User::class, 'array' => false],
            ],
            \App\Models\Attendance::class => [
                'student_id' => ['class' => \App\Models\Student::class, 'array' => false],
            ],
            \App\Models\TranscriptStudent::class => [
                'transcript_id' => ['class' => \App\Models\Transcript::class, 'array' => false],
                'academic_year_id' => ['class' => \App\Models\AcademicYear::class, 'array' => false],
                'subject_id' => ['class' => \App\Models\Subject::class, 'array' => false],
                'grade_id' => ['class' => \App\Models\Grade::class, 'array' => false],
                'student_id' => ['class' => \App\Models\Student::class, 'array' => false],
            ],
            \App\Models\Transcript::class => [
                'grade_id' => ['class' => \App\Models\Grade::class, 'array' => false],
                'subject_id' => ['class' => \App\Models\Subject::class, 'array' => false],
                'journal_id' => ['class' => \App\Models\Journal::class, 'array' => false],
                'academic_year_id' => ['class' => \App\Models\AcademicYear::class, 'array' => false],
                'user_id' => ['class' => \App\Models\User::class, 'array' => false],
            ],
            \App\Models\Signature::class => [
                'journal_id' => ['class' => \App\Models\Journal::class, 'array' => false],
                'signer_id' => ['class' => \App\Models\User::class, 'array' => false],
            ],
            \App\Models\Schedule::class => [
                'subject_id' => ['class' => \App\Models\Subject::class, 'array' => false],
            ],
            \App\Models\Subject::class => [
                'user_id' => ['class' => \App\Models\User::class, 'array' => false],
                'grade_id' => ['class' => \App\Models\Grade::class, 'array' => false],
                'academic_year_id' => ['class' => \App\Models\AcademicYear::class, 'array' => false],
            ],
            \App\Models\Grade::class => [
                'academic_year_id' => ['class' => \App\Models\AcademicYear::class, 'array' => false],
            ],
            \App\Models\AcademicCalendar::class => [
                'user_id' => ['class' => \App\Models\User::class, 'array' => false],
                'academic_year_id' => ['class' => \App\Models\AcademicYear::class, 'array' => false],
            ],
        ];

        $class = get_class($model);
        if (! isset($mapping[$class])) {
            return;
        }

        $map = $mapping[$class];

        $effective = [];
        // seed effective with existing attributes (useful for updates)
        foreach (array_keys($map) as $field) {
            $effective[$field] = $model->getAttribute($field);
        }

        // merge incoming data
        $effective = array_merge($effective, $data);

        $contextFields = ['academic_year_id', 'subject_id', 'grade_id', 'user_id'];

        foreach ($map as $field => $info) {
            if (! array_key_exists($field, $effective) || $effective[$field] === null) {
                continue;
            }

            $targetClass = $info['class'];
            $isArray = $info['array'] ?? false;

            if ($isArray) {
                if (! is_array($effective[$field])) {
                    throw new InvalidArgumentException("{$field} must be an array of IDs.");
                }

                $ids = array_values(array_unique($effective[$field]));
                foreach ($ids as $id) {
                    if (! is_string($id) || $id === '') {
                        throw new InvalidArgumentException("{$field} must contain only non-empty string IDs.");
                    }
                }

                $found = $targetClass::query()->whereIn('id', $ids)->get();
                if ($found->count() !== count($ids)) {
                    throw new InvalidArgumentException("One or more {$field} values do not exist.");
                }

                $relatedRecords = $found;
            } else {
                $id = $effective[$field];
                if (! is_string($id) && ! is_int($id)) {
                    throw new InvalidArgumentException("{$field} must be a string or integer ID.");
                }

                $related = $targetClass::query()->find($id);
                if (! $related) {
                    throw new InvalidArgumentException("{$field} value does not exist.");
                }

                $relatedRecords = collect([$related]);
            }

            // context validation
            if (($info['context'] ?? false) === true) {
                foreach ($relatedRecords as $related) {
                    foreach ($contextFields as $ctx) {
                        if (array_key_exists($ctx, $effective) && $effective[$ctx] !== null) {
                            if ((string) $related->getAttribute($ctx) !== (string) $effective[$ctx]) {
                                throw new InvalidArgumentException(
                                    "{$class} {$ctx} does not match the selected {$field} records."
                                );
                            }
                        }
                    }
                }
            }

            // special: journal target belongs_to_main
            if (($info['belongs_to_main'] ?? false) === true && $class === \App\Models\Journal::class) {
                $mainIds = array_values(array_unique($effective['main_target_id'] ?? []));
                foreach ($relatedRecords as $related) {
                    if (! in_array((string) $related->main_target_id, array_map('strval', $mainIds), true)) {
                        throw new InvalidArgumentException('Every target_id must belong to one of the selected main_target_id values.');
                    }
                }
            }
        }
    }

    private function serialize(Model $record): array
    {
        return $record->toArray();
    }

    private function withTrashedIfRequested(Model $model, $query, array $arguments): void
    {
        if (($arguments['with_trashed'] ?? false) === true) {
            $this->withTrashed($model, $query);
        }
    }

    private function withTrashed(Model $model, $query): void
    {
        if (in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
            $query->withTrashed();
        }
    }

    private function requiredString(array $arguments, string $key): string
    {
        if (! is_string($arguments[$key] ?? null) || $arguments[$key] === '') {
            throw new InvalidArgumentException("{$key} is required.");
        }

        return $arguments[$key];
    }

    private function modelSchema(array $properties, array $required = []): array
    {
        return ['type' => 'object', 'properties' => $properties, 'required' => $required, 'additionalProperties' => false];
    }

    private function result(mixed $id, array $result): array
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'result' => $result];
    }

    private function error(mixed $id, int $code, string $message): array
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'error' => ['code' => $code, 'message' => $message]];
    }
}
