<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
    @if($house->getFirstMedia('main'))
        <div class="relative h-96">
            <img src="{{ $house->getFirstMedia('main')->getUrl() }}" alt="{{ $house->title }}" class="w-full h-full object-cover">
        </div>
    @endif
    
    @if($house->getMedia('gallery')->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 p-2">
            @foreach($house->getMedia('gallery') as $media)
                <div class="aspect-square overflow-hidden rounded">
                    <img src="{{ $media->getUrl() }}" alt="{{ $house->title }} - изображение {{ $loop->iteration }}" class="w-full h-full object-cover hover:opacity-90 transition-opacity cursor-pointer">
                </div>
            @endforeach
        </div>
    @endif
</div>