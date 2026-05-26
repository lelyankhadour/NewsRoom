<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Profile;
use App\Models\Article;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\Attachment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. إنشاء حساب مدير ثابت وتوليد الملف الشخصي له
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@news.com',
            'password' => bcrypt('password'),
            "role"=>UserRole::ADMIN,
        ]);
        
        Profile::factory()->create(['user_id' => $admin->id]);

        // 2. إنشاء مجموعة مستخدمين (كتاب ومحررين) مع ملفاتهم الشخصية
        $users = User::factory(15)->create()->each(function ($user) {
            Profile::factory()->create(['user_id' => $user->id]);
        });

        // دمج كل المستخدمين في مصفوفة واحدة لاستخدامهم لاحقاً في التعليقات
        $allUsers = $users->concat([$admin]);

        // 3. إنشاء الوسوم (Tags)
        $tags = Tag::factory(10)->create();

        // 4. إنشاء المقالات وتطبيق العلاقات متعددة الأشكال (Morph)
        Article::factory(30)->create([
            // توزيع المقالات بشكل عشوائي على المستخدمين المنشأين سابقاً
            'user_id' => fn() => $allUsers->random()->id
        ])->each(function ($article) use ($tags, $allUsers) {
            
            // أ. ربط المقال بوسوم عشوائية (Polymorphic Many-to-Many)
            // نستخدم ميزة الـ attach المدمجة في لارافيل للتعامل مع جدول الـ pivot المورف
            $article->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );

            // ب. إنشاء مرفقات للمقال (Polymorphic One-to-Many / Attachments)
            Attachment::factory(rand(1, 2))->create([
                'attachable_id' => $article->id,
                'attachable_type' => Article::class, // أو استخدام الـ Morph Map إذا تم تعريفها
            ]);

            // ج. إنشاء تعليقات على المقال (Polymorphic One-to-Many / Comments)
            Comment::factory(rand(2, 5))->create([
                'commentable_id' => $article->id,
                'commentable_type' => Article::class,
                'user_id' => fn() => $allUsers->random()->id // كاتب التعليق مستخدم عشوائي
            ]);
        });

        // 5. إضافة تعليقات ومرفقات للملفات الشخصية (إذا كان التصميم يسمح بذلك)
        // هذا مجرد تأكيد لتغطية الـ Polymorphic بالكامل
        $allUsers->each(function ($user) use ($allUsers) {
            // إضافة مرفق للملف الشخصي (مثل صورة الحساب الافتراضية)
            Attachment::factory()->create([
                'attachable_id' => $user->profile->id,
                'attachable_type' => Profile::class,
            ]);
        });
    }
}
