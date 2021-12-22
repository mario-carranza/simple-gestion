{{-- @todo Optimizar el codigo para no generar tantas consultas la base de datos al buscar el numero
    de productos de una categoria
 --}}

@foreach ($categories as $category)
@if ($category->getProductCount())
<option value="{{ route('category.products', $category->slug) }}">{{ str_repeat('>', $level * 2) }} {{ $category->name }}</option>
    @if ($category->children->count() && $category->getProductCount(true))
        @include('livewire.categories-menu-mobil-partial', ['categories' => $category->children, 'level' => $level + 1])
    @endif
@endif
@endforeach