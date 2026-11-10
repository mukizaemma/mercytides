<?php

namespace App\Http\Controllers;

use App\Support\SiteContent;
use App\Support\SiteCopyCatalog;
use Illuminate\Http\Request;

class SiteCopyController extends Controller
{
    public function index()
    {
        return view('admin.site-copy', [
            'groups' => SiteCopyCatalog::groups(),
            'active' => $this->activeGroup(request('tab')),
            'ready' => SiteContent::tableReady(),
            'objectives' => SiteContent::objectives(),
        ]);
    }

    public function update(Request $request)
    {
        if (! SiteContent::tableReady()) {
            return redirect()
                ->route('siteCopy.index')
                ->with('error', 'Page copy could not be saved because the database is not available.');
        }

        $validated = $request->validate([
            'group' => ['required', 'string', 'max:40'],
            'fields' => ['nullable', 'array'],
            'fields.*.key' => ['nullable', 'string', 'max:120'],
            'fields.*.value' => ['nullable', 'string', 'max:20000'],
            'objectives' => ['nullable', 'array', 'max:24'],
            'objectives.*.text' => ['nullable', 'string', 'max:500'],
            'objectives.*.icon' => ['nullable', 'string', 'max:64'],
        ]);

        $group = $this->activeGroup($validated['group'] ?? null);
        $allowed = array_flip(SiteCopyCatalog::keysFor($group));
        $pairs = [];

        foreach ($validated['fields'] ?? [] as $row) {
            $key = (string) ($row['key'] ?? '');
            if ($key === '' || ! isset($allowed[$key])) {
                continue;
            }
            $pairs[$key] = (string) ($row['value'] ?? '');
        }

        if ($group === 'objectives') {
            $items = SiteContent::normalizeObjectives($validated['objectives'] ?? []);
            if ($items === []) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Add at least one objective before saving.');
            }
            $pairs['objectives.items'] = json_encode($items, JSON_UNESCAPED_UNICODE);
        }

        SiteContent::putMany($pairs);

        return redirect()
            ->route('siteCopy.index', ['tab' => $group])
            ->with('success', 'Page copy has been updated.');
    }

    private function activeGroup(?string $tab): string
    {
        $tab = (string) $tab;

        return SiteCopyCatalog::has($tab) ? $tab : 'objectives';
    }
}
