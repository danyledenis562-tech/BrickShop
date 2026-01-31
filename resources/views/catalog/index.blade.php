<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10">
        <section class="lego-card p-8">
            <h1 class="text-4xl font-extrabold">{{ __('messages.catalog') }}</h1>
            <p class="mt-2 text-sm text-[color:var(--muted)]">{{ __('messages.catalog_subtitle') }}</p>

            <form method="GET" class="mt-6 grid gap-4 md:grid-cols-6">
                <input name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}" class="lego-input md:col-span-2">
                <select name="category" class="lego-input">
                    <option value="">{{ __('messages.all_categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <input name="age" type="number" value="{{ request('age') }}" placeholder="{{ __('messages.age') }}" class="lego-input">
                <select name="difficulty" class="lego-input">
                    <option value="">{{ __('messages.difficulty') }}</option>
                    @foreach (['easy', 'medium', 'hard'] as $level)
                        <option value="{{ $level }}" @selected(request('difficulty') === $level)>{{ __('messages.'.$level) }}</option>
                    @endforeach
                </select>
                <input name="min_price" type="number" value="{{ request('min_price') }}" placeholder="{{ __('messages.min_price') }}" class="lego-input">
                <input name="max_price" type="number" value="{{ request('max_price') }}" placeholder="{{ __('messages.max_price') }}" class="lego-input">
                <select name="sort" class="lego-input">
                    <option value="">{{ __('messages.sort') }}</option>
                    <option value="popular" @selected(request('sort') === 'popular')>{{ __('messages.sort_popular') }}</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>{{ __('messages.sort_price_asc') }}</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>{{ __('messages.sort_price_desc') }}</option>
                </select>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="in_stock" value="1" @checked(request('in_stock'))>
                    {{ __('messages.in_stock') }}
                </label>
                <button class="lego-btn lego-btn-primary md:col-span-2">{{ __('messages.apply') }}</button>
            </form>
        </section>

        @if ($bannerTop)
            @php
                $bannerImage = $bannerTop->image
                    ? (\Illuminate\Support\Str::startsWith($bannerTop->image, ['http://', 'https://'])
                        ? $bannerTop->image
                        : asset('storage/'.$bannerTop->image))
                    : null;
            @endphp
            <section class="mt-10">
                <a href="{{ $bannerTop->link_url }}" class="lego-poster lego-pattern block">
                    <div class="relative z-10 grid gap-6 md:grid-cols-[1.2fr,0.8fr] md:items-center">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-white/80">{{ __('messages.catalog_highlight') }}</p>
                            <h2 class="mt-2 text-3xl font-extrabold">{{ $bannerTop->title }}</h2>
                            <p class="mt-2 text-sm text-white/90">{{ $bannerTop->subtitle }}</p>
                            <span class="mt-4 inline-flex lego-btn lego-btn-secondary text-xs">{{ __('messages.shop_now') }}</span>
                        </div>
                        @if ($bannerImage)
                            <div class="lego-poster-image p-3">
                                <img src="{{ $bannerImage }}" alt="{{ $bannerTop->title }}" class="h-44 w-full rounded-2xl object-cover">
                            </div>
                        @endif
                    </div>
                </a>
            </section>
        @endif

        <section class="lego-section">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.no_products') }}</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
