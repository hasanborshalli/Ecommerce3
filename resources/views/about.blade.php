@extends('layouts.app')

@section('title', 'About — ' . $siteName)
@section('meta_description', 'Learn about ' . ($siteName ?? 'Luma') . ' and our commitment to mindful, quality living.')

@section('content')

{{-- ── About Hero ────────────────────────────────────────── --}}
<section class="about-hero section-pad" style="background:var(--ink);overflow:hidden;position:relative">
    <div style="position:absolute;inset:0;background:linear-gradient(135deg,var(--ink) 0%,var(--ink-mid) 60%,#3d2f1e 100%);opacity:0.8"></div>
    <div class="container" style="position:relative;z-index:1;text-align:center">
        <div class="label-overline" style="color:var(--amber);margin-bottom:var(--sp-3)">Our Story</div>
        <h1 class="display-lg" style="color:var(--white);margin-bottom:var(--sp-4)">
            Made with Intention
        </h1>
        <p style="font-size:var(--text-lg);color:rgba(255,255,255,0.65);max-width:560px;margin:0 auto;line-height:var(--leading-relaxed)">
            {{ $settings['footer_about'] ?? 'Thoughtfully made goods for a calmer, more intentional life.' }}
        </p>
    </div>
</section>

{{-- ── Mission ───────────────────────────────────────────── --}}
<section class="section-pad">
    <div class="container-sm">
        <div class="about-mission-grid">
            <div>
                <div class="label-overline" style="margin-bottom:var(--sp-3)">Why we exist</div>
                <h2 class="display-md" style="margin-bottom:var(--sp-5)">Slow down. Live beautifully.</h2>
                <p style="color:var(--text-secondary);line-height:var(--leading-relaxed);margin-bottom:var(--sp-4)">
                    In a world of fast trends and disposable goods, {{ $siteName }} was built as an antidote.
                    We source and curate products that earn a permanent place in your home — things that age
                    with character and feel better the longer you use them.
                </p>
                <p style="color:var(--text-secondary);line-height:var(--leading-relaxed)">
                    Every piece in our catalogue is chosen because we'd live with it ourselves.
                    That's the only criteria that matters.
                </p>
            </div>
            <div style="aspect-ratio:4/5;background:var(--bg-subtle);border-radius:var(--radius-xl);overflow:hidden">
                <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--amber-light) 0%,#fde68a 100%);display:flex;align-items:center;justify-content:center">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="var(--amber)" stroke-width="1"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── Values ────────────────────────────────────────────── --}}
<section class="section-pad" style="background:var(--bg-subtle)">
    <div class="container">
        <div style="text-align:center;margin-bottom:var(--sp-12)">
            <div class="label-overline" style="margin-bottom:var(--sp-2)">What drives us</div>
            <h2 class="display-md">Our Values</h2>
        </div>
        <div class="about-values-grid">
            @php
            $values = [
                ['icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','title'=>'Uncompromising Quality','desc'=>'We test everything before it earns a place in our catalogue. If we wouldn\'t use it ourselves, it doesn\'t ship.'],
                ['icon'=>'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z','title'=>'Honest Communication','desc'=>'No hype, no inflated claims, no fake scarcity. We tell you what you need to know and let the product speak.'],
                ['icon'=>'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064','title'=>'Considered Sourcing','desc'=>'We think about where things come from. Suppliers who share our values on craftsmanship and working conditions.'],
            ];
            @endphp
            @foreach($values as $value)
            <div style="background:var(--white);padding:var(--sp-8);border-radius:var(--radius-xl);border:1px solid var(--border)">
                <div style="width:52px;height:52px;background:var(--amber-light);border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;margin-bottom:var(--sp-5)">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--amber)" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $value['icon'] }}"/>
                    </svg>
                </div>
                <h3 style="font-size:var(--text-lg);font-weight:var(--weight-bold);margin-bottom:var(--sp-3)">{{ $value['title'] }}</h3>
                <p style="color:var(--text-secondary);font-size:var(--text-sm);line-height:var(--leading-relaxed)">{{ $value['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Stats ─────────────────────────────────────────────── --}}
<section class="section-pad" style="background:var(--ink)">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--sp-8);text-align:center">
            @php
            $stats = [
                ['number'=>'2,400+','label'=>'Happy Customers'],
                ['number'=>'150+',  'label'=>'Curated Products'],
                ['number'=>'98%',   'label'=>'Satisfaction Rate'],
            ];
            @endphp
            @foreach($stats as $stat)
            <div>
                <div style="font-size:var(--text-4xl);font-weight:var(--weight-black);color:var(--amber);margin-bottom:var(--sp-2)">{{ $stat['number'] }}</div>
                <div style="font-size:var(--text-sm);color:rgba(255,255,255,0.55);text-transform:uppercase;letter-spacing:var(--tracking-wider)">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── CTA ────────────────────────────────────────────────── --}}
<section class="section-pad" style="text-align:center">
    <div class="container-sm">
        <div class="label-overline" style="margin-bottom:var(--sp-3)">Ready to start?</div>
        <h2 class="display-md" style="margin-bottom:var(--sp-5)">Discover the collection</h2>
        <p style="color:var(--text-secondary);font-size:var(--text-base);margin-bottom:var(--sp-8);max-width:440px;margin-left:auto;margin-right:auto">
            Explore thoughtfully curated products for a calmer, more considered way of living.
        </p>
        <div style="display:flex;gap:var(--sp-3);justify-content:center;flex-wrap:wrap">
            <a href="{{ route('shop') }}" class="btn btn-primary btn-lg">Shop the Collection</a>
            <a href="{{ route('contact') }}" class="btn btn-outline btn-lg">Get in Touch</a>
        </div>
    </div>
</section>

@endsection
