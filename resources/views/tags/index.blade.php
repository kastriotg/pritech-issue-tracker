<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Tags') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('tags.store') }}" class="grid gap-4 p-6 md:grid-cols-[minmax(0,1fr)_12rem_auto] md:items-start">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="color" :value="__('Color')" />
                        <x-text-input id="color" name="color" type="text" class="mt-1 block w-full" :value="old('color')" placeholder="#4F46E5" />
                        <x-input-error class="mt-2" :messages="$errors->get('color')" />
                    </div>

                    <div class="flex md:pt-6">
                        <x-primary-button>
                            {{ __('Create Tag') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="divide-y divide-gray-100">
                    @forelse ($tags as $tag)
                        <article class="p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="h-10 w-10 shrink-0 rounded-full border"
                                        style="background-color: {{ $tag['color'] ?? '#f3f4f6' }}; border-color: {{ $tag['color'] ?? '#d1d5db' }};"
                                        aria-hidden="true"
                                    ></span>

                                    <div class="min-w-0">
                                        <h3 class="truncate text-lg font-semibold text-gray-900">
                                            {{ $tag['name'] }}
                                        </h3>

                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ $tag['color'] ?? __('No color') }}
                                            <span aria-hidden="true">&middot;</span>
                                            {{ __('Created') }} {{ $tag['created_at_label'] }}
                                        </p>
                                    </div>
                                </div>

                                <div class="shrink-0">
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                        {{ trans_choice(':count issue|:count issues', $tag['issues_count'], ['count' => $tag['issues_count']]) }}
                                    </span>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="p-6 text-sm text-gray-600">
                            {{ __('No tags yet. Create your first tag to organize issues.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                {{ $tags->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
