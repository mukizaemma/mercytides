<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Background;
use App\Models\Team;
use App\Support\MercyTidesContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MercyTidesContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Schema::hasTable('abouts')) {
            $about = About::query()->first() ?? new About();
            $about->vision = MercyTidesContent::vision();
            $about->mission = MercyTidesContent::mission();
            $about->values = '<ul>'
                . collect(MercyTidesContent::coreValues())->map(fn ($v) => '<li>' . e($v) . '</li>')->join('')
                . '</ul>';
            if (Schema::hasColumn('abouts', 'core_values_list')) {
                $about->core_values_list = implode("\n", MercyTidesContent::coreValues());
            }
            $about->save();
        }

        if (Schema::hasTable('backgrounds')) {
            $bg = Background::query()->first() ?? new Background();
            $bg->description = MercyTidesContent::overview();
            if (Schema::hasColumn('backgrounds', 'problem_statement')) {
                $bg->problem_statement = MercyTidesContent::problemStatement();
            }
            if (Schema::hasColumn('backgrounds', 'solution_statement')) {
                $bg->solution_statement = MercyTidesContent::solutionStatement();
            }
            if (Schema::hasColumn('backgrounds', 'approach_content')) {
                $bg->approach_content = MercyTidesContent::programOfferingsHtml()
                    . MercyTidesContent::holisticLeadershipHtml();
            }
            if (Schema::hasColumn('backgrounds', 'model_content')) {
                $bg->model_content = MercyTidesContent::whereWeWorkHtml();
            }
            if (Schema::hasColumn('backgrounds', 'what_we_do')) {
                $bg->what_we_do = MercyTidesContent::programOfferingsHtml();
            }
            $bg->save();
        }

        if (Schema::hasTable('teams')) {
            foreach (MercyTidesContent::leadershipTeam() as $leader) {
                $slug = Str::slug($leader['names']);
                Team::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'names' => $leader['names'],
                        'position' => $leader['position'],
                        'bio' => $leader['bio'],
                        'category' => 'Administration',
                        'display' => 'Yes',
                        'status' => 'Active',
                    ]
                );
            }
        }
    }
}
