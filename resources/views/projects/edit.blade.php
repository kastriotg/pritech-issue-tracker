<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edit Project') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6 p-6">
                    @csrf
                    @method('PATCH')

                    @include('projects.partials.form')

                    <div class="flex items-center gap-3">
                        <x-primary-button>{{ __('Save Project') }}</x-primary-button>
                        <a href="{{ route('projects.show', $project) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
