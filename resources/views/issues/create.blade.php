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

                    @include('issues.partials.form', ['issue' => null])

                    <div class="flex items-center gap-3">
                        <x-primary-button>{{ __('Create Issue') }}</x-primary-button>
                        <a href="{{ $selectedProject ? route('projects.show', $selectedProject) : route('projects.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
