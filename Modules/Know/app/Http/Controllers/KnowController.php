<?php

namespace Modules\Know\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Know\Models\Know;

class KnowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $knows = Know::query()
            ->latest()
            ->get()
            ->map(fn (Know $know) => [
                'id' => $know->id,
                'nama' => $know->nama,
                'deskripsi' => $know->deskripsi,
                'link' => $know->link ?? [],
                'pic' => $know->pic,
                'tanggal_pelaksanaan' => $know->tanggal_pelaksanaan?->toDateString(),
                'kategori' => $know->kategori ?? [],
            ]);

        return Inertia::render(
            'know::KnowIndexPage',
            [
                'knows' => $knows,
            ]
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'link' => 'nullable|array',
            'link.*' => 'nullable|array',
            'link.*.nama' => 'nullable|string|max:255',
            'link.*.link' => 'nullable|string|max:1000',
            'pic' => 'nullable|string|max:255',
            'tanggal_pelaksanaan' => 'nullable|date',
            'kategori' => 'nullable|array',
            'kategori.*' => 'nullable|string|max:100',
        ]);

        $links = collect($validated['link'] ?? [])
            ->map(function ($item): ?array {
                if (! is_array($item)) {
                    return null;
                }

                $name = is_string($item['nama'] ?? null) ? trim($item['nama']) : '';
                $link = is_string($item['link'] ?? null) ? trim($item['link']) : '';

                if ($name === '' && $link === '') {
                    return null;
                }

                return [
                    'nama' => $name,
                    'link' => $link,
                ];
            })
            ->filter()
            ->values()
            ->all();

        $categories = collect($validated['kategori'] ?? [])
            ->map(fn ($item) => is_string($item) ? trim($item) : '')
            ->filter()
            ->values()
            ->all();

        Know::create([
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'link' => $links !== [] ? $links : null,
            'pic' => $validated['pic'] ?? null,
            'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'] ?? null,
            'kategori' => $categories !== [] ? $categories : null,
        ]);

        return back()->with('success', 'Knowledge berhasil ditambahkan');
    }
}
