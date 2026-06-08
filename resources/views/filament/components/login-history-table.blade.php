<div class="overflow-x-auto">
    @if ($histories->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">No login history yet.</p>
    @else
        <table class="w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="text-left text-gray-500 dark:text-gray-400">
                    <th class="py-2 pr-4 font-medium">Logged in</th>
                    <th class="py-2 pr-4 font-medium">IP address</th>
                    <th class="py-2 font-medium">User agent</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($histories as $history)
                    <tr>
                        <td class="py-2 pr-4 whitespace-nowrap">{{ $history->logged_in_at->format('M j, Y g:i A') }}</td>
                        <td class="py-2 pr-4">{{ $history->ip_address ?? '—' }}</td>
                        <td class="py-2 max-w-xs truncate" title="{{ $history->user_agent }}">{{ $history->user_agent ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
