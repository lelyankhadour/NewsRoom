<?php

namespace App\Services;

use App\Contracts\ArticleRepositoryInterface;
use App\Jobs\SendArticleNotificationJob;
use App\Models\Article;
use App\Enums\ArticleStatus;
use App\Events\ArticlePublished;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleService
{
    protected ArticleRepositoryInterface $repository;

    public function __construct(ArticleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getPublishedArticles(int $perPage = 15): mixed
    {
        return $this->repository->getAllPublished($perPage);
    }

    public function createArticle(array $data, int $userId): Article
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['user_id'] = $userId;
            $tagIds = $data['tags'] ?? [];
            $attachments = $data['attachments'] ?? [];
            
            unset($data['tags'], $data['attachments']);

            /** @var Article $article */
            $article = $this->repository->create($data);

            if (!empty($tagIds)) {
                $this->repository->syncTags($article, $tagIds);
            }




              

foreach ($attachments as $file) {
//                 $path = $file->store( 'public');
    $path = $file->store('attachments', 'public');

    $article->attachments()->create([
        'file_path' => $path, // attachments/hashname.jpg
        'file_name' => $file->getClientOriginalName(),
        'file_type' => $file->getClientMimeType(),
        'file_size' => $file->getSize(),
    ]);
}

            event(new ArticlePublished($article));
            
            return $article;
        });
    }

    public function updateArticle(Article $article, array $data): Article
    { 
        return DB::transaction(function () use ($article, $data) {
            $oldStatus = $article->status;

            $this->repository->update($article, $data);

            if (isset($data['tags'])) {
                $this->repository->syncTags($article, $data['tags']);
            }
            
               if (isset($data['attachments'])) {
                foreach ($article->attachments as $oldAttachment) {
                    Storage::disk('public')->delete($oldAttachment->file_path);
                }
                $article->attachments()->delete();

                foreach ($data['attachments'] as $file) {
                    $path = $file->store('articles/attachments', 'public');
          
$article->attachments()->create([
    'file_path' => $path,
    'file_name' => $file->getClientOriginalName(),
    'file_type' => $file->getClientMimeType(), 
    'file_size' => $file->getSize(),           
]);
                }
            }
            
            $article->refresh(); 
            
            if (isset($data['status']) && $data['status'] === ArticleStatus::PUBLISHED->value && $oldStatus !== ArticleStatus::PUBLISHED->value) {
                event(new ArticlePublished($article));
            }

            return $article; 
        });
    }

    public function deleteArticle(Article $article): bool
    {
        return DB::transaction(function () use ($article) {

            foreach ($article->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            
            $this->repository->detachTags($article);
            return $this->repository->delete($article);
        });
    }
}