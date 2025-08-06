@props(['house', 'showSchema' => false])

@inject('schemaService', 'App\Services\SchemaOrgService')

@if($showSchema)
@push('schema-org-footer')
    {!! $schemaService->generateHouseProduct($house) !!}
@endpush
@endif

<div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg hover:-translate-y-1">
                                <a href="{{ route('house.show', $house->slug) }}">
                                    @if($house->hasMedia('main'))
                                        <img src="{{ $house->getFirstMediaUrl('main') }}" alt="{{ $house->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-400">Нет изображения</span>
                                        </div>
                                    @endif

                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $house->title }}</h3>

                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-gray-600">{{ $house->area_total }} м²</span>
                                            <span class="text-gray-600">{{ $house->floor_count }} этаж{{ $house->floor_count > 1 ? 'а' : '' }}</span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            @if($house->old_price && $house->old_price > $house->price)
                                                <div>
                                                    <span class="text-gray-400 line-through text-sm">{{ number_format($house->old_price, 0, '.', ' ') }} ₽</span>
                                                    <span class="text-gray-900 font-bold block">{{ number_format($house->price, 0, '.', ' ') }} ₽</span>
                                                </div>
                                            @else
                                                <span class="text-gray-900 font-bold">{{ number_format($house->price, 0, '.', ' ') }} ₽</span>
                                            @endif

                                            <div class="flex flex-wrap gap-1">
                                                @foreach($house->categories as $category)
                                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $category->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>