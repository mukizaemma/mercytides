<?php

namespace App\Http\Controllers;

use App\Models\Background;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class FactoryAdminController extends Controller
{
    private function backgroundRow(): Background
    {
        return Background::firstOrEmpty();
    }

    public function overview()
    {
        return view('admin.factory.edit', [
            'background' => $this->backgroundRow(),
            'section' => 'overview',
        ]);
    }

    public function services()
    {
        return view('admin.factory.edit', [
            'background' => $this->backgroundRow(),
            'section' => 'services',
        ]);
    }

    public function impact()
    {
        return view('admin.factory.edit', [
            'background' => $this->backgroundRow(),
            'section' => 'impact',
        ]);
    }

    public function training()
    {
        return view('admin.factory.edit', [
            'background' => $this->backgroundRow(),
            'section' => 'training',
        ]);
    }

    public function save(Request $request, string $section)
    {
        $allowed = ['overview', 'services', 'impact', 'training'];
        if (! in_array($section, $allowed, true)) {
            abort(404);
        }

        $bg = $this->backgroundRow();

        if ($section === 'overview') {
            $data = $request->validate([
                'factory_description' => ['nullable', 'string'],
            ]);

            if (Schema::hasColumn('backgrounds', 'factory_description')) {
                $bg->factory_description = $data['factory_description'] ?? null;
            }
        }

        if ($section === 'services') {
            $data = $request->validate([
                'factory_services' => ['nullable', 'string'],
                'factory_services_subitems' => ['nullable', 'string'],
                ...$this->imageInputRules('factory_services_image'),
            ]);

            if (Schema::hasColumn('backgrounds', 'factory_services')) {
                $bg->factory_services = $data['factory_services'] ?? null;
            }
            if (Schema::hasColumn('backgrounds', 'factory_services_subitems')) {
                $bg->factory_services_subitems = $data['factory_services_subitems'] ?? null;
            }
            $this->replaceImageIfUploaded($request, $bg, 'factory_services_image', 'factory_services_');
        }

        if ($section === 'impact') {
            $data = $request->validate([
                'factory_community_impact' => ['nullable', 'string'],
                'factory_community_impact_subitems' => ['nullable', 'string'],
                ...$this->imageInputRules('factory_community_impact_image'),
            ]);

            if (Schema::hasColumn('backgrounds', 'factory_community_impact')) {
                $bg->factory_community_impact = $data['factory_community_impact'] ?? null;
            }
            if (Schema::hasColumn('backgrounds', 'factory_community_impact_subitems')) {
                $bg->factory_community_impact_subitems = $data['factory_community_impact_subitems'] ?? null;
            }
            $this->replaceImageIfUploaded($request, $bg, 'factory_community_impact_image', 'factory_impact_');
        }

        if ($section === 'training') {
            $data = $request->validate([
                'factory_training_facilities' => ['nullable', 'string'],
                'factory_training_facilities_subitems' => ['nullable', 'string'],
                ...$this->imageInputRules('factory_training_facilities_image'),
            ]);

            if (Schema::hasColumn('backgrounds', 'factory_training_facilities')) {
                $bg->factory_training_facilities = $data['factory_training_facilities'] ?? null;
            }
            if (Schema::hasColumn('backgrounds', 'factory_training_facilities_subitems')) {
                $bg->factory_training_facilities_subitems = $data['factory_training_facilities_subitems'] ?? null;
            }
            $this->replaceImageIfUploaded($request, $bg, 'factory_training_facilities_image', 'factory_training_');
        }

        $bg->save();

        return redirect()->route('factory.admin.' . $section)->with('success', 'Factory section updated successfully.');
    }

    private function replaceImageIfUploaded(Request $request, Background $bg, string $field, string $prefix): void
    {
        if (! Schema::hasColumn('backgrounds', $field)) {
            return;
        }

        $path = $this->imageFromRequest($request, $field, 'images');
        if (! $path) {
            return;
        }

        $existing = (string) ($bg->{$field} ?? '');
        if ($existing !== '' && $existing !== $path) {
            $normalized = ltrim(str_replace('\\', '/', $existing), '/');
            $candidates = [$normalized];
            if (! str_contains($normalized, '/')) {
                $candidates[] = 'images/'.$normalized;
            }
            foreach ($candidates as $relative) {
                if (Storage::disk('public')->exists($relative)) {
                    Storage::disk('public')->delete($relative);
                }
            }
        }

        $bg->{$field} = $path;
    }
}
