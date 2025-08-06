@if(count($similarHouses) > 0)
    @inject('schemaService', 'App\Services\SchemaOrgService')
    
    @push('schema-org-footer')
        {!! $schemaService->generateItemList($similarHouses, 'Похожие дома', url()->current()) !!}
    @endpush
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Похожие дома</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($similarHouses as $index => $house)
                <div>
            <div>
                        <x-house-card :house="$house" :showSchema="true" />
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif