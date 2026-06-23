<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500">
                    <a href="{{ route('projects.index') }}" class="font-medium text-indigo-700 hover:text-indigo-900">{{ __('Projects') }}</a>
                </p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-800">
                    {{ $project['name'] }}
                </h2>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('projects.edit', $project['id']) }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 16.875 4.5" />
                    </svg>
                    {{ __('Edit') }}
                </a>

                <form method="POST" action="{{ route('projects.destroy', $project['id']) }}" onsubmit="return confirm('{{ __('Delete this project?') }}')">
                    @csrf
                    @method('DELETE')

                    <x-danger-button class="gap-2 shadow-sm">
                        <svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
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
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Project Details') }}</h3>
                        <p class="mt-3 text-sm leading-6 text-gray-600">
                            {{ $project['description'] ?: __('No description yet.') }}
                        </p>
                    </div>
                </section>

                <aside class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Summary') }}</h3>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-gray-500">{{ __('Issues') }}</dt>
                                <dd class="font-semibold text-gray-900">{{ count($project['issues']) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-gray-500">{{ __('Created') }}</dt>
                                <dd class="font-semibold text-gray-900">{{ $project['created_at_label'] }}</dd>
                            </div>
                        </dl>
                    </div>
                </aside>
            </div>

            <section class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="flex flex-col gap-4 border-b border-gray-100 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Issues') }}</h3>

                    <a href="{{ route('issues.create', ['project_id' => $project['id']]) }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">
                        <svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        {{ __('Add Issue') }}
                    </a>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($project['issues'] as $issue)
                        <article class="p-6">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div class="min-w-0">
                                    <a href="{{ route('issues.show', $issue['id']) }}" class="font-semibold text-gray-900 hover:text-indigo-700">
                                        {{ $issue['title'] }}
                                    </a>
                                    <p class="mt-2 text-sm leading-6 text-gray-600">
                                        {{ $issue['description'] ?: __('No description yet.') }}
                                    </p>

                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach ($issue['tag_badges'] as $tag)
                                            <span
                                                class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium"
                                                style="background-color: {{ $tag['color'] ?? '#f3f4f6' }}1A; border-color: {{ $tag['color'] ?? '#d1d5db' }}; color: {{ $tag['color'] ?? '#374151' }};"
                                            >
                                                {{ $tag['name'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <dl class="grid shrink-0 grid-cols-2 gap-3 text-sm md:w-64">
                                    <div>
                                        <dt class="text-xs uppercase tracking-widest text-gray-500">{{ __('Status') }}</dt>
                                        <dd class="mt-1 font-medium text-gray-900">{{ $issue['status_label'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs uppercase tracking-widest text-gray-500">{{ __('Priority') }}</dt>
                                        <dd class="mt-1 font-medium text-gray-900">{{ $issue['priority_label'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs uppercase tracking-widest text-gray-500">{{ __('Due') }}</dt>
                                        <dd class="mt-1 font-medium text-gray-900">{{ $issue['due_date_label'] ?? __('None') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs uppercase tracking-widest text-gray-500">{{ __('Comments') }}</dt>
                                        <dd class="mt-1 font-medium text-gray-900">{{ $issue['comments_count'] }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </article>
                    @empty
                        <div class="p-6 text-sm text-gray-600">
                            {{ __('This project does not have any issues yet.') }}
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
