<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
    <div class="divide-y divide-gray-100">
        @forelse ($issues as $issue)
            <article class="p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('issues.show', $issue['id']) }}" class="text-lg font-semibold text-gray-900 hover:text-indigo-700">
                                {{ $issue['title'] }}
                            </a>
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">{{ $issue['status_label'] }}</span>
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ $issue['priority_label'] }}</span>
                        </div>

                        <p class="mt-2 text-sm text-gray-500">
                            <a href="{{ route('projects.show', $issue['project']['id']) }}" class="font-medium text-indigo-700 hover:text-indigo-900">{{ $issue['project']['name'] }}</a>
                            <span aria-hidden="true">&middot;</span>
                            {{ __('Due') }} {{ $issue['due_date_label'] ?? __('None') }}
                            <span aria-hidden="true">&middot;</span>
                            {{ trans_choice(':count comment|:count comments', $issue['comments_count'], ['count' => $issue['comments_count']]) }}
                        </p>

                        <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-600">
                            {{ $issue['description'] ?: __('No description yet.') }}
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($issue['tags'] as $tag)
                                <span
                                    class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium"
                                    style="background-color: {{ $tag['color'] ?? '#f3f4f6' }}1A; border-color: {{ $tag['color'] ?? '#d1d5db' }}; color: {{ $tag['color'] ?? '#374151' }};"
                                >
                                    {{ $tag['name'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    @canany(['update', 'delete'], $issue['model'])
                        <div class="flex shrink-0 items-center gap-2">
                            @can('update', $issue['model'])
                                <a href="{{ route('issues.edit', $issue['id']) }}" class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ __('Edit') }}
                                </a>
                            @endcan

                            @can('delete', $issue['model'])
                                <form method="POST" action="{{ route('issues.destroy', $issue['id']) }}" onsubmit="return confirm('{{ __('Delete this issue?') }}')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="inline-flex items-center gap-2 rounded-md border border-red-200 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-widest text-red-700 shadow-sm transition duration-150 ease-in-out hover:border-red-300 hover:bg-red-50 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endcanany
                </div>
            </article>
        @empty
            <div class="p-6 text-sm text-gray-600">
                {{ __('No issues match the current filters.') }}
            </div>
        @endforelse
    </div>
</div>

<div class="mt-6">
    {{ $issues->links() }}
</div>
