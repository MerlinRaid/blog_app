<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'body',      
        'status',
        'published_at',
        'featured_image',
        'reading_time',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];


    //Seosed
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


    //Abi funktsioonid
    //Abi scope funktsioonid avalikule loendile
    public function scopePublic($q)
    {
        return $q->where('status', 'published')
                 ->whereNotNull('published_at')
                 ->where('published_at', '<=', now());
    }

    /**
     * Pildi üleslaadimise abi funktsioon
     * Salvestame ainult suhtelise tee, storage/app/public/ nt: posts/abc123.jpd
     * @return string|null
     */
    public function featuredImageUrl() : ?string
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    //Admin vaate jaoks
    public function scopeNeedsAction($q)
    {
        return $q->whereIn('status', ['draft', 'review']);
    }

    public function scopeScheduled($q)
    {
        return $q->where('status', 'published')
                 ->where('published_at', '>', now());
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')
                 ->where('published_at', '<=', now());
    }

}
