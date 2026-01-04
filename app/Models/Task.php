<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'status',
        'due_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
    }

    /**
     * タスクが属するユーザー（多対1）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このタスクが属するカテゴリー
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * このタスクに付いているタグ
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * ログインユーザーのタスクのみを取得するスコープ
     */
    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', Auth::id());
    }

    /**
     * 完了したタスクのみを取得するスコープ
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * 未完了のタスクのみを取得するスコープ
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
