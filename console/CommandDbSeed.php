<?php
namespace SkillDo\DevTool\Console;

use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;
use SkillDo\Http\Http;
use SkillDo\Model\Post;
use SkillDo\Model\PostCategory;

class CommandDbSeed extends Command {

    protected string $signature = 'db:seed {--module=} {number=10}';

    protected string $description = 'Generates a random fake database';

    public function handle(): bool
    {
        $module = $this->option('module');

        $number = $this->argument('number');

        if($number > 50) {

            $this->line('Số lượng tối đa là 50');

            return self::ERROR;
        }

        if($number < 1) {

            $this->line('Số lượng tối thiểu là 1');

            return self::ERROR;
        }

        if($module == 'post') {
            return $this->generatePost($number);
        }

        $this->line('Module is required');

        return self::ERROR;
    }

    public function generatePost($number)
    {
        $response = Http::withOptions(['verify' => false])->get('https://cms.sikido.vn/api/cms/data-fake/post');

        $postsFaker = $response->object();

        $numberPost = 0;

        if(!empty($postsFaker->data)) {

            $postsFaker = $postsFaker->data;

            $categories = PostCategory::where('cate_type', 'post_categories')->fetch();

            if (have_posts($categories)) {

                foreach ($categories as $category) {

                    for ($i = 0; $i < $number; $i++) {
                        $post = [
                            'title'     => $postsFaker->title[array_rand($postsFaker->title, 1)],
                            'excerpt'   => $postsFaker->description[array_rand($postsFaker->description, 1)],
                            'image'     => $postsFaker->images[array_rand($postsFaker->images, 1)],
                            'post_type' => 'post',
                            'taxonomies'=> ['post_categories' => [$category->id]]
                        ];
                        if (!is_skd_error(Post::insert($post))) {
                            $numberPost++;
                        }
                    }
                }
            }
            else {

                for ($i = 0; $i < $number; $i++) {
                    $post = [
                        'title' => $postsFaker->title[array_rand($postsFaker->title, 1)],
                        'excerpt' => $postsFaker->description[array_rand($postsFaker->description, 1)],
                        'image' => $postsFaker->images[array_rand($postsFaker->images, 1)],
                        'post_type' => 'post',
                    ];

                    if (!is_skd_error(Post::insert($post))) {
                        $numberPost++;
                    }
                }
            }

            if($numberPost != 0) {
                $this->line(function (Message $message) use ($numberPost) {
                    $message->line('Đã thêm thành công');
                    $message->line($numberPost, 'green', true);
                    $message->line('bài viết');
                });

                return self::SUCCESS;
            }

            $this->line('Không tạo được bài viết nào');

            return self::ERROR;
        }

        $this->line('Không lấy được dữ liệu từ server');

        return self::ERROR;
    }
}