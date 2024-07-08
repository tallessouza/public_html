@php
    $statusArr = [
        'not-trained' => [
            'class' => 'default',
            'text' => 'Não Treinado',
        ],
        'trained' => [
            'class' => 'success',
            'text' => 'Treinado',
        ],
        'training' => [
            'class' => 'info',
            'text' => 'Treinando',
        ],
    ];
@endphp

<x-badge
    class="text-3xs"
    variant="{{ data_get($statusArr, $status . '.class') ?: 'default' }}"
>
    {{ data_get($statusArr, $status . '.text') ?: 'Waiting' }}
</x-badge>
