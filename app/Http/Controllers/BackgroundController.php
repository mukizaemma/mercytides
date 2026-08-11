<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\About;
use App\Models\Background;

class BackgroundController extends Controller
{
    public function background(){
        $data = background::first();
        if($data===null)
        {
            $data = new background();
            $data->description = 'Our Background';
            $data->save();
            $data = background::first();
        }

        return view('admin.background', ['data'=>$data]);
    }

public function saveBackg(Request $request)
{
    $request->validate(array_merge([
        'description' => 'nullable|string',
        'donations' => 'nullable|string',
        'get_involved_intro' => 'nullable|string',
        'approach_content' => 'nullable|string',
        'model_content' => 'nullable|string',
        'problem_statement' => 'nullable|string',
        'solution_statement' => 'nullable|string',
        'what_we_do' => 'nullable|string',
        'how_it_works' => 'nullable|string',
        'expertise_content' => 'nullable|string',
        'manufacturing_impact_content' => 'nullable|string',
        'products_intro' => 'nullable|string',
        'factory_description' => 'nullable|string',
        'factory_services' => 'nullable|string',
        'factory_community_impact' => 'nullable|string',
        'factory_training_facilities' => 'nullable|string',
        'factory_services_subitems' => 'nullable|string',
        'factory_community_impact_subitems' => 'nullable|string',
        'factory_training_facilities_subitems' => 'nullable|string',
        'families_impacted' => 'nullable|string|max:255',
        'jobs_created' => 'nullable|string|max:255',
        'training_hours' => 'nullable|string|max:255',
    ], $this->imageInputRules('image'), $this->imageInputRules('image1'), $this->imageInputRules('image2'), $this->imageInputRules('core_values_background'), $this->imageInputRules('model_image'), $this->imageInputRules('factory_services_image'), $this->imageInputRules('factory_community_impact_image'), $this->imageInputRules('factory_training_facilities_image')));

    $data = Background::firstOrEmpty();
    if ($request->has('description')) {
        $data->description = $request->input('description');
    }
    if ($request->has('donations')) {
        $data->donations = $request->input('donations');
    }
    if (Schema::hasColumn('backgrounds', 'get_involved_intro') && $request->has('get_involved_intro')) {
        $data->get_involved_intro = $request->input('get_involved_intro');
    }
    if (Schema::hasColumn('backgrounds', 'approach_content') && $request->has('approach_content')) {
        $data->approach_content = $request->input('approach_content');
    }
    if (Schema::hasColumn('backgrounds', 'model_content') && $request->has('model_content')) {
        $data->model_content = $request->input('model_content');
    }
    if (Schema::hasColumn('backgrounds', 'problem_statement') && $request->has('problem_statement')) {
        $data->problem_statement = $request->input('problem_statement');
    }
    if (Schema::hasColumn('backgrounds', 'solution_statement') && $request->has('solution_statement')) {
        $data->solution_statement = $request->input('solution_statement');
    }
    if (Schema::hasColumn('backgrounds', 'what_we_do') && $request->has('what_we_do')) {
        $data->what_we_do = $request->input('what_we_do');
    }
    if (Schema::hasColumn('backgrounds', 'how_it_works') && $request->has('how_it_works')) {
        $data->how_it_works = $request->input('how_it_works');
    }
    if (Schema::hasColumn('backgrounds', 'expertise_content') && $request->has('expertise_content')) {
        $data->expertise_content = $request->input('expertise_content');
    }
    if (Schema::hasColumn('backgrounds', 'manufacturing_impact_content') && $request->has('manufacturing_impact_content')) {
        $data->manufacturing_impact_content = $request->input('manufacturing_impact_content');
    }
    if (Schema::hasColumn('backgrounds', 'products_intro') && $request->has('products_intro')) {
        $data->products_intro = $request->input('products_intro');
    }
    if (Schema::hasColumn('backgrounds', 'factory_description') && $request->has('factory_description')) {
        $data->factory_description = $request->input('factory_description');
    }
    if (Schema::hasColumn('backgrounds', 'factory_services') && $request->has('factory_services')) {
        $data->factory_services = $request->input('factory_services');
    }
    if (Schema::hasColumn('backgrounds', 'factory_community_impact') && $request->has('factory_community_impact')) {
        $data->factory_community_impact = $request->input('factory_community_impact');
    }
    if (Schema::hasColumn('backgrounds', 'factory_training_facilities') && $request->has('factory_training_facilities')) {
        $data->factory_training_facilities = $request->input('factory_training_facilities');
    }
    if (Schema::hasColumn('backgrounds', 'factory_services_subitems') && $request->has('factory_services_subitems')) {
        $data->factory_services_subitems = $request->input('factory_services_subitems');
    }
    if (Schema::hasColumn('backgrounds', 'factory_community_impact_subitems') && $request->has('factory_community_impact_subitems')) {
        $data->factory_community_impact_subitems = $request->input('factory_community_impact_subitems');
    }
    if (Schema::hasColumn('backgrounds', 'factory_training_facilities_subitems') && $request->has('factory_training_facilities_subitems')) {
        $data->factory_training_facilities_subitems = $request->input('factory_training_facilities_subitems');
    }
    if (Schema::hasColumn('backgrounds', 'families_impacted') && $request->has('families_impacted')) {
        $data->families_impacted = $request->input('families_impacted');
    }
    if (Schema::hasColumn('backgrounds', 'jobs_created') && $request->has('jobs_created')) {
        $data->jobs_created = $request->input('jobs_created');
    }
    if (Schema::hasColumn('backgrounds', 'training_hours') && $request->has('training_hours')) {
        $data->training_hours = $request->input('training_hours');
    }

    $this->assignBackgroundImage($request, $data, 'image', ['preset' => 'hero']);
    $this->assignBackgroundImage($request, $data, 'image1');
    $this->assignBackgroundImage($request, $data, 'image2');
    if (Schema::hasColumn('backgrounds', 'core_values_background')) {
        $this->assignBackgroundImage($request, $data, 'core_values_background', ['preset' => 'hero']);
    }
    if (Schema::hasColumn('backgrounds', 'model_image')) {
        $this->assignBackgroundImage($request, $data, 'model_image');
    }
    if (Schema::hasColumn('backgrounds', 'factory_services_image')) {
        $this->assignBackgroundImage($request, $data, 'factory_services_image');
    }
    if (Schema::hasColumn('backgrounds', 'factory_community_impact_image')) {
        $this->assignBackgroundImage($request, $data, 'factory_community_impact_image');
    }
    if (Schema::hasColumn('backgrounds', 'factory_training_facilities_image')) {
        $this->assignBackgroundImage($request, $data, 'factory_training_facilities_image');
    }

    $data->save();

    return redirect()->back()->with('success', 'Background has been updated successfully');
}

