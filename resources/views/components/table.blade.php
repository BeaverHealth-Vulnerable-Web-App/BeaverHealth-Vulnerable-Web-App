<div class="rounded-lg overflow-hidden border border-transparent dark:border-gray-700">
    <table class="table-auto w-full">
        <thead>
            <tr class="bg-gray-700 text-white">
                @foreach($headers as $header)
                    <th class="px-4 py-3 text-{{ $header['align'] ?? 'left' }} border-b border-transparent dark:border-gray-700">
                        {{ $header['text'] }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
