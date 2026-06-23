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

            <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(16rem,1fr)_minmax(18rem,1fr)]">
                <section
                    class="bg-white shadow-sm sm:rounded-lg"
                    x-data="issueTags({
                        initialTags: @js($issue['tags']),
                        availableTags: @js($allTags),
                        attachUrlTemplate: @js(route('issues.tags.store', ['issue' => $issue['id'], 'tag' => '__TAG__'])),
                        detachUrlTemplate: @js(route('issues.tags.destroy', ['issue' => $issue['id'], 'tag' => '__TAG__'])),
                    })"
                >
                    <div class="p-6">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">{{ __('Issue Details') }}</h3>
                        </div>

                        <p class="mt-3 text-sm leading-6 text-gray-600">
                            {{ $issue['description'] ?: __('No description yet.') }}
                        </p>

                        <div class="mt-6 flex flex-wrap gap-2">
                            <template x-for="tag in tags" :key="tag.id">
                                <button
                                    type="button"
                                    @click="detach(tag)"
                                    :disabled="isProcessing(tag)"
                                    class="group relative inline-flex h-8 min-w-[6.5rem] items-center justify-center overflow-hidden rounded-full border px-3 text-xs font-medium transition duration-150 ease-in-out [perspective:600px] hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60"
                                    :style="tagStyle(tag)"
                                    :aria-label="`Remove ${tag.name} tag`"
                                >
                                    <span class="block truncate transition duration-200 [backface-visibility:hidden] group-hover:opacity-0 group-hover:[transform:rotateX(180deg)]" x-text="tag.name"></span>
                                    <span class="absolute inset-0 flex items-center justify-center rounded-full px-3 text-red-700 opacity-0 transition duration-200 [backface-visibility:hidden] [transform:rotateX(-180deg)] group-hover:opacity-100 group-hover:[transform:rotateX(0deg)]">
                                        {{ __('Remove tag') }}
                                    </span>
                                </button>
                            </template>

                            <template x-if="tags.length === 0">
                                <span class="text-sm text-gray-500">{{ __('No tags attached.') }}</span>
                            </template>

                            <div class="relative" @click.outside="open = false">
                                <button type="button" @click="open = ! open" class="inline-flex h-8 items-center justify-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3 text-xs font-semibold text-indigo-700 transition duration-150 ease-in-out hover:border-indigo-300 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <svg class="h-3.5 w-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    {{ __('New Tag') }}
                                </button>

                                <div x-show="open" x-transition class="absolute left-0 z-10 mt-2 w-72 rounded-md border border-gray-200 bg-white p-3 shadow-lg sm:left-auto sm:right-0">
                                    <div class="flex flex-col gap-2">
                                        <p x-show="error" x-text="error" class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></p>

                                        <template x-for="tag in unattachedTags()" :key="tag.id">
                                            <button
                                                type="button"
                                                @click="attach(tag)"
                                                :disabled="isProcessing(tag)"
                                                class="flex items-center justify-between gap-3 rounded-md border border-gray-200 bg-white px-3 py-2 text-left text-sm transition hover:border-indigo-300 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60"
                                            >
                                                <span class="inline-flex min-w-0 items-center gap-2">
                                                    <span class="h-3 w-3 shrink-0 rounded-full border" :style="`background-color: ${tag.color || '#f3f4f6'}; border-color: ${tag.color || '#d1d5db'};`"></span>
                                                    <span class="truncate font-medium text-gray-800" x-text="tag.name"></span>
                                                </span>

                                                <span class="shrink-0 text-xs font-semibold uppercase tracking-widest text-indigo-700">{{ __('Add') }}</span>
                                            </button>
                                        </template>

                                        <template x-if="unattachedTags().length === 0">
                                            <p class="px-1 py-2 text-sm text-gray-600">{{ __('All tags are attached.') }}</p>
                                        </template>
                                    </div>
                                </div>
                            </div>
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

                <aside
                    class="relative bg-white shadow-sm sm:rounded-lg"
                    x-data="issueMembers({
                        initialMembers: @js($issue['members']),
                        availableUsers: @js($allUsers),
                        attachUrlTemplate: @js(route('issues.members.store', ['issue' => $issue['id'], 'user' => '__USER__'])),
                        detachUrlTemplate: @js(route('issues.members.destroy', ['issue' => $issue['id'], 'user' => '__USER__'])),
                    })"
                >
                    <div class="p-6">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('Members') }}</h3>

                            <div class="relative" @click.outside="open = false">
                                <button type="button" @click="open = ! open" class="inline-flex h-8 items-center justify-center gap-1.5 rounded-md border border-indigo-200 bg-indigo-50 px-3 text-xs font-semibold text-indigo-700 transition duration-150 ease-in-out hover:border-indigo-300 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <svg class="h-3.5 w-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    {{ __('Assign') }}
                                </button>

                                <div x-show="open" x-transition class="absolute right-0 z-30 mt-2 max-h-72 w-56 max-w-[calc(100vw-2rem)] overflow-y-auto rounded-md border border-gray-200 bg-white p-2 shadow-xl">
                                    <div class="flex flex-col gap-2">
                                        <p x-show="error" x-text="error" class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></p>

                                        <template x-for="user in unassignedUsers()" :key="user.id">
                                            <button
                                                type="button"
                                                @click="attach(user)"
                                                :disabled="isProcessing(user)"
                                                class="flex items-center justify-between gap-3 rounded-md px-3 py-2 text-left text-sm transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60"
                                            >
                                                <span class="min-w-0">
                                                    <span class="block truncate font-medium text-gray-800" x-text="user.name"></span>
                                                    <span class="block truncate text-xs text-gray-500" x-text="user.email"></span>
                                                </span>

                                                <span class="shrink-0 rounded-md bg-indigo-50 px-2 py-1 text-xs font-semibold uppercase tracking-widest text-indigo-700">{{ __('Add') }}</span>
                                            </button>
                                        </template>

                                        <template x-if="unassignedUsers().length === 0">
                                            <p class="px-1 py-2 text-sm text-gray-600">{{ __('All users are assigned.') }}</p>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-col gap-2">
                            <template x-for="member in members" :key="member.id">
                                <div class="flex items-center justify-between gap-3 rounded-md border border-gray-200 px-3 py-2">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-gray-900" x-text="member.name"></p>
                                        <p class="truncate text-xs text-gray-500" x-text="member.email"></p>
                                    </div>

                                    <button
                                        type="button"
                                        @click="detach(member)"
                                        :disabled="isProcessing(member)"
                                        class="shrink-0 text-xs font-semibold uppercase tracking-widest text-red-700 transition hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60"
                                    >
                                        {{ __('Remove') }}
                                    </button>
                                </div>
                            </template>

                            <template x-if="members.length === 0">
                                <p class="text-sm text-gray-500">{{ __('No members assigned.') }}</p>
                            </template>
                        </div>
                    </div>
                </aside>
            </div>

            <section
                class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                x-data="issueComments({
                    indexUrl: @js(route('issues.comments.index', $issue['id'])),
                    storeUrl: @js(route('issues.comments.store', $issue['id'])),
                })"
            >
                <div class="border-b border-gray-100 p-6">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Comments') }}</h3>
                </div>

                <div class="divide-y divide-gray-100">
                    <template x-for="comment in comments" :key="comment.id">
                        <article class="p-6">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                                <h4 class="font-semibold text-gray-900" x-text="comment.author_name"></h4>
                                <p class="text-xs font-medium uppercase tracking-widest text-gray-500" x-text="comment.created_at_label"></p>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-600" x-text="comment.body"></p>
                        </article>
                    </template>

                    <template x-if="! isLoading && comments.length === 0">
                        <div class="p-6 text-sm text-gray-600">
                            {{ __('No comments yet.') }}
                        </div>
                    </template>

                    <template x-if="isLoading && comments.length === 0">
                        <div class="p-6 text-sm text-gray-600">
                            {{ __('Loading comments...') }}
                        </div>
                    </template>
                </div>

                <div class="border-t border-gray-100 p-6" x-show="nextPageUrl">
                    <x-secondary-button type="button" @click="loadComments(nextPageUrl)" x-bind:disabled="isLoading">
                        <span x-show="! isLoading">{{ __('Load More') }}</span>
                        <span x-show="isLoading">{{ __('Loading...') }}</span>
                    </x-secondary-button>
                </div>

                <form class="border-t border-gray-100 p-6" @submit.prevent="submit">
                    <div>
                        <x-input-label for="body" :value="__('Comment')" />
                        <textarea
                            id="body"
                            name="body"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            x-model="form.body"
                        ></textarea>
                        <template x-if="errors.body">
                            <p class="mt-2 text-sm text-red-600" x-text="errors.body[0]"></p>
                        </template>
                    </div>

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-500">
                            {{ __('Posting as :name', ['name' => auth()->user()->name]) }}
                        </p>

                        <x-primary-button type="submit" x-bind:disabled="isSubmitting">
                            <span x-show="! isSubmitting">{{ __('Add Comment') }}</span>
                            <span x-show="isSubmitting">{{ __('Adding...') }}</span>
                        </x-primary-button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
