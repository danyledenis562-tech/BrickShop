<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromoCodeController extends Controller
{
    public function index(): View
    {
        $promoCodes = PromoCode::query()->latest()->paginate(15);

        return view('admin.promocodes.index', compact('promoCodes'));
    }

    public function create(): View
    {
        return view('admin.promocodes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promo_codes,code'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['code'] = strtoupper(trim($data['code']));

        PromoCode::create($data);

        return redirect()->route('admin.promo-codes.index')->with('toast', __('messages.promo_created'));
    }

    public function edit(PromoCode $promo_code): View
    {
        return view('admin.promocodes.edit', ['promoCode' => $promo_code]);
    }

    public function update(Request $request, PromoCode $promo_code): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promo_codes,code,' . $promo_code->id],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['code'] = strtoupper(trim($data['code']));

        $promo_code->update($data);

        return redirect()->route('admin.promo-codes.edit', $promo_code)->with('toast', __('messages.promo_updated'));
    }

    public function destroy(PromoCode $promo_code): RedirectResponse
    {
        $promo_code->delete();

        return back()->with('toast', __('messages.promo_deleted'));
    }
}
