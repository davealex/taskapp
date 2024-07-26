<?php

namespace App\Models;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use App\Traits\AddUserIdAttribute;
use App\Traits\AddUuidRefAttribute;
use App\Traits\UseRefAsRouteKeyNameTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory, AddUuidRefAttribute, AddUserIdAttribute, UseRefAsRouteKeyNameTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status' => TaskStatusEnum::class,
        'priority' => TaskPriorityEnum::class
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'preview_texts'
    ];

    /**
     * Add snippet of description attribute
     *
     * @return Attribute
     */
    public function previewTexts(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::of($this->attributes['description'])->limit(),
        );
    }

    /**
     * Task publisher
     *
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
