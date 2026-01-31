<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewModerationRequest;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::query()
            ->with('product', 'user')
            ->when(request()->has('approved'), fn ($q) => $q->where('approved', (bool) request('approved')))
            ->latest()
            ->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function update(ReviewModerationRequest $request, Review $review): RedirectResponse
    {
        $review->update(['approved' => $request->validated()['approved']]);

        return back()->with('toast', __('messages.review_updated'));
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('toast', __('messages.review_deleted'));
    }
}
