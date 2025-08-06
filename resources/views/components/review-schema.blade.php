@props(['review'])

@push('schema-org-footer')
    @inject('schemaService', 'App\Services\SchemaOrgService')
    {!! $schemaService->generateReview($review) !!}
@endpush

<div class="bg-white rounded-lg p-8 h-full shadow-md">
    <div class="flex items-center mb-6">
        @if($review->hasMedia('main'))
        <div class="w-16 h-16 bg-cover bg-center rounded-full mr-4" style="background-image: url({{ $review->getFirstMediaUrl('main') }});"></div>
        @else
        <div class="w-16 h-16 bg-cover bg-center rounded-full mr-4" style="background-image: url({{ asset('images/user.png') }});"></div>
        @endif
        <div>
            <h4 class="font-semibold text-secondary">{{ $review->author }}</h4>
        </div>
    </div>
    <div class="text-accent leading-relaxed italic">
        {{ $review->text }}
    </div>
</div>