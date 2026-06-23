<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500">
                    <a href="{{ route('projects.show', $issue['project']['id']) }}" class="font-medium text-indigo-700 hover:text-indigo-900">{{ $issue['project']['name'] }}</a>
                </p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-800">
                    {{ $issue['title'] }}
                </h2>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('issues.edit', $issue['id']) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{ __('Edit') }}
                </a>

                <form method="POST" action="{{ route('issues.destroy', $issue['id']) }}" onsubmit="return confirm('{{ __('Delete this issue?') }}')">
                    @csrf
                    @method('DELETE')

                    <x-danger-button class="shadow-sm">
                        {{ __('Delete') }}
                    </x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(18rem,1fr)]">
                <section class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Issue Details') }}</h3>
                        <p class="mt-3 text-sm leading-6 text-gray-600">
                            {{ $issue['description'] ?: __('No description yet.') }}
                        </p>

                        <div class="mt-6 flex flex-wrap gap-2">
                            @forelse ($issue['tags'] as $tag)
                                <span
                                    class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium"
                                    style="background-color: {{ $tag['color'] ?? '#f3f4f6' }}1A; border-color: {{ $tag['color'] ?? '#d1d5db' }}; color: {{ $tag['color'] ?? '#374151' }};"
                                >
                                    {{ $tag['name'] }}
                                </span>
                            @empty
                                <span class="text-sm text-gray-500">{{ __('No tags attached.') }}</span>
                            @endforelse
                        </div>
                    </div>
                </section>

                <aside class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Summary') }}</h3>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-gray-500">{{ __('Status') }}</dt>
                                <dd class="font-semibold text-gray-900">{{ $issue['status_label'] }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-gray-500">{{ __('Priority') }}</dt>
                                <dd class="font-semibold text-gray-900">{{ $issue['priority_label'] }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-gray-500">{{ __('Due') }}</dt>
                                <dd class="font-semibold text-gray-900">{{ $issue['due_date_label'] ?? __('None') }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-gray-500">{{ __('Comments') }}</dt>
                                <dd class="font-semibold text-gray-900">{{ $issue['comments_count'] }}</dd>
                            </div>
                        </dl>
                    </div>
                </aside>
            </div>

            <section class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-100 p-6">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Comments') }}</h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($comments as $comment)
                        <article class="p-6">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                                <h4 class="font-semibold text-gray-900">{{ $comment->author_name }}</h4>
                                <p class="text-xs font-medium uppercase tracking-widest text-gray-500">{{ $comment->created_at->format('M j, Y') }}</p>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-gray-600">{{ $comment->body }}</p>
                        </article>
                    @empty
                        <div class="p-6 text-sm text-gray-600">
                            {{ __('No comments yet.') }}
                        </div>
                    @endforelse
                </div>
            </section>

            <div class="mt-6">
                {{ $comments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
