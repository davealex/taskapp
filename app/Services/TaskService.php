<?php

namespace App\Services;

use App\Dtos\TaskDto;
use App\Exceptions\NotTaskAuthorException;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\HigherOrderWhenProxy;

class TaskService
{
    public const DEFAULT_PER_PAGE = 10;

    /**
     * @param TaskDto $data
     * @return Builder|HigherOrderWhenProxy|ParamAwareLengthAwarePaginator
     */
    public function index(TaskDto $data): Builder|\Illuminate\Support\HigherOrderWhenProxy|ParamAwareLengthAwarePaginator
    {
        return Task::query()
            ->with(['author'])
            ->when(
                $author = $data->author,
                fn(Builder $query) => $query->where('user_id', '=', $author),
//                fn(Builder $query) => $query->where('user_id', '=', auth()->id())
            )
            ->when($status = $data->status, fn(Builder $query) => $query->where('status', '=', $status))
            ->when($priority = $data->priority, fn(Builder $query) => $query->where('priority', '=', $priority))
            ->when(
                $search = $data->search,
                fn(Builder $query) => $query->where('title', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%")
            )
            ->when($orderBy = $data->orderBy, fn(Builder $query) => $query->{$orderBy}())
            ->when(
                $perPage = $data->perPage,
                fn(Builder $query) => $query->paginate($perPage),
                fn(Builder $query) => $query->paginate(self::DEFAULT_PER_PAGE)
            );
    }

    /**
     * @param array $values
     * @return Collection
     */
    public static function formatListingOptions(array $values): Collection
    {
        return collect($values)->map(fn ($value) => [
            'label' => ucfirst($value),
            'value' => $value
        ]);
    }

    /**
     * @param string $ref
     * @return Builder|Model
     */
    public function getByRef(string $ref): Builder|Model
    {
        return Task::query()
            ->where('ref', '=', $ref)
            ->firstOrFail()
            ?->load('author');
    }

    /**
     * @param TaskDto $dto
     * @return Model|Builder
     */
    public function create(TaskDto $dto): \Illuminate\Database\Eloquent\Model|Builder
    {
        return Task::query()
            ->create([
                'title' => $dto->title,
                'description' => $dto->description,
                'status' => $dto->status,
                'priority' => $dto->priority
            ])->load('author');
    }

    /**
     * @param string $ref
     * @param TaskDto $dto
     * @return Builder|Model
     * @throws NotTaskAuthorException
     */
    public function update(string $ref, TaskDto $dto): Builder|Model
    {
        $task = $this->getByRef($ref);

        $this->securityCheck($ref);

        return tap($task)->update(array_filter([
                'title' => $dto->title,
                'description' => $dto->description,
                'status' => $dto->status,
                'priority' => $dto->priority
            ]))->load('author');
    }

    /**
     * @param string $ref
     * @return void
     * @throws NotTaskAuthorException
     */
    public function delete(string $ref): void
    {
        $task = $this->getByRef($ref);

        $this->securityCheck($ref);

        $task->delete();
    }

    /**
     * @param string $ref
     * @return bool
     */
    private function taskIsAuthoredByUser(string $ref): bool
    {
        return $this->getByRef($ref)?->user_id === auth()->id();
    }

    /**
     * @param string $ref
     * @return void
     * @throws NotTaskAuthorException
     */
    private function securityCheck(string $ref): void
    {
        if (! $this->taskIsAuthoredByUser($ref)) {
            throw new NotTaskAuthorException('You can only modify tasks you authored.', 403);
        }
    }
}
