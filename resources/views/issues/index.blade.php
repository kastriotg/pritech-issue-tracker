<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Issues') }}
            </h2>

            <a href="{{ route('issues.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">
                {{ __('New Issue') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div x-data="issueSearch({ action: @js(route('issues.index')), initialSearch: @js($filters['search'] ?? '') })">
                <form x-ref="form" method="GET" action="{{ route('issues.index') }}" class="mb-6 grid gap-4 bg-white p-6 shadow-sm sm:rounded-lg md:grid-cols-4">
                    <div class="md:col-span-4">
                        <x-input-label for="search" :value="__('Search')" />
                        <div class="relative mt-1">
                            <x-text-input
                                id="search"
                                name="search"
                                type="search"
                                class="block w-full pr-28"
                                x-model="search"
                                x-on:input="queueSearch"
                                :value="$filters['search'] ?? ''"
                                :placeholder="__('Search title or description')"
                            />
                            <div x-cloak x-show="isLoading" class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-sm text-gray-500">
                                {{ __('Searching...') }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" onchange="this.form.submit()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Any status') }}</option>
                            <option value="open" @selected(($filters['status'] ?? '') === 'open')>{{ __('Open') }}</option>
                            <option value="in_progress" @selected(($filters['status'] ?? '') === 'in_progress')>{{ __('In Progress') }}</option>
                            <option value="closed" @selected(($filters['status'] ?? '') === 'closed')>{{ __('Closed') }}</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="priority" :value="__('Priority')" />
                        <select id="priority" name="priority" onchange="this.form.submit()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Any priority') }}</option>
                            <option value="low" @selected(($filters['priority'] ?? '') === 'low')>{{ __('Low') }}</option>
                            <option value="medium" @selected(($filters['priority'] ?? '') === 'medium')>{{ __('Medium') }}</option>
                            <option value="high" @selected(($filters['priority'] ?? '') === 'high')>{{ __('High') }}</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="tag" :value="__('Tag')" />
                        <select id="tag" name="tag" onchange="this.form.submit()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Any tag') }}</option>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}" @selected((int) ($filters['tag'] ?? 0) === $tag->id)>{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-3">
                        <a href="{{ route('issues.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </form>

                <div x-ref="results">
                    @include('issues.partials.list')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
