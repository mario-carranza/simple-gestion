{{-- @todo Optimizar el codigo para no generar tantas consultas la base de datos al buscar el numero
    de productos de una categoria
 --}}

@foreach ($categories as $category)
@if ($category->getProductCount())
<li class="dropdown">
    <a 
        class="dropdown-item @if ($category->getProductCount(true)) dropdown-toggle @endif" 
        href="{{ route('category.products', $category->slug) }}"
    >
        <i class="{{ $category->icon }} opacity-60 font-size-lg mt-n1 mr-2"></i> {{ $category->name }}
    </a>

    @if ($category->children->count() && $category->getProductCount(true))
    <ul class="dropdown-menu">
        @include('livewire.categories-menu-partial', ['categories' => $category->children])
    </ul>
    @endif
</li>
@endif
@endforeach