<div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-start">
            @foreach ($categories as $category)
                @if (!$loop->last) 
                <li class="breadcrumb-item text-nowrap"><a href="{{ route('category.products', $category->slug) }}">{{ $category->name }}</a>
                @else
                <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ $category->name }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>