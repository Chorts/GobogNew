<?php

namespace App\Http\Controllers;

use App\Models\Tenan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenanController extends Controller
{
    public function index(): View
    {
        $tenans = Tenan::withCount('transactions')
            ->orderBy('nama')
            ->get();

        return view('admin.tenan.index', compact('tenans'));
    }

    public function create(): View
    {
        return view('admin.tenan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:tenans,nama'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'nama.required' => 'Nama tenan wajib diisi.',
            'nama.unique' => 'Nama tenan sudah terdaftar.',
            'nama.max' => 'Nama tenan maksimal 100 karakter.',
        ]);

        Tenan::create($validated);

        return redirect()->route('admin.tenan.index')
            ->with('success', 'Tenan baru berhasil ditambahkan.');
    }

    public function edit(Tenan $tenan): View
    {
        return view('admin.tenan.edit', compact('tenan'));
    }

    public function update(Request $request, Tenan $tenan): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('tenans', 'nama')->ignore($tenan->id)],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'nama.required' => 'Nama tenan wajib diisi.',
            'nama.unique' => 'Nama tenan sudah terdaftar.',
            'nama.max' => 'Nama tenan maksimal 100 karakter.',
        ]);

        $tenan->update($validated);

        return redirect()->route('admin.tenan.index')
            ->with('success', 'Data tenan berhasil diperbarui.');
    }

    public function destroy(Tenan $tenan): RedirectResponse
    {
        if ($tenan->transactions()->exists()) {
            return redirect()->route('admin.tenan.index')
                ->with('error', 'Tenan tidak bisa dihapus: sudah memiliki riwayat transaksi.');
        }

        $tenan->delete();

        return redirect()->route('admin.tenan.index')
            ->with('success', 'Tenan berhasil dihapus.');
    }
}
