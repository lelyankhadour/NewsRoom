<?php

namespace App\Http\Resources\Api\V2;

use App\Http\Resources\Api\V1\ArticleV1Resource;
use Illuminate\Http\Request;

class ArticleV2Resource extends ArticleV1Resource
{
    public function toArray(Request $request): array
    {
        // جلب البيانات الأساسية من الـ V1 منعاً لتكرار الكود
        $baseData = parent::toArray($request);

        // دمج البيانات الإضافية المطلوبة للـ Mobile App
        return array_merge($baseData, [
            'reading_time'   => $this->reading_time ?? 5, // بالدقائق
            'tags'           => $this->tags?->pluck('name')->toArray() ?? [], // الوسوم
            'comments_count' => $this->comments?->count() ?? 0, // عدد التعليقات
        ]);
    }
}