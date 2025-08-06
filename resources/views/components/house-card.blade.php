@props(['house', 'showSchema' => false])

@inject('schemaService', 'App\Services\SchemaOrgService')

@if($showSchema)
@push('schema-org-footer')
    {!! $schemaService->generateHouseProduct($house) !!}
@endpush
@endif

<div class="bg-white-custom rounded-xl overflow-hidden shadow-md hover:-translate-y-2 transition-transform">
    <div class="h-48 overflow-hidden">
         <a href="{{ route('house.show', $house->slug) }}">
         @if($house->hasMedia('main'))
             <img data-src="{{ $house->getFirstMediaUrl('main') }}" alt="{{ $house->title }}" class="w-full h-48 object-cover">
         @else
             <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                 <span class="text-gray-400">Нет изображения</span>
             </div>
         @endif
         </a>
     </div>
     <div class="p-6">
         <h3 class="font-evolventa text-xl text-secondary mb-4"><a href="{{ route('house.show', $house->slug) }}">{{ $house->title }}</a></h3>
         <div class="flex justify-between text-sm text-green-dark mb-6">
             <span>{{ $house->area_total }} м²</span>
             <span>Этажей: {{ $house->floor_count }}</span>
             <span>от {{ number_format($house->price, 0, '.', ' ') }} ₽</span>
         </div>
         <a href="{{ route('house.show', $house->slug) }}" class="bg-primary hover:bg-primary-dark text-white-custom px-4 py-2 rounded-lg text-sm font-medium transition-colors">
             Подробнее
         </a>
     </div>
</div>