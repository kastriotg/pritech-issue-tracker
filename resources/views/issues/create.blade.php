<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500">
                @if ($selectedProject)
                    <a href="{{ route('projects.show', $selectedProject) }}" class="font-medium text-indigo-700 hover:text-indigo-900">{{ $selectedProject->name }}</a>
                @else
                    <a href="{{ route('projects.index') }}" class="font-medium text-indigo-700 hover:text-indigo-900">{{ __('Projects') }}</a>
                @endif
            </p>
            <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-800">
                {{ __('New Issue') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('issues.store') }}" class="space-y-6 p-6">
                    @csrf

                    <div>
                        <x-input-label for="project_id" :value="__('Project')" />

                        @if ($selectedProject)
                            <input type="hidden" name="project_id" value="{{ $selectedProject->id }}">
                            <div class="mt-1 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-800">
                                {{ $selectedProject->name }}
                            </div>
                        @else
                            <select id="project_id" name="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">{{ __('Choose a project') }}</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" @selected((int) old('project_id') === $project->id)>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        <x-input-error class="mt-2" :messages="$errors->get('project_id')" />
                    </div>

                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="open" @selected(old('status', 'open') === 'open')>{{ __('Open') }}</option>
                                <option value="in_progress" @selected(old('status') === 'in_progress')>{{ __('In Progress') }}</option>
                                <option value="closed" @selected(old('status') === 'closed')>{{ __('Closed') }}</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div>
                            <x-input-label for="priority" :value="__('Priority')" />
                            <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="low" @selected(old('priority') === 'low')>{{ __('Low') }}</option>
                                <option value="medium" @selected(old('priority', 'medium') === 'medium')>{{ __('Medium') }}</option>
                                <option value="high" @selected(old('priority') === 'high')>{{ __('High') }}</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('priority')" />
                        </div>

                        <div>
                            <x-input-label for="due_date" :value="__('Due Date')" />
                            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date')" />
                            <x-input-error class="mt-2" :messages="$errors->get('due_date')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label :value="__('Tags')" />

                        <div class="mt-2 flex flex-wrap gap-2">
                            @forelse ($tags as $tag)
                                <label class="inline-flex cursor-pointer items-center gap-2 rounded-full border px-3 py-2 text-sm font-medium transition hover:bg-gray-50">
                                    <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(in_array($tag->id, old('tag_ids', [])))>
                                    <span
                                        class="h-3 w-3 rounded-full"
                                        style="background-color: {{ $tag->color ?? '#9ca3af' }};"
                                    ></span>
                                    <span>{{ $tag->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-600">{{ __('No tags available yet.') }}</p>
                            @endforelse
                        </div>

                        <x-input-error class="mt-2" :messages="$errors->get('tag_ids')" />
                        <x-input-error class="mt-2" :messages="$errors->get('tag_ids.*')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>{{ __('Create Issue') }}</x-primary-button>
                        <a href="{{ $selectedProject ? route('projects.show', $selectedProject) : route('projects.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