    public function homePage()
    {
        $data = About::firstOrEmpty();
        if (! $data->exists) {
            $data->vision = 'Alleviate poverty among single-teen mothers in Rutsiro District by providing tailoring trainings';
            $data->save();
            $data = About::firstOrEmpty();
        }

        return view('admin.homePage', ['data' => $data]);
    }

    public function saveHom(Request $request)
    {
        $request->validate(array_merge([
            'welcomeNote' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'values' => ['nullable', 'string'],
        ], $this->imageInputRules('aboutImage'), $this->imageInputRules('back1'), $this->imageInputRules('back2')));

        $data = About::firstOrEmpty();
        if (Schema::hasColumn('abouts', 'welcomeNote') && $request->has('welcomeNote')) {
            $data->welcomeNote = $request->input('welcomeNote');
        }
        if ($request->has('mission')) {
            $data->mission = $request->input('mission');
        }
        if ($request->has('vision')) {
            $data->vision = $request->input('vision');
        }
        if ($request->has('values')) {
            $data->values = $request->input('values');
        }

        if (Schema::hasColumn('abouts', 'aboutImage')) {
            $this->assignModelImage($request, $data, 'aboutImage', 'images');
        }
        if (Schema::hasColumn('abouts', 'back1')) {
            $this->assignModelImage($request, $data, 'back1', 'images');
        }
        if (Schema::hasColumn('abouts', 'back2')) {
            $this->assignModelImage($request, $data, 'back2', 'images');
        }

        $data->save();

        return redirect()->back()->with('success', 'Home page has been updated successfully');
    }

    private function assignBackgroundImage(Request $request, Background $data, string $field, array $options = []): void
    {
        $this->assignModelImage($request, $data, $field, 'images', $options);
    }

    private function assignModelImage(Request $request, object $data, string $field, string $directory, array $options = []): void
    {
        $path = $this->imageFromRequest($request, $field, $directory, $options);
        if (! $path) {
            return;
        }

        $existing = (string) ($data->{$field} ?? '');
        if ($existing !== '' && $existing !== $path) {
            $normalized = ltrim(str_replace('\\', '/', $existing), '/');
            $candidates = [$normalized];
            if (! str_contains($normalized, '/')) {
                $candidates[] = $directory.'/'.$normalized;
            }
            foreach ($candidates as $relative) {
                if (Storage::disk('public')->exists($relative)) {
                    Storage::disk('public')->delete($relative);
                }
            }
        }

        $data->{$field} = $path;
    }

}
