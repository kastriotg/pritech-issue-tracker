<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500">
                <a href="{{ route('issues.show', $issue) }}" class="font-medium text-indigo-700 hover:text-indigo-900">{{ $issue->title }}</a>
            </p>
            <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-800">
                {{ __('Edit Issue') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('issues.update', $issue) }}" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')

                    @include('issues.partials.form')

                    <div class="flex items-center gap-3">
                        <x-primary-button>{{ __('Update Issue') }}</x-primary-button>
                        <a href="{{ route('issues.show', $issue) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
