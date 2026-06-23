<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-3">
                <a href="{{ route('projects.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:ring-2 hover:ring-indigo-500">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Projects') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('Organize work by client, product, or milestone.') }}</p>
                    </div>
                </a>

                <a href="{{ route('issues.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:ring-2 hover:ring-indigo-500">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Issues') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('Track status, priority, due dates, and comments.') }}</p>
                    </div>
                </a>

                <a href="{{ route('tags.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:ring-2 hover:ring-indigo-500">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Tags') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('Label issues for quick filtering and triage.') }}</p>
                    </div>
                </a>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-sm text-gray-700">
                    {{ __('Use Projects to group work, Issues to track progress, and Tags to keep triage fast.') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
